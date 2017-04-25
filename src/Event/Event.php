<?php

namespace Olegf13\Jivochat\Webhooks\Event;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;
use Olegf13\Jivochat\Webhooks\Request\Page;
use Olegf13\Jivochat\Webhooks\Request\Session;
use Olegf13\Jivochat\Webhooks\Request\Visitor;

/**
 * Class Event
 *
 * @package Olegf13\Jivochat\Webhooks\Event
 */
abstract class Event
{
    use PopulateObjectViaArray;

    /**
     * Event will be sent when agent clicks 'Reply'.
     *
     * All known data about visitor and some agent's info will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * If response to `chat_accepted` contains contact_info,
     * this data will be displayed to the agent as if a visitor introduced in the chat window.
     * It's also will be saved in the archive and email with the chat log.
     */
    const EVENT_CHAT_ACCEPTED = 'chat_accepted';

    /**
     * Event will be sent when a chat connects to CRM using the parameter "crm_link" from reply on Chat Accepted.
     *
     * All known data about visitor and some agent's info will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * In response we expect only `{"result": "ok or an error message"}`.
     */
    const EVENT_CHAT_ASSIGNED = 'chat_assigned';

    /**
     * Event will be sent when a chat is closed in the agent application.
     *
     * All known data about visitor, agent's info and the chat log will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * In response we expect only `{"result": "ok or an error message"}`.
     */
    const EVENT_CHAT_FINISHED = 'chat_finished';

    /**
     * Event will be sent when a visitor's information has been updated
     * (for example a visitor filled the contacts form in the chat).
     *
     * All known data about visitor and agent's info will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * In response we expect only `{"result": "ok or an error message"}`.
     */
    const EVENT_CHAT_UPDATED = 'chat_updated';

    /**
     * Event will be sent when a visitor sends an offline message through the chat offline form.
     *
     * All known data about visitor and offline message will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * In response we expect only `{"result": "ok or an error message"}`.
     */
    const EVENT_OFFLINE_MESSAGE = 'offline_message';

    /** Available events list. */
    const EVENTS = [
        self::EVENT_CHAT_ACCEPTED,
        self::EVENT_CHAT_ASSIGNED,
        self::EVENT_CHAT_FINISHED,
        self::EVENT_CHAT_UPDATED,
        self::EVENT_OFFLINE_MESSAGE,
    ];

    /** @var string Type of event (e.g. "chat_accepted"). See {@link EventListener::EVENTS} for available values. */
    public $event_name;
    /** @var string Channel widget ID, it can be found in the chat code (e.g. "2669"). */
    public $widget_id;
    /** @var Visitor Object with information about the visitor. See {@link Visitor} for details. */
    public $visitor;
    /** @var Session Information on user sessions. See {@link Session} for details. */
    public $session;
    /** @var string|null Visitor id (e.g. "3c077929b8_12175"). */
    public $user_token;
    /** @var Page Information about a page on which the visitor. See {@link Page} for details. */
    public $page;

    /**
     * Event constructor.
     *
     * @param array $requestData
     */
    final protected function __construct(array $requestData)
    {
        $this->populate($requestData);
    }

    /**
     * Creates object of concrete Event class and populates its properties via given Webhook request data.
     *
     * @param string $requestJSON Webhook request JSON string.
     * @return Event Returns created object of concrete event (see {@link Event::EVENTS}).
     * @throws \InvalidArgumentException when couldn't decode request string or couldn't detect concrete event type.
     * @throws \LogicException in case when got unknown event name or class for given event name is not implemented.
     */
    final public static function create(string $requestJSON): Event
    {
        /** @var array Decoded Webhook request data array (assoc). */
        $decodedRequest = json_decode($requestJSON, true);
        if (null === $decodedRequest) {
            $errorCode = json_last_error();
            $error = json_last_error_msg();

            throw new \InvalidArgumentException("Couldn't decode request string, error ({$errorCode}): `{$error}`.");
        }

        /** @var string Event name. */
        $eventName = $decodedRequest['event_name'] ?: null;
        if (null === $eventName) {
            throw new \InvalidArgumentException("Request doesn't contain `event_name` field (not a Jivochat Webhook?).");
        }

        if (!in_array($eventName, static::EVENTS, true)) {
            throw new \LogicException("Got unknown event name from Webhook request (`{$eventName}`).");
        }

        switch ($eventName) {
            case static::EVENT_CHAT_ACCEPTED:
                return new ChatAccepted($decodedRequest);
                break;
            case static::EVENT_CHAT_ASSIGNED:
                return new ChatAssigned($decodedRequest);
                break;
            case static::EVENT_CHAT_FINISHED:
                return new ChatFinished($decodedRequest);
                break;
            case static::EVENT_CHAT_UPDATED:
                return new ChatUpdated($decodedRequest);
                break;
            case static::EVENT_OFFLINE_MESSAGE:
                return new OfflineMessage($decodedRequest);
                break;
            default:
                throw new \LogicException("Class for event name `{$eventName}` is not implemented.");
                break;
        }
    }

    /**
     * Setter for {@link visitor} property.
     *
     * @param Visitor|array $data
     * @throws \InvalidArgumentException
     */
    public function setVisitor($data)
    {
        if (is_array($data)) {
            $visitor = new Visitor();
            $visitor->populate($data);
            $this->visitor = $visitor;
            return;
        }

        if ($data instanceof Visitor) {
            $this->visitor = $data;
            return;
        }

        throw new \InvalidArgumentException('Invalid data given.');
    }

    /**
     * Setter for {@link session} property.
     *
     * @param Visitor|array $data
     * @throws \InvalidArgumentException
     */
    public function setSession($data)
    {
        if (is_array($data)) {
            $obj = new Session();
            $obj->populate($data);
            $this->session = $obj;
            return;
        }

        if ($data instanceof Session) {
            $this->session = $data;
            return;
        }

        throw new \InvalidArgumentException('Invalid data given.');
    }

    /**
     * Setter for {@link page} property.
     *
     * @param Visitor|array $data
     * @throws \InvalidArgumentException
     */
    public function setPage($data)
    {
        if (is_array($data)) {
            $obj = new Page();
            $obj->populate($data);
            $this->page = $obj;
            return;
        }

        if ($data instanceof Page) {
            $this->page = $data;
            return;
        }

        throw new \InvalidArgumentException('Invalid data given.');
    }
}
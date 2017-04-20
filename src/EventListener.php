<?php

namespace Jivochat\Webhooks;

/**
 * Class EventListener
 *
 * @package Jivochat\Webhook
 */
class EventListener
{
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
     * In response we expect only {"result": "ok or an error message"}
     */
    const EVENT_CHAT_FINISHED = 'chat_finished';

    /**
     * Event will be sent when a visitor's information has been updated
     * (for example a visitor filled the contacts form in the chat).
     *
     * All known data about visitor and agent's info will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * In response we expect only {"result": "ok or an error message"}
     */
    const EVENT_CHAT_UPDATED = 'chat_updated';

    /**
     * Event will be sent when a visitor sends an offline message through the chat offline form.
     *
     * All known data about visitor and offline message will be sent in the request parameters.
     * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
     *
     * In response we expect only {"result": "ok or an error message"}
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

    /** @var array Event listeners. */
    protected $listeners = [];

    /** @var array Registered loggers. */
    protected $loggers = [];

    /**
     * EventListener constructor.
     *
     * @param ILogger[] $loggers Loggers to be used. Optional. Requires loggers to be {@link ILogger} descendants.
     * @throws \InvalidArgumentException in case when invalid logger given (not a ILogger descendant).
     */
    public function __construct(array $loggers = [])
    {
        if (!empty($loggers)) {
            foreach ($loggers as $logger) {
                if ($logger instanceof ILogger) {
                    continue;
                }

                $class = get_class($logger);
                throw new \InvalidArgumentException("Invalid Logger object given, ILogger descendant required, `{$class}` given.");
            }
        }

        $this->loggers = $loggers;
    }

    /**
     * Event handler registration method.
     *
     * You can register only one handler for each event (see available {@link EventListener::EVENTS events} list).
     *
     * @param string $event Event name.
     * @param callable $callback Event callback.
     * Must return response string (JSON) after executing. See {@link Response}.
     *
     * @throws \InvalidArgumentException in case if invalid event name given or second parameter is not a callable.
     * @throws \LogicException in case if event handler for given event is already registered.
     */
    public function on(string $event, callable $callback): void
    {
        if (!in_array($event, static::EVENTS, true)) {
            throw new \InvalidArgumentException("Invalid `event` name given (`{$event}`).");
        }

        if (!is_callable($callback, false, $callableName)) {
            throw new \InvalidArgumentException("Invalid callable given (`{$callableName}`.)");
        }

        if (array_key_exists($event, $this->listeners)) {
            throw new \LogicException("Event handler for `{$event}` event is already registered");
        }

        $this->listeners[$event] = $callback;
    }

    /**
     * Listener. Use it after adding necessary handlers (using {@link on()} method).
     *
     * @return bool
     * @throws \RuntimeException
     * @throws \LogicException in case when couldn't get `event` name from the request.
     * @throws \LogicException in case when got unknown `event` name from the request.
     * @throws \LogicException in case when event handler for current `event` returned an empty response.
     */
    public function listen(): bool
    {
        if (!array_key_exists('REQUEST_METHOD', $_SERVER) || ('POST' !== $_SERVER['REQUEST_METHOD'])) {
            return false;
        }

        /** @var string Webhook request data string. */
        $requestData = file_get_contents('php://input');
        if (empty($requestData)) {
            return false;
        }

        // log request string via registered loggers
        foreach ($this->loggers as $logger) {
            $logger->logRequest($requestData);
        }

        /** @var array Decoded Webhook request data array (assoc). */
        $decodedRequest = json_decode($requestData, true);
        if (empty($decodedRequest)) {
            return false;
        }

        /** @var string Event name. */
        $event = $decodedRequest['event_name'] ?: null;
        if (null === $event) {
            throw new \LogicException("Request doesn't contain `event` field (not a Jivochat Webhook?).");
        }

        if (!in_array($event, static::EVENTS, true)) {
            throw new \LogicException("Got unknown event name from Webhook request (`{$event}`).");
        }

        if (!array_key_exists($event, $this->listeners)) {
            $responseData = (new Response())->getResponse();
        } else {
            /** @var string Response JSON string. */
            $responseData = call_user_func($this->listeners[$event], $decodedRequest);
            if (empty($responseData)) {
                throw new \LogicException("Event handler for `{$event}` event returned an empty response.");
            }
        }

        // log response string via registered loggers
        foreach ($this->loggers as $logger) {
            $logger->logResponse($responseData);
        }

        \HttpResponse::status(200);
        \HttpResponse::setContentType('application/json; charset=utf-8');
        \HttpResponse::setData($responseData);

        return \HttpResponse::send();
    }
}
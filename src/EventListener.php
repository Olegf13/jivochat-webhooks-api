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
}
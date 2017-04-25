<?php

namespace Olegf13\Jivochat\Webhooks;

use Olegf13\Jivochat\Webhooks\Event\Event;
use Olegf13\Jivochat\Webhooks\Log\LogInterface;

/**
 * Webhooks API Event listener.
 *
 * @package Jivochat\Webhook
 */
class EventListener
{
    /** @var array Event listeners. */
    protected $listeners = [];

    /** @var array Registered loggers. */
    protected $loggers = [];

    /**
     * EventListener constructor.
     *
     * @param LogInterface[] $loggers Loggers to be used. Optional. Requires loggers to be {@link LogInterface} descendants.
     * @throws \InvalidArgumentException in case when invalid logger given (not a LogInterface descendant).
     */
    public function __construct(array $loggers = [])
    {
        if (!empty($loggers)) {
            foreach ($loggers as $logger) {
                if ($logger instanceof LogInterface) {
                    continue;
                }

                $class = get_class($logger);
                throw new \InvalidArgumentException("Invalid Logger object given, LogInterface descendant required, `{$class}` given.");
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
        if (!in_array($event, Event::EVENTS, true)) {
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
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException in case when couldn't get `event` name from the request.
     * @throws \LogicException in case when got unknown `event` name from the request.
     * @throws \LogicException in case when event handler for current `event` returned an empty response.
     */
    public function listen(): void
    {
        if (!array_key_exists('REQUEST_METHOD', $_SERVER) || ('POST' !== $_SERVER['REQUEST_METHOD'])) {
//            throw new \HttpRequestMethodException("Only HTTP POST is expected,");
            return;
        }

        /** @var string Webhook request data string. */
        $requestData = file_get_contents('php://input');
        if (empty($requestData)) {
            return;
        }

        // log request string via registered loggers
        foreach ($this->loggers as $logger) {
            $logger->logRequest($requestData);
        }

        $event = Event::create($requestData);

        // if no handler is registered on current event, respond with a default response
        if (!array_key_exists($event->event_name, $this->listeners)) {
            $this->respond(new Response());
        }

        /** @var Response $response */
        $response = call_user_func($this->listeners[$event->event_name], $event);
        if (!($response instanceof Response)) {
            throw new \LogicException("Registered handler for `{$event->event_name}` event returned invalid response.");
        }

        $this->respond($response);
    }

    /**
     * Responds on Webhook after event handler is executed.
     *
     * @param Response $response Event handler response.
     * @throws \RuntimeException
     */
    protected function respond(Response $response): void
    {
        /** @var string Webhook response JSON string. */
        $responseJSON = $response->getResponse();

        // log response string via registered loggers
        foreach ($this->loggers as $logger) {
            $logger->logResponse($responseJSON);
        }

        http_response_code(200);
        header('ContentType: application/json; charset=utf-8');

        die($responseJSON);
    }
}
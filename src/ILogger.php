<?php

namespace Jivochat\Webhooks;

/**
 * Interface ILogger
 *
 * Use it for creating own logger classes to log Jivochat API Webhooks.
 *
 * See {@link Jivochat\Webhooks\MonologLogger MonologLogger} and {@link Jivochat\Webhooks\MySQLLogger MySQLLogger}
 * for examples.
 *
 * @package Jivochat\Webhooks
 */
interface ILogger
{
    /**
     * Logger constructor.
     *
     * Concrete implementation depends on Logger type,
     * e.g. for MySQL it should be \PDO instance and table name.
     *
     * @param mixed $logHandler Logger handler object (e.g. \PDO instance for MySQL).
     * @param string $target Logger target string (e.g. filename full path for files).
     * @throws \InvalidArgumentException in case if incorrect Logger handler given in concrete implementation.
     */
    public function __construct($logHandler, string $target);

    /**
     * Logs Jivosite Webhook event request data.
     *
     * Concrete implementation depends on Logger type (MySQL db, file etc).
     *
     * @param string $data Request data, must be valid JSON string.
     * @return bool Returns true on successful logging.
     */
    public function logRequest(string $data): bool;

    /**
     * Logs Jivosite Webhook event response data.
     *
     * Concrete implementation depends on Logger type (MySQL db, file etc).
     *
     * @param string $data Data to be logged, must be valid JSON string.
     * @return bool Returns true on successful logging.
     */
    public function logResponse(string $data): bool;
}
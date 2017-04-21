<?php

namespace Jivochat\Webhooks\Log;

/**
 * Class MongoDBLog
 * todo: to be implemented
 *
 * @package Jivochat\Webhooks\Log
 */
class MongoDBLog implements LogInterface
{
    /**
     * Log constructor.
     *
     * Concrete implementation depends on Log type,
     * e.g. for MySQL it should be \PDO instance and table name.
     *
     * @param mixed $logHandler Log handler object (e.g. \PDO instance for MySQL).
     * @param string $target Log target string (e.g. filename full path for files).
     * @throws \InvalidArgumentException in case if incorrect Log handler given in concrete implementation.
     */
    public function __construct($logHandler, string $target)
    {
        parent::__construct($logHandler, $target);
    }

    /**
     * Logs Jivosite Webhook event request data.
     *
     * Concrete implementation depends on Log type (MySQL db, file etc).
     *
     * @param string $data Request data, must be valid JSON string.
     * @return bool Returns true on successful logging.
     */
    public function logRequest(string $data): bool
    {
        // TODO: Implement logRequest() method.
    }

    /**
     * Logs Jivosite Webhook event response data.
     *
     * Concrete implementation depends on Log type (MySQL db, file etc).
     *
     * @param string $data Data to be logged, must be valid JSON string.
     * @return bool Returns true on successful logging.
     */
    public function logResponse(string $data): bool
    {
        // TODO: Implement logResponse() method.
    }
}
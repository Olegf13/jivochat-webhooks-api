<?php

namespace Jivochat\Webhooks\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class MonologLog
 * @package Jivochat\Webhooks\Log
 */
class MonologLog implements LogInterface
{
    /** @var Logger Monolog logger instance. */
    protected $logger;

    /**
     * FileLogger constructor.
     *
     * @param Logger $logger Monolog logger instance.
     * @param string $filePath (with full path, e.g. `/var/log/jivosite_callbacks.log`).
     * @throws \InvalidArgumentException in case if given log file already exists but is not writable.
     * @throws \Exception
     */
    public function __construct($logger, string $filePath)
    {
        if (file_exists($filePath) && !is_writable($filePath)) {
            throw new \InvalidArgumentException("File `{$filePath}` is not writable.");
        }

        $logger->pushHandler(new StreamHandler($filePath, Logger::INFO));

        $this->logger = $logger;
    }

    /**
     * Logs Jivosite Webhook event request data.
     *
     * Concrete implementation depends on Logger type (MySQL db, file etc).
     *
     * @param string $data Request data, must be valid JSON string.
     * @return bool Returns true on successful logging.
     */
    public function logRequest(string $data): bool
    {
        return $this->logger->info($data);
    }

    /**
     * Logs Jivosite Webhook event response data.
     *
     * Concrete implementation depends on Logger type (MySQL db, file etc).
     *
     * @param string $data Data to be logged, must be valid JSON string.
     * @return bool Returns true on successful logging.
     */
    public function logResponse(string $data): bool
    {
        return $this->logger->info($data);
    }
}
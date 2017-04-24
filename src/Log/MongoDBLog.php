<?php

namespace Jivochat\Webhooks\Log;

use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Collection;

/**
 * Allows logging of Webhooks requests/response data into MongoDB.
 *
 * @package Jivochat\Webhooks\Log
 */
class MongoDBLog implements LogInterface
{
    /** @var Client MongoDB client instance. */
    protected $client;
    /** @var Database MongoDB database instance. */
    protected $database;
    /** @var Collection MongoDB collection instance for storing request logs. */
    protected $requestCollection;
    /** @var Collection MongoDB collection instance for storing response logs. */
    protected $responseCollection;
    /** @var int|null Id of Webhook request row in database. Used for saving response. */
    protected $id;

    /**
     * MongoDBLog constructor.
     *
     * @param Client $client MongoDB collection object.
     * @param string $database Database name for logging.
     * @throws \InvalidArgumentException in case if incorrect Log handler given in concrete implementation.
     */
    public function __construct($client, string $database)
    {
        if (!($client instanceof Client)) {
            $class = get_class($client);
            throw new \InvalidArgumentException("First parameter must be an instance of \\Client, `{$class}` given.");
        }

        $this->client = $client;
        $this->database = $this->client->selectDatabase($database);
    }

    /**
     * Setter for {@link requestCollection} property.
     *
     * @param string $name Collection name (optional).
     * @throws \MongoDB\Exception\InvalidArgumentException
     */
    public function setRequestCollection(string $name = 'jivochat_webhooks_request_log')
    {
        $this->requestCollection = $this->database->selectCollection($name);
    }

    /**
     * Setter for {@link responseCollection} property.
     *
     * @param string $name Collection name (optional).
     * @throws \MongoDB\Exception\InvalidArgumentException
     */
    public function setResponseCollection(string $name = 'jivochat_webhooks_response_log')
    {
        $this->responseCollection = $this->database->selectCollection($name);
    }

    /**
     * @inheritdoc
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function logRequest(string $data): bool
    {
        if (null === $this->requestCollection) {
            $this->setRequestCollection();
        }

        $result = $this->requestCollection->insertOne(json_decode($data, true));
        $this->id = $result->getInsertedId();

        return $result->isAcknowledged();
    }

    /**
     * @inheritdoc
     * @throws \MongoDB\Exception\InvalidArgumentException
     * @throws \MongoDB\Driver\Exception\RuntimeException
     */
    public function logResponse(string $data): bool
    {
        if (null === $this->responseCollection) {
            $this->setResponseCollection();
        }

        $responseData = json_decode($data, true);
        $responseData['request_id'] = $this->id;

        $result = $this->responseCollection->insertOne($responseData);

        return $result->isAcknowledged();
    }
}
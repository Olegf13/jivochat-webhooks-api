<?php

namespace Jivochat\Webhooks\Log;

/**
 * Class MySQLLog
 * @package Jivochat\Webhooks\Log
 */
class MySQLLog implements LogInterface
{
    /** @var \PDO Log MySQL PDO instance. */
    protected $pdo;
    /** @var string Logging table name. */
    protected $tableName;
    /** @var int|null Id of Webhook request row in database. Used for saving response. */
    protected $id;

    /**
     * MySQLLog constructor.
     *
     * @param \PDO $pdo MySQL PDO instance.
     * @param string $tableName Table name. Optional, defaults to `jivochat_webhooks_log`.
     * @throws \InvalidArgumentException in case if first argument is not a \PDO instance.
     * @throws \RuntimeException in case if error occurs.
     */
    public function __construct($pdo, string $tableName = 'jivochat_webhooks_log')
    {
        if (!($pdo instanceof \PDO)) {
            $class = get_class($pdo);
            throw new \InvalidArgumentException("First parameter must be an instance of \\PDO, `{$class}` given.");
        }

        $this->pdo = $pdo;
        $this->tableName = $tableName;

        if (!$this->isTableExists()) {
            $this->createLogTable();
        }
    }

    /**
     * Log Jivochat Webhook request data.
     *
     * @param string $data Data to be logged. Must represent a JSON string.
     * @return bool Returns `true` on success.
     */
    public function logRequest(string $data): bool
    {
        $sql = "INSERT INTO `{$this->tableName}` (`request_data`) VALUES (:webhook_data);";
        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute([':webhook_data' => $data]);
        if (!$result) {
            return false;
        }

        $id = (int)$this->pdo->lastInsertId();
        if (!empty($id)) {
            $this->id = $id;
        }

        return (bool)$stmt->rowCount();
    }

    /**
     * Log Jivochat Webhook response data.
     *
     * @param string $responseData Data to be logged. Must represent a JSON string.
     * @return bool Returns `true` on success.
     */
    public function logResponse(string $responseData): bool
    {
        if (null === $this->id) {
            return false;
        }

        $sql = "UPDATE `{$this->tableName}` SET `response_data` = :response_data WHERE `id` = {$this->id} LIMIT 1;";
        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute([':response_data' => $responseData]);
        if (!$result) {
            return false;
        }

        $this->id = null;

        return (bool)$stmt->rowCount();
    }

    /**
     * Check if Log table exists.
     *
     * @return bool Returns `true` if table exists.
     * @throws \RuntimeException in case if error occurs while checking Log table existence.
     */
    protected function isTableExists(): bool
    {
        $sql = 'SHOW TABLES LIKE :table_name;';
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([':table_name' => $this->tableName]);
        if (!$result) {
            $errorInfo = print_r($stmt->errorInfo(), true);
            throw new \RuntimeException("Couldn't check if Log table exists. Query string: `{$stmt->queryString}`, table name: `{$this->tableName}`, error info: `{$errorInfo}`.");
        }

        return 1 === $stmt->rowCount();
    }

    /**
     * Create logger table in database.
     *
     * @throws \RuntimeException in case if error occurs while Log table creation.
     */
    protected function createLogTable(): void
    {
        $sql = <<<SQL
CREATE TABLE `{$this->tableName}` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Id',
  `request_data` JSON NOT NULL
  COMMENT 'Jivochat Webhook API request data (JSON string)',
  `response_data` JSON NULL DEFAULT NULL 
  COMMENT 'Jivochat Webhook API response data (JSON string)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT 'Log creation datetime',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  COMMENT 'Update datetime',
  PRIMARY KEY (id)
)
  ENGINE = InnoDB
  COMMENT 'Stores Jivochat Webhooks API requests.';
SQL;
        $result = $this->pdo->exec($sql);
        if (!$result) {
            $errorInfo = print_r($this->pdo->errorInfo(), true);
            throw new \RuntimeException("Couldn't create Log table. Query string: `{$sql}`, table name: `{$this->tableName}`, error info: `{$errorInfo}`.");
        }
    }
}
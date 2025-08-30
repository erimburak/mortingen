<?php

namespace DB;

use PDO;
use PDOException;
use RuntimeException;
use DB\Helper\BaseHelper;

class DB
{
    protected PDO $pdo;
    protected BaseHelper $connection;

    protected int $lastAffectedRows = 0;

    public static ?DB $db = null;

    public function __construct(BaseHelper $connection, array $options = [])
    {
        $this->pdo = new PDO(
            (string) $connection,
            $connection->getUser(),
            $connection->getPass(),
            $options
        );

        $this->connection = $connection;

        if (is_null(static::$db))
        {
            static::$db = $this;
        }
    }

    public function getConnection(): BaseHelper
    {
        return $this->connection;
    }

    public function query(string|\DB\Query $sql, array $params = []): \PDOStatement
    {
        if ($sql instanceof \DB\Query)
        {
            $params = $sql->getNamedParams();
            $sql = (string) $sql;
        }

        $stmt = $this->pdo->prepare($sql);
        if ($stmt === false)
        {
            throw new \RuntimeException('Failed to prepare statement');
        }
        $stmt->execute($params);

        $this->lastAffectedRows = $stmt->rowCount();

        return $stmt;
    }

    public function fetchAll(string|\DB\Query $sql, array $params = []): array
    {
        if ($sql instanceof \DB\Query)
        {
            $params = $sql->getNamedParams();
            $sql = (string) $sql;
        }
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchOne(string|\DB\Query $sql, array $params = []): ?array
    {
        if ($sql instanceof \DB\Query)
        {
            $params = $sql->getNamedParams();
            $sql = (string) $sql;
        }
        $result = $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    public function getAffectedRows(): int
    {
        return $this->lastAffectedRows;
    }

    public function tableExists(string $table): bool
    {
        $sql = $this->connection->tableExistsQuery();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['table' => $table]);

        return (bool) $stmt->fetchColumn();
    }

    public function columnExists(string $table, string $column): bool
    {
        // Special case for SQLite
        if ($this->connection instanceof \DB\Helper\SQLiteHelper)
        {
            $stmt = $this->pdo->query("PRAGMA table_info(" . $this->pdo->quote($table) . ")");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $col)
            {
                if (strcasecmp($col['name'], $column) === 0)
                {
                    return true;
                }
            }
            return false;
        }

        $sql = $this->connection->columnExistsQuery();
        if (!$sql)
        {
            throw new RuntimeException('columnExistsQuery() not implemented for this connection');
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['table' => $table, 'column' => $column]);
        return (bool) $stmt->fetchColumn();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Run a callback inside a transaction.
     * Automatically commits if callback succeeds,
     * rolls back if an exception is thrown.
     *
     *       Usage example:
     *
     *        $db->transaction(function(DB $db) {
     *           $db->query("UPDATE accounts SET balance = balance - :amount WHERE id = :id", [
     *               'amount' => 100,
     *               'id' => 1,
     *           ]);
     *           
     *           $db->query("UPDATE accounts SET balance = balance + :amount WHERE id = :id", [
     *               'amount' => 100,
     *               'id' => 2,
     *           ]);
     *       });
     *
     * @param callable $callback
     * @return mixed
     * @throws \Exception
     */
    public function transaction(callable $callback)
    {
        $this->beginTransaction();
        try
        {
            $result = $callback($this);
            $this->commit();
            return $result;
        }
        catch (\Throwable $e)
        {
            $this->rollBack();
            throw $e;
        }
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * The driver-specific error message or null if none
     */
    public function getLastError(): ?string
    {
        $errorInfo = $this->pdo->errorInfo();
        return $errorInfo[2] ?? null;
    }

    /**
     * The driver-specific error code or null if none
     */
    public function getLastErrorNo(): ?int
    {
        $errorInfo = $this->pdo->errorInfo();
        return isset($errorInfo[1]) ? (int)$errorInfo[1] : null;
    }
}

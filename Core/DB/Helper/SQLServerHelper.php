<?php

namespace DB\Helper;

class SQLServerHelper extends BaseHelper
{
    public function __construct(
        public string $server,
        public string $dbname,
        protected ?string $user = null,
        protected ?string $pass = null,
        public int $port = 1433
    )
    {
    }

    public function __toString(): string
    {
        return "sqlsrv:Server={$this->server},{$this->port};Database={$this->dbname}";
    }

    public function tableExistsQuery(): string
    {
        return "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table";
    }

    public function columnExistsQuery(): string
    {
        return "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :table AND COLUMN_NAME = :column";
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE [{$table}] ADD [{$column}] {$type} {$definitions}";
    }

    public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string
    {
        $mod = $tableModifiers ?? static::$defaultTableModifiers;
        $mod = $mod ? " " . trim($mod) : "";
        $cols = implode(",\n    ", $columns);
        return "CREATE TABLE [{$table}] (\n    {$cols}\n){$mod};";
    }
}

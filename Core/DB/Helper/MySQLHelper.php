<?php

namespace DB\Helper;

class MySQLHelper extends BaseHelper
{
    protected static string $defaultTableModifiers = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    public function __construct(
        public string $host,
        public string $dbname,
        protected ?string $user = null,
        protected ?string $pass = null,
        public int $port = 3306,
        public string $charset = 'utf8mb4'
    )
    {
    }

    public function __toString(): string
    {
        return "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
    }

    public function tableExistsQuery(): string
    {
        return "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table";
    }

    public function columnExistsQuery(): string
    {
        return "SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column";
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$type} {$definitions}";
    }

    public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string
    {
        $mod = $tableModifiers ?? static::$defaultTableModifiers;
        $mod = $mod ? " " . trim($mod) : "";
        $cols = implode(",\n    ", $columns);
        return "CREATE TABLE `{$table}` (\n    {$cols}\n){$mod};";
    }
}

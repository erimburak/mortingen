<?php

namespace DB\Helper;

class CUBRIDConnection extends BaseHelper
{
    public function __construct(
        public string $host = 'localhost',
        public string $dbname = '',
        protected ?string $user = null,
        protected ?string $pass = null,
        public int $port = 33000,
        public string $charset = 'utf8'
    )
    {
    }

    public function __toString(): string
    {
        return "cubrid:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
    }

    public function tableExistsQuery(): string
    {
        return "SELECT 1 FROM db_class WHERE class_name = :table";
    }

    public function columnExistsQuery(): string
    {
        return "SELECT 1 FROM db_attribute WHERE class_name = :table AND attr_name = :column";
    }

    public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string
    {
        $mod = $tableModifiers ?? static::$defaultTableModifiers;
        $mod = $mod ? " " . trim($mod) : "";
        $cols = implode(",\n    ", $columns);
        return "CREATE TABLE \"{$table}\" (\n    {$cols}\n){$mod};";
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE \"{$table}\" ADD COLUMN \"{$column}\" {$type} {$definitions}";
    }
}

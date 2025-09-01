<?php

namespace DB\Helper;

class PostgreSQLHelper extends BaseHelper
{
    public function __construct(
        public string $host,
        public string $dbname,
        protected ?string $user = null,
        protected ?string $pass = null,
        public int $port = 5432
    )
    {
    }

    public function __toString(): string
    {
        return "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
    }

    public function tableExistsQuery(): string
    {
        return "SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema() AND table_name = :table";
    }

    public function columnExistsQuery(): string
    {
        return "SELECT 1 FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = :table AND column_name = :column";
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE \"{$table}\" ADD COLUMN \"{$column}\" {$type} {$definitions}";
    }

    public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string
    {
        $mod = $tableModifiers ?? static::$defaultTableModifiers;
        $mod = $mod ? " " . trim($mod) : "";
        $cols = implode(",\n    ", $columns);
        return "CREATE TABLE \"{$table}\" (\n    {$cols}\n){$mod};";
    }

    public function addForeignKeyQuery(string $table, string $column, string $referencedTable, string $referencedColumn): string
    {
        $constraintName = "fk_{$table}_{$column}";
        return "ALTER TABLE \"{$table}\" ADD CONSTRAINT \"{$constraintName}\" FOREIGN KEY (\"{$column}\") REFERENCES \"{$referencedTable}\" (\"{$referencedColumn}\") ON DELETE CASCADE ON UPDATE CASCADE";
    }
}

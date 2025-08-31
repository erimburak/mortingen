<?php

namespace DB\Helper;

class FirebirdHelper extends BaseHelper
{
    public function __construct(
        public string $host = 'localhost',
        public string $dbname,
        protected ?string $user = null,
        protected ?string $pass = null,
        public int $port = 3050,
        public string $charset = 'UTF8'
    )
    {
    }

    public function __toString(): string
    {
        return "firebird:dbname={$this->host}/{$this->port}:{$this->dbname};charset={$this->charset}";
    }

    public function tableExistsQuery(): string
    {
        return 'SELECT 1 FROM rdb$relations WHERE rdb$relation_name = UPPER(:table) AND rdb$view_blr IS NULL';
    }

    public function columnExistsQuery(): string
    {
        return 'SELECT 1 FROM rdb$relation_fields WHERE rdb$relation_name = UPPER(:table) AND rdb$field_name = UPPER(:column)';
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE {$table} ADD {$column} {$type} {$definitions}";
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
        $constraintName = "FK_" . strtoupper($table) . "_" . strtoupper($column);
        return "ALTER TABLE \"{$table}\" ADD CONSTRAINT {$constraintName} FOREIGN KEY ({$column}) REFERENCES \"{$referencedTable}\" ({$referencedColumn})";
    }
}

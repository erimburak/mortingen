<?php

namespace DB\Helper;

class DB2ODBCHelper extends BaseHelper
{
    public function __construct(
        public string $dsn,
        protected ?string $user = null,
        protected ?string $pass = null
    )
    {
    }

    public function __toString(): string
    {
        return "odbc:{$this->dsn}";
    }

    public function tableExistsQuery(): string
    {
        return "SELECT 1 FROM syscat.tables WHERE tabschema = CURRENT SCHEMA AND tabname = :table";
    }

    public function columnExistsQuery(): string
    {
        return "SELECT 1 FROM syscat.columns WHERE tabschema = CURRENT SCHEMA AND tabname = :table AND colname = :column";
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE {$table} ADD COLUMN {$column} {$type} {$definitions}";
    }

    public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string
    {
        $mod = $tableModifiers ?? static::$defaultTableModifiers;
        $mod = $mod ? " " . trim($mod) : "";
        $cols = implode(",\n    ", $columns);
        return "CREATE TABLE \"{$table}\" (\n    {$cols}\n){$mod}";
    }

    public function addForeignKeyQuery(string $table, string $column, string $referencedTable, string $referencedColumn): string
    {
        $constraintName = "FK_{$table}_{$column}";
        return "ALTER TABLE \"{$table}\" ADD CONSTRAINT {$constraintName} FOREIGN KEY ({$column}) REFERENCES \"{$referencedTable}\" ({$referencedColumn})";
    }
}

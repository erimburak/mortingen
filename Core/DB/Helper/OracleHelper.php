<?php

namespace DB\Helper;

class OracleHelper extends BaseHelper
{
    public function __construct(
        public string $tns, // ORACLE service identifier (e.g. host:port/sid or a TNS name)
        protected ?string $user = null,
        protected ?string $pass = null,
        public string $charset = 'AL32UTF8'
    )
    {
    }

    public function __toString(): string
    {
        return "oci:dbname={$this->tns};charset={$this->charset}";
    }

    public function tableExistsQuery(): string
    {
        return "SELECT 1 FROM all_tables WHERE table_name = UPPER(:table)";
    }

    public function columnExistsQuery(): string
    {
        return "SELECT 1 FROM all_tab_columns WHERE table_name = UPPER(:table) AND column_name = UPPER(:column)";
    }

    public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string
    {
        return "ALTER TABLE \"{$table}\" ADD (\"{$column}\" {$type} {$definitions})";
    }

    public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string
    {
        $mod = $tableModifiers ?? static::$defaultTableModifiers;
        $mod = $mod ? " " . trim($mod) : "";
        $cols = implode(",\n    ", $columns);
        // Oracle prefers uppercase for unquoted identifiers
        return "CREATE TABLE \"" . strtoupper($table) . "\" (\n    {$cols}\n){$mod}";
    }
}

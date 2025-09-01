<?php

namespace DB\Helper;

abstract class BaseHelper
{
    protected static string $defaultTableModifiers = '';

    protected ?string $user = null;
    protected ?string $pass = null;

    abstract public function __toString(): string;

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public static function setTableModifiers(string $tableModifiers)
    {
        static::$defaultTableModifiers = $tableModifiers;
    }

    abstract public function tableExistsQuery(): string;

    abstract public function columnExistsQuery(): string;

    abstract public function addTableQuery(string $table, array $columns, ?string $tableModifiers = null): string;

    abstract public function addColumnQuery(string $table, string $column, string $type, string $definitions = ''): string;

    abstract public function addForeignKeyQuery(string $table, string $column, string $referencedTable, string $referencedColumn): string;
}

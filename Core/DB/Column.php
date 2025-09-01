<?php

namespace DB;

class Column
{
    private string $name;
    private string $table;

    public function __construct(
        private string $type,      // e.g. 'BIGINT', 'VARCHAR(255)', 'TEXT'
        private string $definition = '' // e.g. 'UNSIGNED NOT NULL AUTO_INCREMENT', or any extra SQL
    )
    {
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    /**
     * returns "{type} {definition}"
     */
    public function getFullDefinition(): string
    {
        return trim($this->name . ' ' . $this->type . ' ' . $this->definition);
    }

    public function __toString(): string
    {
        return (string) ($this->table . '.' . $this->name);
    }
}

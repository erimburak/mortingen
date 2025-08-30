<?php

namespace DB;

/**
 * Represents a query parameter.
 *
 * It is the counterpart to the 'Identifier' class.
 * - Use Identifier for table/column names (not quoted).
 * - Use Param for values (quoted).
 */
final class Param
{
    /**
     * The raw value of the parameter.
     * @var mixed
     */
    private mixed $value;

    /**
     * Creates a new parameter instance.
     * This is the "normal way" to create an object.
     *
     * @param mixed $value The value to be wrapped.
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Magic method for string conversion.
     *
     * This is where the sanitization and quoting logic happens. It formats
     * the value correctly based on its type (string, integer, null, etc.).
     *
     * @return string The formatted and quoted value.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}

<?php

namespace DB;

final class Query
{
    /**
     * Stores the parts of the query in the order they were called.
     * @var array
     */
    private array $parts = [];
    private array $namedParams = [];

    /**
     * The constructor is made private to encourage using the static factory methods.
     */
    private function __construct()
    {
        // Intentionally private.
    }

    /**
     * Magic static factory method.
     * Intercepts calls like `Query::select(...)` to start a new query chain.
     *
     * @param string $method The name of the called static method (e.g., "select").
     * @param array $args The arguments passed to the method.
     * @return self A new Query instance to allow method chaining.
     */
    public static function __callStatic(string $method, array $args): self
    {
        // Create a new instance and make the first call on it.
        return (new self())->__call($method, $args);
    }

    /**
     * Magic instance method for chained calls.
     *
     * @param string $method The name of the called method (e.g., "from", "where").
     * @param array $args The arguments passed to the method.
     * @return self Returns the instance for method chaining.
     */
    public function __call(string $method, array $args): self
    {
        $this->parts[] = [
            'keyword' => $this->formatKeyword($method),
            'content' => $this->resolveParameters($args)
        ];
        return $this;
    }

    /**
     * The core recursive function to process parameters based on structure.
     *
     * @param array $data The array of parameters to process.
     * @return string The formatted string part.
     */
    private function resolveParameters(array $data): string
    {
        if (empty($data))
        {
            return '';
        }

        // Rule 1: If the elements are arrays, the separator is a comma.
        if (isset($data[0]) && is_array($data[0]))
        {
            $separator = ', ';
        }
        // Rule 2: If the elements are simple values, the separator is a space.
        else
        {
            $separator = ' ';
        }

        $processed = [];
        foreach ($data as $item)
        {

            if ($item instanceof Identifier)
            {
                $processed[] = (string) $item;
                // $processed[] = '`' . $item . '`';
                //Backticks are MySQL-origin syntax.
                //Because we do not have access to DBMS type here, we keep it as is for now.
            }
            elseif ($item instanceof Param)
            {
                $processed[] = $this->prepareParam($item);
            }
            elseif ($item instanceof self)
            {
                $processed[] = '(' . $item->toSql() . ')';
                $this->namedParams = array_merge($this->namedParams, $item->namedParams);
            }
            elseif (is_array($item))
            {
                $processed[] = $this->resolveParameters($item);
            }
            else
            {
                $processed[] = $item;
            }
        }

        return implode($separator, $processed);
    }

    /**
     * Converts a method name from camelCase or snake_case to a spaced, uppercase keyword.
     */
    private function formatKeyword(string $methodName): string
    {
        if ($methodName === '_') return '';
        $snakeCase = preg_replace('/(?<=\\w)(?=[A-Z])/', '_$1', $methodName);
        return strtoupper(str_replace('_', ' ', strtolower($snakeCase)));
    }

    private function randomParameterName(): string
    {
        return ':param_' . bin2hex(random_bytes(16));
    }

    private function escapeForLike($value, $escapeChar = '\\')
    {
        $new_value = str_replace(
            [$escapeChar, '%', '_'],
            [$escapeChar . $escapeChar, $escapeChar . '%', $escapeChar . '_'],
            $value
        );

        return '%' . $new_value . '%';
    }

    public function likeEscape($value)
    {
        $this->parts[] = [
            'keyword' => "LIKE",
            'content' => $this->prepareParam(new \DB\Param($this->escapeForLike($value)))
        ];
        $this->parts[] = [
            'keyword' => "ESCAPE",
            'content' => "'\\'"
        ];
        return $this;
    }

    /**
     * Safely prepare the value for use in a query.
     */
    private function prepareParam(mixed $value): string
    {
        $newParameterName = $this->randomParameterName();

        $this->namedParams[$newParameterName] = $value;

        return $newParameterName;
    }

    /**
     * Assembles and returns the final query string.
     */
    public function toSql(): string
    {
        $sqlParts = [];
        foreach ($this->parts as $part)
        {
            $renderedPart = trim("{$part['keyword']} {$part['content']}");
            if (!empty($renderedPart))
            {
                $sqlParts[] = $renderedPart;
            }
        }
        return implode(' ', $sqlParts);
    }

    /**
     * Magic method to allow the object to be treated as a string.
     */
    public function __toString(): string
    {
        return $this->toSql();
    }

    public function getNamedParams(): array
    {
        return $this->namedParams;
    }
}


/*
$q = Query::select(
        ["Users.id"],
        ["Orders.amount"],
        ["Users.name"]
    )
    ->from("Users")
    ->where("Orders.status", "=", "completed")->and("Orders.id",">",0)
    ->orderBy("Orders.created_at")->desc();

echo $q;


$q2 = Query::update("Users")
    ->set(
        ["name", "=", "John Doe"],
        ["score", "=", 100],
        ["last_login", "=", null]
    )
    ->where( Query::_("id", "=", 123)->and("name", "=", "a") );

echo "\n\n" . $q2;


$q3 = Query::select(
        ["Users.id"],
        ["Orders.amount"],
        ["Users.name"]
    )
    ->from( Query::select(["id"])->from("Items") )
    ->where("Orders.status", "=", "completed")->and("Orders.id",">",0)
    ->orderBy("Orders.created_at")->desc();

echo "\n\n" . $q3;

*/

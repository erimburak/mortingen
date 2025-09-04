<?php

namespace DB;

/**
 * A factory class for creating simple identifier objects.
 *
 * It uses the __callStatic() magic method to capture a method name
 * (like 'Users' or 'Orders') and use it as the value for a new instance.
 *
 * Usage:
 * $userTable = Identifier::Users();
 * echo $userTable; // Outputs: Users
 */
class Identifier
{
    /**
     * The internal value of the identifier.
     * @var string
     */
    private string $value;

    /**
     * The constructor is private to force instantiation via the static magic method.
     *
     * @param string $value The value to be stored.
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * The magic static factory method.
     *
     * This method is triggered when an undefined static method is called on the class.
     * For example, `Identifier::Users()` will call this method with $name = 'Users'.
     *
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method (ignored in this case).
     * @return self A new Identifier instance holding the method name as its value.
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        // The name of the called method becomes the value of the new object.
        return new self($name);
    }

    /**
     * Magic method for string conversion.
     *
     * Allows the object to be used in any context that requires a string.
     *
     * @return string The stored value.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}

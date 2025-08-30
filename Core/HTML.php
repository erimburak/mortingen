<?php

/**
 * Author: mofselvi
 * Written fast with the help of Gemini.
 * 
 * An extensible static helper class for creating HTML tags.
 *
 * It uses late static binding (static::) and protected methods to allow
 * child classes to override its behavior.
 *
 * Standard tags are created via __callStatic: HTML::div('content');
 * Void elements have explicit methods: HTML::img(['src' => '...']);
 */
class HTML
{
    /**
     * Magic method to handle dynamic static calls for non-void HTML tags.
     *
     * @param string $name The name of the method being called, used as the HTML tag name.
     * @param array $arguments The arguments passed to the method.
     *                         - $arguments[0] (string|View|null): The content inside the tag.
     *                         - $arguments[1] (array): An associative array of HTML attributes.
     * @return View A View object containing the generated HTML tag as a string.
     */
    public static function __callStatic($name, $arguments): View
    {
        $content = $arguments[0] ?? '';
        $attributes = $arguments[1] ?? [];
        $attributesString = static::buildAttributes($attributes);

        $content = (new View($content))->escape();

        return new View("<{$name}{$attributesString}>" . $content . "</{$name}>" . PHP_EOL);
    }

    /**
     * Builds an HTML attribute string from an associative array.
     * Can be overridden by child classes.
     *
     * @param array $attributes An associative array of attributes.
     * @return View The formatted HTML attributes, with a leading space if not empty.
     */
    protected static function buildAttributes(array $attributes): View
    {
        $parts = [];
        foreach ($attributes as $key => $value)
        {
            if (is_bool($value))
            {
                if ($value === true)
                {
                    $parts[] = $key;
                }
                continue;
            }

            if ($value !== null)
            {
                $parts[] = $key . '="' . (new View($value))->escape() . '"';
            }
        }

        return new View(empty($parts) ? '' : ' ' . implode(' ', $parts));
    }

    /**
     * A protected helper to create any void element.
     * Can be used or overridden by child classes.
     *
     * @param string $tag The name of the void tag.
     * @param array $attributes An associative array of HTML attributes.
     * @return View A View object containing the generated HTML tag as a string.
     */
    protected static function createVoidElement(string $tag, array $attributes = []): View
    {
        // Using static:: ensures that if a child class overrides buildAttributes,
        // that new version is called here.
        $attributesString = static::buildAttributes($attributes);
        return new View("<{$tag}{$attributesString}>" . PHP_EOL);
    }

    // --- Defined methods for HTML Void Elements ---

    public static function css(string $href, array $attributes = []): View
    {
        $attributes['rel'] = 'stylesheet';
        $attributes['href'] = $href;
        return new View(static::link($attributes));
    }

    public static function js(string $src, array $attributes = []): View
    {
        $attributes['src'] = $src;
        return new View(static::script($attributes));
    }

    public static function area(array $attributes = []): View
    {
        return static::createVoidElement('area', $attributes);
    }
    public static function base(array $attributes = []): View
    {
        return static::createVoidElement('base', $attributes);
    }
    public static function br(array $attributes = []): View
    {
        return static::createVoidElement('br', $attributes);
    }
    public static function col(array $attributes = []): View
    {
        return static::createVoidElement('col', $attributes);
    }
    public static function embed(array $attributes = []): View
    {
        return static::createVoidElement('embed', $attributes);
    }
    public static function hr(array $attributes = []): View
    {
        return static::createVoidElement('hr', $attributes);
    }
    public static function img(array $attributes = []): View
    {
        return static::createVoidElement('img', $attributes);
    }
    public static function input(array $attributes = []): View
    {
        return static::createVoidElement('input', $attributes);
    }
    public static function link(array $attributes = []): View
    {
        return static::createVoidElement('link', $attributes);
    }
    public static function meta(array $attributes = []): View
    {
        return static::createVoidElement('meta', $attributes);
    }
    public static function param(array $attributes = []): View
    {
        return static::createVoidElement('param', $attributes);
    }
    public static function source(array $attributes = []): View
    {
        return static::createVoidElement('source', $attributes);
    }
    public static function track(array $attributes = []): View
    {
        return static::createVoidElement('track', $attributes);
    }
    public static function wbr(array $attributes = []): View
    {
        return static::createVoidElement('wbr', $attributes);
    }
}

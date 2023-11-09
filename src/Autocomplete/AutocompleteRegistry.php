<?php
/**
 * Created for plugin-component-form
 * Date: 30.11.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\Autocomplete;


use SalesRender\Plugin\Components\Form\Exceptions\AutocompleteRegistryException;

final class AutocompleteRegistry
{

    /** @var callable */
    private static $resolver;

    private function __construct() {}

    public static function config(callable $resolver): void
    {
        self::$resolver = $resolver;
    }

    /**
     * @param string $name
     * @return AutocompleteInterface|null
     * @throws AutocompleteRegistryException
     */
    public static function getAutocomplete(string $name): ?AutocompleteInterface
    {
        if (!isset(self::$resolver)) {
            throw new AutocompleteRegistryException('Autocomplete registry was not configured', 100);
        }

        $resolver = self::$resolver;
        return $resolver($name);
    }

}
<?php
/**
 * Created for plugin-component-form
 * Date: 30.11.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\TableView;


use SalesRender\Plugin\Components\Form\Exceptions\TablePreviewRegistryException;

final class TablePreviewRegistry
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
     * @return TablePreviewInterface|null
     * @throws TablePreviewRegistryException
     */
    public static function getTablePreview(string $name): ?TablePreviewInterface
    {
        if (!isset(self::$resolver)) {
            throw new TablePreviewRegistryException('Table preview registry was not configured', 100);
        }

        $resolver = self::$resolver;
        return $resolver($name);
    }

}
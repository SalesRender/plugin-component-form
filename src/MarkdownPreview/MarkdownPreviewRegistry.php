<?php

namespace SalesRender\Plugin\Components\Form\MarkdownPreview;

use SalesRender\Plugin\Components\Form\Exceptions\MarkdownPreviewRegistryException;

final class MarkdownPreviewRegistry
{

    /** @var callable */
    private static $resolver;

    private function __construct() {}

    public static function config(callable $resolver): void
    {
        self::$resolver = $resolver;
    }


    public static function getMarkdownPreview(string $name): ?MarkdownPreviewInterface
    {
        if (!isset(self::$resolver)) {
            throw new MarkdownPreviewRegistryException('Markdown preview registry was not configured', 100);
        }

        $resolver = self::$resolver;
        return $resolver($name);
    }

}
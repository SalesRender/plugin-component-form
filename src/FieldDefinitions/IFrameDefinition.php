<?php
/**
 * Created for plugin-component-form
 * Date: 25.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


class IFrameDefinition extends FieldDefinition
{

    private string $iframe;

    public function __construct(string $title, ?string $description, callable $validator, string $iframe, $default = null)
    {
        parent::__construct($title, $description, $validator, $default);
        $this->iframe = $iframe;
    }

    public function getDefinition(): string
    {
        return 'iframe';
    }

    public function getIframe(): string
    {
        return $this->iframe;
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'iframe' => $this->iframe,
        ]);
    }
}
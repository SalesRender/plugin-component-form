<?php
/**
 * Created for plugin-component-form
 * Date: 27.12.2023
 * @author: Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

class TablePreviewField extends FieldDefinition
{

    private string $previewer;

    public function __construct(string $title, ?string $description, string $previewer, $default = null, $context = null)
    {
        parent::__construct($title, $description, fn() => [], $default, $context);
        $this->previewer = $previewer;
    }

    public function getPreviewer(): string
    {
        return $this->previewer;
    }

    public function getDefinition(): string
    {
        return 'tablePreview';
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'previewer' => $this->getPreviewer(),
        ]);
    }
}
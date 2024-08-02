<?php

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

class MarkdownPreviewField extends FieldDefinition
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
        return 'markdownPreview';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'previewer' => $this->getPreviewer(),
        ]);
    }
}
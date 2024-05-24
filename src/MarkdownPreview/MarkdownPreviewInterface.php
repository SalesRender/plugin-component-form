<?php

namespace SalesRender\Plugin\Components\Form\MarkdownPreview;

interface MarkdownPreviewInterface
{

    public function render(array $dependencies, array $context): string;

}
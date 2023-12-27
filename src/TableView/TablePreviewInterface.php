<?php
/**
 * Created for plugin-component-form
 * Date: 26.12.2023
 * @author: Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\TableView;

interface TablePreviewInterface
{

    public function render(array $dependencies, array $context): array;

}
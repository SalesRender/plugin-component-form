<?php
/**
 * Created for plugin-component-form
 * Date: 28.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\Components;


use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use SalesRender\Plugin\Components\Form\FormData;

interface ValidatorInterface
{

    public function __invoke($value, FieldDefinition $definition, FormData $data): array;

}
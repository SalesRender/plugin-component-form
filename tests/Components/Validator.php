<?php
/**
 * Created for plugin-component-form
 * Date: 28.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\Components;


use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use SalesRender\Plugin\Components\Form\FormData;

class Validator implements ValidatorInterface
{

    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke($value, FieldDefinition $definition, FormData $data): array
    {
        return ($this->callable)($value, $definition, $data);
    }

}
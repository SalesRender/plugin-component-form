<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values;


class DynamicValues implements ValuesListInterface
{

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get(): string
    {
        return $this->name;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->get();
    }
}
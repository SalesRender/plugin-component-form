<?php
/**
 * Created for plugin-component-form
 * Date: 08.02.2021
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values;


class CallableValues implements ValuesListInterface
{

    private $callable;

    private StaticValues $values;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function get(): array
    {
        return $this->resolve()->get();
    }

    public function jsonSerialize(): array
    {
        return $this->resolve()->jsonSerialize();
    }

    private function resolve(): StaticValues
    {
        if (!isset($this->values)) {
            $this->values = new StaticValues(($this->callable)());
        }

        return $this->values;
    }
}
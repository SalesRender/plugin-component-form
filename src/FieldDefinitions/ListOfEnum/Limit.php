<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum;


use JsonSerializable;

class Limit implements JsonSerializable
{

    private ?int $min;

    private ?int $max;

    public function __construct(?int $min, ?int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if (is_null($this->getMin()) && is_null($this->getMax())) {
            return null;
        }

        return [
            'min' => $this->getMin(),
            'max' => $this->getMax()
        ];
    }
}
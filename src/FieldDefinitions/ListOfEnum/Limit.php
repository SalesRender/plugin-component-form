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

    public ?int $min;

    public ?int $max;

    public function __construct(?int $min, ?int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function jsonSerialize(): ?array
    {
        if (is_null($this->min) && is_null($this->max)) {
            return null;
        }

        return [
            'min' => $this->min,
            'max' => $this->max
        ];
    }
}
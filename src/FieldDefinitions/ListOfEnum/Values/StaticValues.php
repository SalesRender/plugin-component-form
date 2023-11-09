<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values;


use InvalidArgumentException;

class StaticValues implements ValuesListInterface
{

    private array $values;

    public function __construct(array $values)
    {
        $this->guardArray($values);
        $this->values = $values;
    }

    public function get(): array
    {
        return $this->values;
    }

    public function jsonSerialize()
    {
        return $this->get();
    }

    private function guardArray(array $array)
    {
        foreach ($array as $values) {
            if (!is_array($values)) {
                throw new InvalidArgumentException('No items info (title & group)', 1);
            }

            if (!isset($values['title'])) {
                throw new InvalidArgumentException('No item title', 2);
            }

            if (!isset($values['group'])) {
                throw new InvalidArgumentException('No item group', 3);
            }

            if (!is_scalar($values['title'])) {
                throw new InvalidArgumentException('Title is not scalar', 4);
            }

            if (!is_scalar($values['group'])) {
                throw new InvalidArgumentException('Group is not scalar', 5);
            }
        }
    }

}
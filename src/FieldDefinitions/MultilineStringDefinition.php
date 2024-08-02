<?php
/**
 * Created for plugin-form.
 * Datetime: 29.01.2024 14:43
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


class MultilineStringDefinition extends StringDefinition
{

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'multiline' => true,
        ]);
    }
}
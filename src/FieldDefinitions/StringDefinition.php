<?php
/**
 * Created for plugin-form.
 * Datetime: 02.07.2018 15:37
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


class StringDefinition extends FieldDefinition
{

    public function getDefinition(): string
    {
        return 'string';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'multiline' => false,
        ]);
    }
}
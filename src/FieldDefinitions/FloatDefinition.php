<?php
/**
 * Created for plugin-form.
 * Datetime: 02.07.2018 16:54
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


class FloatDefinition extends FieldDefinition
{

    public function getDefinition(): string
    {
        return 'float';
    }
}
<?php
/**
 * Created for plugin-form.
 * Datetime: 02.07.2018 16:53
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

class IntegerDefinition extends FieldDefinition
{

    public function getDefinition(): string
    {
        return 'integer';
    }

}
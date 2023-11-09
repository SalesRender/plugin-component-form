<?php
/**
 * Created for plugin-component-form
 * Datetime: 29.08.2019 12:40
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;


class FileDefinition extends FieldDefinition
{

    public function getDefinition(): string
    {
        return 'file';
    }

}
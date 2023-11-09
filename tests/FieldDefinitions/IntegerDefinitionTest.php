<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;

class IntegerDefinitionTest extends FieldDefinitionTestCase
{

    protected function getClass(): string
    {
        return IntegerDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'integer';
    }
}

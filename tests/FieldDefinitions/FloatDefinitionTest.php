<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;

class FloatDefinitionTest extends FieldDefinitionTestCase
{

    protected function getClass(): string
    {
        return FloatDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'float';
    }
}

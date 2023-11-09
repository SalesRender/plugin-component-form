<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;

class PasswordDefinitionTest extends FieldDefinitionTestCase
{

    protected function getClass(): string
    {
        return PasswordDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'password';
    }
}

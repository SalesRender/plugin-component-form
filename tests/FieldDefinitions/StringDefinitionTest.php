<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;

class StringDefinitionTest extends FieldDefinitionTestCase
{

    public function testJsonSerialize()
    {
        $this->assertSame(
            json_encode([
                'title' => 'My field',
                'description' => 'My description',
                'definition' => $this->getDefinitionString(),
                'default' => 'My default value',
                'context' => 'My context value',
                'multiline' => false,
            ]),
            json_encode($this->definition)
        );
    }

    protected function getClass(): string
    {
        return StringDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'string';
    }
}

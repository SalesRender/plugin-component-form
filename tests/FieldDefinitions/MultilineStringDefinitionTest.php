<?php
/**
 * Created for plugin-form.
 * Datetime: 29.01.2024 14:43
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;

class MultilineStringDefinitionTest extends FieldDefinitionTestCase
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
                'multiline' => true,
            ]),
            json_encode($this->definition)
        );
    }

    protected function getClass(): string
    {
        return MultilineStringDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'string';
    }
}

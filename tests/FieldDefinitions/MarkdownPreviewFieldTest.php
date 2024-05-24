<?php

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;
use SalesRender\Plugin\Components\Form\FormData;

class MarkdownPreviewFieldTest extends FieldDefinitionTestCase
{

    public function testValidate()
    {
        $this->assertTrue($this->definition->validate(true, $this->formData));
        $this->assertTrue($this->definition->validate(false, $this->formData));

        $this->assertTrue($this->definitionValidator->validate(true, $this->formData));
        $this->assertTrue($this->definitionValidator->validate(false, $this->formData));
    }

    public function testGetErrors()
    {
        $this->assertEquals([], $this->definition->getErrors(true, $this->formData));
        $this->assertEquals([], $this->definition->getErrors(false, $this->formData));

        $this->assertEquals([], $this->definitionValidator->getErrors(true, $this->formData));
        $this->assertEquals([], $this->definitionValidator->getErrors(false, $this->formData));
    }

    public function testJsonSerialize()
    {
        $this->assertSame(
            json_encode([
                'title' => 'My field',
                'description' => 'My description',
                'definition' => $this->getDefinitionString(),
                'default' => 'My default value',
                'context' => 'My context value',
                'previewer' => 'my_previewer',
            ]),
            json_encode($this->definition)
        );
    }

    protected function getClass(): string
    {
        return MarkdownPreviewField::class;
    }

    protected function getDefinitionString(): string
    {
        return 'markdownPreview';
    }

    protected function setUp(): void
    {
        $this->formData = new FormData();

        $this->definition = new MarkdownPreviewField(
            'My field',
            'My description',
            'my_previewer',
            'My default value',
            'My context value'
        );

        $this->definitionNull = new MarkdownPreviewField(
            'My field',
            null,
            'my_previewer',
            null,
            null
        );

        $this->definitionValidator = new MarkdownPreviewField(
            'My field',
            null,
            'my_previewer',
            null,
            null
        );
    }
}

<?php
/**
 * Created for plugin-component-form
 * Date: 25.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\Components\Validator;
use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;
use SalesRender\Plugin\Components\Form\FormData;

class IFrameDefinitionTest extends FieldDefinitionTestCase
{

    protected string $iframe;

    protected function setUp(): void
    {
        $this->formData = new FormData([]);
        $this->iframe = 'example.html';

        /** @var FieldDefinition $class */
        $class = $this->getClass();

        $validator = function ($value, $object, FormData $formData) {

            $this->assertInstanceOf(FieldDefinition::class, $object);
            $this->assertSame($this->formData, $formData);

            if (!$value) {
                return ['Invalid value passed'];
            }

            return [];
        };

        $this->definition = new $class('My field', 'My description', $validator, $this->iframe, 'My default value');
        $this->definitionNull = new $class('My field', null, $validator, $this->iframe, null);
        $this->definitionValidator = new $class('My field', null, new Validator($validator), $this->iframe, null);
    }

    protected function getClass(): string
    {
        return IFrameDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'iframe';
    }

    public function testGetIframe()
    {
        $this->assertSame($this->iframe, $this->definition->getIframe());
    }

    public function testJsonSerialize()
    {
        $this->assertSame(
            json_encode([
                'title' => 'My field',
                'description' => 'My description',
                'definition' => $this->getDefinitionString(),
                'default' => 'My default value',
                'iframe' => $this->iframe,
            ]),
            json_encode($this->definition)
        );
    }
}

<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions;

use SalesRender\Plugin\Components\Form\Components\Validator;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Limit;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values\StaticValues;
use SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values\ValuesListInterface;
use SalesRender\Plugin\Components\Form\FieldDefinitionTestCase;
use SalesRender\Plugin\Components\Form\FormData;

class ListOfEnumDefinitionTest extends FieldDefinitionTestCase
{

    /** @var Limit */
    private $limit;

    /** @var ValuesListInterface */
    private $values;

    /** @var ListOfEnumDefinition */
    protected $definition;

    /** @var ListOfEnumDefinition */
    protected $definitionNull;

    public function testGetLimit()
    {
        $this->assertSame($this->limit, $this->definition->getLimit());
    }

    public function testGetNullLimit()
    {
        $this->assertNull($this->definitionNull->getLimit());
    }

    public function testGetValuesList()
    {
        $this->assertSame($this->values, $this->definition->getValues());
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
                'limit' => [
                    'min' => 1,
                    'max' => 10
                ],
                'values' => [
                    '0' => [
                        'title' => 'zero',
                        'group' => 'group'
                    ],
                    '1' => [
                        'title' => 'one',
                        'group' => 'group'
                    ],
                    '2' => [
                        'title' => 'two',
                        'group' => 'group'
                    ],
                ]
            ]),
            json_encode($this->definition)
        );
    }

    protected function getClass(): string
    {
        return ListOfEnumDefinition::class;
    }

    protected function getDefinitionString(): string
    {
        return 'listOfEnum';
    }

    protected function setUp(): void
    {
        $this->formData = new FormData([]);

        $validator = function ($value) {
            if (!$value) {
                return ['Invalid value passed'];
            }
            return [];
        };

        $this->limit = new Limit(1, 10);
        $this->values = new StaticValues([
            '0' => [
                'title' => 'zero',
                'group' => 'group'
            ],
            '1' => [
                'title' => 'one',
                'group' => 'group'
            ],
            '2' => [
                'title' => 'two',
                'group' => 'group'
            ],
        ]);

        $this->definition = new ListOfEnumDefinition(
            'My field',
            'My description',
            $validator,
            $this->values,
            $this->limit,
            'My default value',
            'My context value'
        );

        $this->definitionNull = new ListOfEnumDefinition(
            'My field',
            null,
            $validator,
            $this->values,
            null,
            null
        );

        $this->definitionValidator = new ListOfEnumDefinition(
            'My field',
            null,
            new Validator($validator),
            $this->values,
            null,
            null
        );
    }
}

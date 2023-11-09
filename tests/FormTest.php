<?php

namespace SalesRender\Plugin\Components\Form;


use Exception;
use PHPUnit\Framework\TestCase;
use SalesRender\Plugin\Components\Form\FieldDefinitions\IntegerDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\StringDefinition;
use TypeError;

class FormTest extends TestCase
{

    /** @var FieldGroup[] */
    private array $fieldGroups;

    private Form $form;

    private Form $formNullData;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldGroups = [
            'main' => new FieldGroup(
                'Main settings',
                'Main settings for this module',
                [
                    'field_1' => new IntegerDefinition('Field 1', 'Description 1', function ($value) {
                        $errors = [];
                        if (is_null($value)) {
                            $errors[] = 'Value should not be null';
                            return $errors;
                        }
                        if ($value < 0) {
                            $errors[] = 'Value should not be negative';
                        }
                        return $errors;
                    }, 10),
                    'field_2' => new StringDefinition('Field 2', 'Description 2', function ($value) {
                        if (empty($value)) {
                            return [
                                'String should not be empty'
                            ];
                        }
                        return [];
                    }, 'Hello'),
                ]
            ),
            'additional' => new FieldGroup(
                'Additional settings',
                'Additional settings for this module',
                [
                    'field_3' => new IntegerDefinition('Field 3', 'Description 3', function ($value) {
                        if ($value < 0) {
                            return [
                                'Value should not be negative'
                            ];
                        }
                        return [];
                    }, 13),
                    'field_4' => new StringDefinition('Field 4', 'Description 4', function ($value) {
                        if (empty($value)) {
                            return [
                                'String should not be empty'
                            ];
                        }
                        return [];
                    }, 'Hello 4'),
                ]
            ),
        ];

        $this->form = new Form(
            'Form_filled',
            'Form_filled description',
            $this->fieldGroups,
            'Save'
        );

        $this->formNullData = new Form(
            'Form_null',
            null,
            $this->fieldGroups,
            'Save'
        );
    }

    public function testCreateWithNotFieldGroupType()
    {
        $this->expectException(TypeError::class);
        $fieldGroups = [5, 10];
        new Form(
            'Form_filled',
            'Form_filled description',
            $fieldGroups,
            'Save'
        );
    }

    public function testGetTitle()
    {
        $this->assertEquals(
            'Form_filled',
            $this->form->getTitle()
        );
    }

    public function testGetDescription()
    {
        $this->assertEquals(
            'Form_filled description',
            $this->form->getDescription()
        );
    }

    public function testGetNullDescription()
    {
        $this->assertNull($this->formNullData->getDescription());
    }

    public function testGetGroups()
    {
        $this->assertEquals($this->fieldGroups, $this->form->getGroups());
    }

    public function testGetButton()
    {
        $this->assertEquals('Save', $this->form->getButton());
    }

    public function testGetDefaultData()
    {
        $this->assertEquals([
            'main' => [
                'field_1' => 10,
                'field_2' => 'Hello',
            ],
            'additional' => [
                'field_3' => 13,
                'field_4' => 'Hello 4',
            ]
        ], $this->form->getDefaultData()->all());
    }

    public function testClearRedundant()
    {
        $data = new FormData([
            'main' => [
                'field_1' => 10,
                'field_2' => 'Hello',
                'red_1' => 'redundant_field',
            ],
            'additional' => [
                'field_3' => 13,
                'field_4' => 'Hello 4',
            ],
            'red_group' => [
                'field_5' => 20,
                'field_6' => 'Hello world',
            ],
        ]);

        $cleared = $this->form->clearRedundant($data);
        $this->assertEquals([
            'main' => [
                'field_1' => 10,
                'field_2' => 'Hello',
            ],
            'additional' => [
                'field_3' => 13,
                'field_4' => 'Hello 4',
            ]
        ], $cleared->all());
    }

    public function testValidateData()
    {
        $this->assertFalse($this->form->validateData(new FormData([
            'main' => [
                'field_1' => -1,
                'field_2' => 'hello world',
            ],
        ])));

        $this->assertTrue($this->form->validateData(new FormData([
            'main' => [
                'field_1' => 1,
                'field_2' => 'hello world',
            ],
            'additional' => [
                'field_3' => 13,
                'field_4' => 'Hello 4',
            ],
        ])));

        $this->assertFalse($this->form->validateData(new FormData([
            'main' => [
                'field_1' => null,
                'field_2' => null,
            ],
        ])));

        $this->assertFalse($this->form->validateData(new FormData([])));
    }

    public function testGetErrors()
    {
        $this->assertEquals([
            'main' => [
                'field_1' => ['Value should not be null'],
                'field_2' => ['String should not be empty'],
            ],
            'additional' => [
                'field_4' => ['String should not be empty'],
            ],
        ], $this->form->getErrors(new FormData([])));

        $data = new FormData(['main' => [
            'field_1' => -10,
            'field_2' => 'hello world',
        ]]);

        $this->assertEquals([
            'main' => [
                'field_1' => [
                    'Value should not be negative'
                ],
            ],
            'additional' => [
                'field_4' => ['String should not be empty'],
            ],
        ], $this->form->getErrors($data));

        $data = new FormData(['main' => [
            'field_1' => -1,
            'field_2' => '',
        ]]);

        $this->assertEquals([
            'main' => [
                'field_1' => [
                    'Value should not be negative'
                ],
                'field_2' => [
                    'String should not be empty'
                ],
            ],
            'additional' => [
                'field_4' => ['String should not be empty'],
            ],
        ], $this->form->getErrors($data));
    }


    public function testJsonSerialize()
    {
        $data = json_decode(json_encode($this->form), true);
        $this->assertEquals('Form_filled', $data['title']);
        $this->assertEquals('Form_filled description', $data['description']);
        $this->assertCount(2, $data['groups']);
        $this->assertArrayHasKey('main', $data['groups']);
        $this->assertArrayHasKey('additional', $data['groups']);
        $this->assertEquals('Save', $data['button']);
    }



}

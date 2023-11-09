<?php
/**
 * Created for plugin-form
 * Datetime: 04.07.2019 16:49
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form;

use PHPUnit\Framework\TestCase;
use SalesRender\Plugin\Components\Form\FieldDefinitions\BooleanDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\IntegerDefinition;
use TypeError;

class FieldGroupTest extends TestCase
{

    /** @var FieldDefinition[] */
    private $fields;

    /** @var FieldGroup */
    private $group;

    /** @var FieldGroup */
    private $groupNull;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fields = [
            'use' => new IntegerDefinition(
                'Use this format',
                'Include this type in export',
                function () {},
                10
            ),
            'printCaption' => new BooleanDefinition(
                'Print caption',
                'Print caption at first page',
                function () {},
                false
            ),
        ];

        $this->group = new FieldGroup('Main settings', 'Primary settings for this module', $this->fields);
        $this->groupNull = new FieldGroup('Main settings', null, $this->fields);
    }

    public function testCreateWithNotFieldGroupType()
    {
        $this->expectException(TypeError::class);
        new FieldGroup('title', null, [1, 2]);
    }

    public function testGetTitle()
    {
        $this->assertEquals('Main settings', $this->group->getTitle());
    }

    public function testGetDescription()
    {
        $this->assertEquals('Primary settings for this module', $this->group->getDescription());
    }

    public function testGetNullDescription()
    {
        $this->assertNull($this->groupNull->getDescription());
    }

    public function testGetFields()
    {
        $this->assertEquals($this->fields, $this->group->getFields());
    }

    public function testJsonSerialize()
    {
        $data = json_decode(json_encode($this->group), true);
        $this->assertEquals('Main settings', $data['title']);
        $this->assertEquals('Primary settings for this module', $data['description']);
        $this->assertCount(2, $data['fields']);
        $this->assertArrayHasKey('use', $data['fields']);
        $this->assertArrayHasKey('printCaption', $data['fields']);
    }

}

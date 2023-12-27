<?php
/**
 * Created for plugin-form
 * Datetime: 04.07.2019 16:49
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Form;

use PHPUnit\Framework\TestCase;
use SalesRender\Plugin\Components\Form\Exceptions\InvalidDependencyException;
use SalesRender\Plugin\Components\Form\FieldDefinitions\BooleanDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\FieldDefinition;
use SalesRender\Plugin\Components\Form\FieldDefinitions\IntegerDefinition;
use TypeError;

class FieldGroupTest extends TestCase
{

    /** @var FieldDefinition[] */
    private $fields;

    /** @var array[][] */
    private array $dependencies;

    private array $context = [];

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

        $this->dependencies = [
            'printCaption' => ['use']
        ];

        $this->context = ['hello' => 'world'];

        $this->group = new FieldGroup('Main settings', 'Primary settings for this module', $this->fields, $this->dependencies, $this->context);
        $this->groupNull = new FieldGroup('Main settings', null, $this->fields);
    }

    public function testConstructWithNotFieldGroupType()
    {
        $this->expectException(TypeError::class);
        new FieldGroup('title', null, [1, 2]);
    }

    public function testConstructWithInvalidDependencyKey()
    {
        $this->expectException(InvalidDependencyException::class);
        $this->expectExceptionCode(100);
        new FieldGroup('Main settings', null, $this->fields, ['abc' => ['def']]);
    }

    public function testConstructWithInvalidDependencyField()
    {
        $this->expectException(InvalidDependencyException::class);
        $this->expectExceptionCode(200);
        new FieldGroup('Main settings', null, $this->fields, ['use' => ['def']]);
    }

    public function testGetTitle()
    {
        $this->assertEquals('Main settings', $this->group->getTitle());
    }

    public function testGetDescription()
    {
        $this->assertEquals('Primary settings for this module', $this->group->getDescription());
        $this->assertNull($this->groupNull->getDescription());
    }

    public function testGetFields()
    {
        $this->assertEquals($this->fields, $this->group->getFields());
    }

    public function testGetDependencies()
    {
        $this->assertEquals($this->dependencies, $this->group->getDependencies());
        $this->assertEquals([], $this->groupNull->getDependencies());
    }

    public function testGetContext()
    {
        $this->assertSame($this->context, $this->group->getContext());
        $this->assertSame([], $this->groupNull->getContext());
    }

    public function testSetContext()
    {
        $context = ['new' => 'context'];
        $this->group->setContext($context);
        $this->groupNull->setContext($context);

        $this->assertSame($context, $this->group->getContext());
        $this->assertSame($context, $this->groupNull->getContext());
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

<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values;

use PHPUnit\Framework\TestCase;

class DynamicValuesTest extends TestCase
{

    public function testGet()
    {
        $name = 'example';
        $values = new DynamicValues($name);
        $this->assertEquals($name, $values->get());
    }

    public function testJsonSerialize()
    {
        $name = 'example';
        $this->assertSame(
            "\"{$name}\"",
            json_encode(new DynamicValues($name))
        );
    }

}

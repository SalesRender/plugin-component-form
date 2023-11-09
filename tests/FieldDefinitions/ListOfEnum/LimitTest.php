<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum;

use PHPUnit\Framework\TestCase;

class LimitTest extends TestCase
{

    public function testGetMin()
    {
        $limit = new Limit(10, 20);
        $this->assertEquals(10, $limit->getMin());

        $limit = new Limit(null, 20);
        $this->assertNull($limit->getMin());
    }

    public function testGetMax()
    {
        $limit = new Limit(10, 20);
        $this->assertEquals(20, $limit->getMax());

        $limit = new Limit(10, null);
        $this->assertNull($limit->getMax());
    }

    public function testJsonSerialize()
    {
        $this->assertSame('null', json_encode(new Limit(null, null)));
        $this->assertSame('{"min":null,"max":10}', json_encode(new Limit(null, 10)));
        $this->assertSame('{"min":5,"max":null}', json_encode(new Limit(5, null)));
        $this->assertSame('{"min":5,"max":10}', json_encode(new Limit(5, 10)));
    }
}

<?php
/**
 * Created for plugin-component-form
 * Date: 08.02.2021
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CallableValuesTest extends TestCase
{

    public function testGet()
    {
        $data = [
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
        ];
        $callable = fn() => $data;

        $values = new CallableValues($callable);
        $this->assertSame($data, $values->get());
    }

    public function testConstructWithInvalidAssoc()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1);
        (new CallableValues(fn() => [
            '0' => 'zero',
            '1' => 'one',
            '2' => 'two',
        ]))->get();
    }

    public function testConstructWithoutTitle()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(2);
        (new CallableValues(fn() => [
            '0' => [
                'group' => 'group'
            ],
        ]))->get();
    }

    public function testConstructWithoutGroup()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(3);
        (new CallableValues(fn() => [
            '0' => [
                'title' => 'title'
            ],
        ]))->get();
    }

    public function testConstructWithNonScalarTitle()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(4);
        (new CallableValues(fn() => [
            '0' => [
                'title' => [],
                'group' => 'group'
            ],
        ]))->get();
    }

    public function testConstructWithNonScalarGroup()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(5);
        (new CallableValues(fn() => [
            '0' => [
                'title' => 'title',
                'group' => []
            ],
        ]))->get();
    }

    public function testConstructWithRedundantLevel()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(2);
        (new CallableValues(fn() => [
            '' => [
                '0' => 'zero',
                '1' => 'one',
                '2' => [
                    '0' => 'zero',
                    '1' => 'one',
                    '2' => 'two',
                ],
            ],
        ]))->get();
    }

}

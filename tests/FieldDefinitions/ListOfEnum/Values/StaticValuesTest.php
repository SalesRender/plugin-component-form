<?php
/**
 * Created for plugin-component-form
 * Date: 04.02.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Form\FieldDefinitions\ListOfEnum\Values;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StaticValuesTest extends TestCase
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
        $values = new StaticValues($data);
        $this->assertEquals($data, $values->get());
    }

    public function testConstructWithInvalidAssoc()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1);
        new StaticValues([
            '0' => 'zero',
            '1' => 'one',
            '2' => 'two',
        ]);
    }

    public function testConstructWithoutTitle()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(2);
        new StaticValues([
            '0' => [
                'group' => 'group'
            ],
        ]);
    }

    public function testConstructWithoutGroup()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(3);
        new StaticValues([
            '0' => [
                'title' => 'title'
            ],
        ]);
    }

    public function testConstructWithNonScalarTitle()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(4);
        new StaticValues([
            '0' => [
                'title' => [],
                'group' => 'group'
            ],
        ]);
    }

    public function testConstructWithNonScalarGroup()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(5);
        new StaticValues([
            '0' => [
                'title' => 'title',
                'group' => []
            ],
        ]);
    }

    public function testConstructWithRedundantLevel()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(2);
        new StaticValues([
            '' => [
                '0' => 'zero',
                '1' => 'one',
                '2' => [
                    '0' => 'zero',
                    '1' => 'one',
                    '2' => 'two',
                ],
            ],
        ]);
    }

}

<?php
/**
 * copyright iresults gmbh
 */

namespace Iresults\Core\Tests\Unit\Core\Enum;

use Iresults\Core\Enum;
use Iresults\Core\Exception\EnumOutOfRangeException;
use Iresults\Core\Tests\Fixture\MixedEnum;

class MixedEnumTest extends \PHPUnit_Framework_TestCase
{
//MixedEnum

    /**
     * @test
     * @dataProvider getValueForNameDataProvider
     * @param $name
     * @param $expected
     */
    public function getValueForNameTest($name, $expected)
    {
        $this->assertSame($expected, MixedEnum::getValueForName($name));
    }

    /**
     * @return array
     */
    public function getValueForNameDataProvider()
    {
        return [
            ['ARRAY', MixedEnum::ARRAY],
            ['array', MixedEnum::ARRAY],
            ['IS_FALSE', MixedEnum::IS_FALSE],
            ['is_false', MixedEnum::IS_FALSE],
            ['IS_TRUE', MixedEnum::IS_TRUE],
            ['is_true', MixedEnum::IS_TRUE],
            ['IS_NULL', MixedEnum::IS_NULL],
            ['is_null', MixedEnum::IS_NULL],
        ];
    }

    /**
     * @test
     * @dataProvider getNameForValueDataProvider
     * @param $value
     * @param $expected
     */
    public function getNameForValueTest($value, $expected)
    {
        $this->assertSame($expected, MixedEnum::getNameForValue($value));
    }

    /**
     * @return array
     */
    public function getNameForValueDataProvider()
    {
        return [
            [MixedEnum::ARRAY, 'ARRAY'],
            [MixedEnum::IS_FALSE, 'IS_FALSE'],
            [MixedEnum::IS_TRUE, 'IS_TRUE'],
            [MixedEnum::IS_NULL, 'IS_NULL'],
        ];
    }

    /**
     * @test
     * @param $input
     * @param $expected
     * @dataProvider normalizeDataProvider
     */
    public function instanceCreationTest($input, $expected)
    {
        $enum = new MixedEnum($input);
        $this->assertEquals($expected, $enum->getValue());
    }

    /**
     * @test
     * @dataProvider normalizeDataProvider
     * @param $input
     * @param $expected
     */
    public function normalizeTest($input, $expected)
    {
        $this->assertSame($expected, MixedEnum::normalize($input));
    }

    /**
     * @return array
     */
    public function normalizeDataProvider()
    {
        return [
            ['ARRAY', MixedEnum::ARRAY],
            ['array', MixedEnum::ARRAY],
            [MixedEnum::ARRAY, MixedEnum::ARRAY],
            ['IS_FALSE', MixedEnum::IS_FALSE],
            ['is_false', MixedEnum::IS_FALSE],
            [MixedEnum::IS_FALSE, MixedEnum::IS_FALSE],
            ['IS_TRUE', MixedEnum::IS_TRUE],
            ['is_true', MixedEnum::IS_TRUE],
            [MixedEnum::IS_TRUE, MixedEnum::IS_TRUE],
            ['IS_NULL', MixedEnum::IS_NULL],
            ['is_null', MixedEnum::IS_NULL],
            [MixedEnum::IS_NULL, MixedEnum::IS_NULL],
        ];
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Exception\EnumOutOfRangeException
     */
    public function instanceCreationShouldFailTest()
    {
        new MixedEnum('not in enum');
    }

    /**
     * @test
     * @dataProvider isValidValueDataProvider
     * @param $value
     * @param $expected
     */
    public function isValidValueTest($value, $expected)
    {
        $this->assertSame($expected, MixedEnum::isValidValue($value));
    }

    /**
     * @return array
     */
    public function isValidValueDataProvider()
    {
        return [
            [MixedEnum::ARRAY, true],
            [MixedEnum::IS_FALSE, true],
            [MixedEnum::IS_TRUE, true],
            [MixedEnum::IS_NULL, true],
            [1000, false],
            ['', false],
        ];
    }
}

<?php
/**
 * copyright iresults gmbh
 */

namespace Iresults\Core\Tests\Unit\Core\Enum;

use Iresults\Core\Enum;
use Iresults\Core\Exception\EnumException;
use Iresults\Core\Exception\InvalidEnumArgumentException;
use Iresults\Core\Exception\InvalidEnumCallException;
use Iresults\Core\Tests\Fixture\AnimalEnum;
use Iresults\Core\Tests\Fixture\EmptyEnum;
use Iresults\Core\Tests\Fixture\MixedEnum;

class GeneralEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @param $constantName
     * @expectedException \InvalidArgumentException
     * @dataProvider hasConstantDataProvider
     */
    public function hasConstantShouldThrowTest($constantName)
    {
        Enum::hasConstant($constantName);
    }

    /**
     * @return array
     */
    public function hasConstantDataProvider()
    {
        return [
            [null],
            [1],
            [false],
            [true],
            [1.1],
            [new \stdClass()],
            [[]],
        ];
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Exception\EnumOutOfRangeException
     */
    public function normalizeShouldThrowTest()
    {
        EmptyEnum::normalize('not in enum');
    }

    /**
     * @test
     * @dataProvider normalizeShouldThrowForInvalidValueDataProvider
     * @expectedException \Iresults\Core\Exception\InvalidEnumArgumentException
     * @param $input
     */
    public function normalizeShouldThrowForInvalidValueTypeTest($input)
    {
        Enum::normalize($input);
    }

    /**
     * @return array
     */
    public function normalizeShouldThrowForInvalidValueDataProvider()
    {
        return [
            [tmpfile(), false],
            [new \stdClass(), false],
            [[new \stdClass()], false],
            [[[tmpfile(), false],], false],
        ];
    }

    /**
     * @test
     * @dataProvider getNameForValueDataProvider
     * @expectedException \Iresults\Core\Exception\InvalidEnumValueException
     * @param $constantValue
     */
    public function getNameForValueShouldThrow($constantValue)
    {
        Enum::getNameForValue($constantValue);
    }

    /**
     * @return array
     */
    public function getNameForValueDataProvider()
    {
        return [
            [tmpfile()],
            [new \stdClass()],
            [[new \stdClass()]],
            [[[tmpfile()]]],
        ];
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Exception\InvalidEnumCallException
     */
    public function isValidValueTest()
    {
        Enum::isValidValue('');
    }

    /**
     * @test
     * @dataProvider isValidTypeDataProvider
     * @param $input
     * @param $expected
     */
    public function isValidTypeTest($input, $expected)
    {
        $this->assertSame($expected, Enum::isValidValueType($input));
    }

    /**
     * @return array
     */
    public function isValidTypeDataProvider()
    {
        return [
            [MixedEnum::ARRAY, true],
            [MixedEnum::IS_FALSE, true],
            [MixedEnum::IS_TRUE, true],
            [MixedEnum::IS_NULL, true],
            [AnimalEnum::CAT, true],
            [AnimalEnum::DOG, true],
            [AnimalEnum::BIRD, true],
            [AnimalEnum::RODENT, true],
            [1000, true],
            ['', true],
            ['hello', true],
            [true, true],
            [false, true],
            [null, true],
            [[], true],
            [[1, 2], true],
            [['b', 'a'], true],
            [tmpfile(), false],
            [new \stdClass(), false],
            [[new \stdClass()], false],
            [[[tmpfile(), false],], false],
        ];
    }
}

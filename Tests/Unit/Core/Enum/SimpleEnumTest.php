<?php
/**
 * copyright iresults gmbh
 */

namespace Iresults\Core\Tests\Unit\Core\Enum;

use Iresults\Core\Tests\Fixture\AnimalEnum;

class SimpleEnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider getValueForNameDataProvider
     * @param $name
     * @param $expected
     */
    public function getValueForNameTest($name, $expected)
    {
        $this->assertSame($expected, AnimalEnum::getValueForName($name));
    }

    /**
     * @return array
     */
    public function getValueForNameDataProvider()
    {
        return [
            ['CAT', AnimalEnum::CAT],
            ['cat', AnimalEnum::CAT],
            ['DOG', AnimalEnum::DOG],
            ['dog', AnimalEnum::DOG],
            ['BIRD', AnimalEnum::BIRD],
            ['bird', AnimalEnum::BIRD],
            ['RODENT', AnimalEnum::RODENT],
            ['rodent', AnimalEnum::RODENT],
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
        $this->assertSame($expected, AnimalEnum::getNameForValue($value));
    }

    /**
     * @return array
     */
    public function getNameForValueDataProvider()
    {
        return [
            [AnimalEnum::CAT, 'CAT'],
            [AnimalEnum::DOG, 'DOG'],
            [AnimalEnum::BIRD, 'BIRD'],
            [AnimalEnum::RODENT, 'RODENT'],
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
        $enum = new AnimalEnum($input);
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
        $this->assertSame($expected, AnimalEnum::normalize($input));
    }

    /**
     * @return array
     */
    public function normalizeDataProvider()
    {
        return [
            ['CAT', AnimalEnum::CAT],
            ['cat', AnimalEnum::CAT],
            [AnimalEnum::CAT, AnimalEnum::CAT],
            ['DOG', AnimalEnum::DOG],
            ['dog', AnimalEnum::DOG],
            [AnimalEnum::DOG, AnimalEnum::DOG],
            ['BIRD', AnimalEnum::BIRD],
            ['bird', AnimalEnum::BIRD],
            [AnimalEnum::BIRD, AnimalEnum::BIRD],
            ['RODENT', AnimalEnum::RODENT],
            ['rodent', AnimalEnum::RODENT],
            [AnimalEnum::RODENT, AnimalEnum::RODENT],
        ];
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Exception\EnumOutOfRangeException
     */
    public function instanceCreationShouldFailTest()
    {
        new AnimalEnum('not in enum');
    }

    /**
     * @test
     * @dataProvider isValidValueDataProvider
     * @param $value
     * @param $expected
     */
    public function isValidValueTest($value, $expected)
    {
        $this->assertSame($expected, AnimalEnum::isValidValue($value));
    }

    /**
     * @return array
     */
    public function isValidValueDataProvider()
    {
        return [
            [AnimalEnum::CAT, true],
            [AnimalEnum::DOG, true],
            [AnimalEnum::BIRD, true],
            [AnimalEnum::RODENT, true],
            [1000, false],
            ['', false],
        ];
    }
}

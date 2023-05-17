<?php

namespace Iresults\Core\Tests\Unit\Cli\Table;
use Iresults\Core\Cli\Table;
use Iresults\Core\Cli\Table\CellFormatter;
use Iresults\Core\Cli\Table\CellFormatterInterface;
use Iresults\Core\Tests\Fixture\ObjectWithoutToString;
use Iresults\Core\Tests\Fixture\ObjectWithToString;

class CellFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CellFormatterInterface
     */
    private $fixture;

    /**
     * @var Table
     */
    private $table;

    protected function setUp()
    {
        $this->fixture = new CellFormatter();
        $this->table = new Table();
    }

    protected function tearDown()
    {
        unset($this->fixture);
        unset($this->table);
    }

    /**
     * @test
     * @param $input
     * @param $expected
     * @dataProvider inputDataProvider
     */
    public function formatTest($input, $expected)
    {
        $this->assertSame($expected, $this->fixture->formatCellData($input, $this->table));
    }

    /**
     * @return array
     */
    public function inputDataProvider()
    {
        $object1 = new \stdClass();
        $object1->name = 'Daniel';
        $object2 = new ObjectWithToString();
        $object3 = new ObjectWithoutToString();

        $testTime = time();
        $date = new \DateTime('@' . $testTime);

        return [
            ['a string', 'a string'],
            [0, '0'],
            [1290, '1290'],
            [$object1, 'stdClass'],
            [$object2, 'Iresults\Core\Tests\Fixture\ObjectWithToString::__toString'],
            [$object3, 'Iresults\Core\Tests\Fixture\ObjectWithoutToString'],
            [$date, gmdate('r', $testTime)],
        ];
    }
}

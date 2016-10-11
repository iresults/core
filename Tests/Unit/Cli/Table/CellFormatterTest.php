<?php
/*
 *  Copyright notice
 *
 *  (c) 2016 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author COD
 * Created 11.10.16 10:53
 */


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

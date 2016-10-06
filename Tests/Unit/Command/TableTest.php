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
 * Created 06.10.16 17:31
 */


namespace Iresults\Core\Unit\Command;


use Iresults\Core\Command\Table;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function renderWithColorsTest()
    {
        $_SERVER['TERM'] = 'a-good terminal';

        $output = (new Table())->render($this->getTestData());
        $expected = $this->expectedOutput();

        $this->assertSame(549, strlen($output));
        $this->assertSame(substr_count($expected, '|'), substr_count($output, '|'));
    }

    /**
     * @test
     */
    public function renderWithoutColorsTest()
    {
        unset($_SERVER['TERM']);

        $output = (new Table())->render($this->getTestData());
        $expected = $this->expectedOutput();

        $this->assertSame(strlen($expected), strlen($output));
        $this->assertSame(substr_count($expected, '|'), substr_count($output, '|'));

        $this->assertSame($expected, $output);
    }

    /**
     * @test
     */
    public function renderWitSeparatorTest()
    {
        unset($_SERVER['TERM']);

        $output = (new Table())->render($this->getTestData(), PHP_INT_MAX, false, '#');
        $expected = $this->expectedOutput();

        $this->assertSame(strlen($expected), strlen($output));
        $this->assertSame(substr_count($expected, '|'), substr_count($output, '#'));

        $this->assertSame(str_replace('|', '#', $expected), $output);
    }

    private function getTestData()
    {
        $data = <<<TEST_DATA
            [{"id":1,"first_name":"Raymond","last_name":"Rodriguez","email":"rrodriguez0@si.edu","gender":"Male","ip_address":"213.76.135.143"},
{"id":2,"first_name":"Michelle","last_name":"Turner","email":"mturner1@mediafire.com","gender":"Female","ip_address":"24.196.215.163"},
{"id":3,"first_name":"William","last_name":"Burke","email":"wburke2@nhs.uk","gender":"Male","ip_address":"22.31.214.205"},
{"id":4,"first_name":"Lois","last_name":"Willis","email":"lwillis3@youku.com","gender":"Female","ip_address":"68.111.41.71"},
{"id":5,"first_name":"Judith","last_name":"Hall","email":"jhall4@etsy.com","gender":"Female","ip_address":"52.29.162.163"}]
TEST_DATA;

        return json_decode($data, true);
    }

    /**
     * @return string
     */
    private function expectedOutput()
    {
        $expected = <<<EXPECTED

| id | first_name | last_name | email                  | gender | ip_address     |
| 1  | Raymond    | Rodriguez | rrodriguez0@si.edu     | Male   | 213.76.135.143 |
| 2  | Michelle   | Turner    | mturner1@mediafire.com | Female | 24.196.215.163 |
| 3  | William    | Burke     | wburke2@nhs.uk         | Male   | 22.31.214.205  |
| 4  | Lois       | Willis    | lwillis3@youku.com     | Female | 68.111.41.71   |
| 5  | Judith     | Hall      | jhall4@etsy.com        | Female | 52.29.162.163  |

EXPECTED;

        return $expected;
    }
}

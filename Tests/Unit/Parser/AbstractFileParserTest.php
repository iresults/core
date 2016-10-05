<?php
/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
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
 * Created 22.10.13 14:59
 */


namespace Iresults\Core\Tests\Unit\Parser;


require_once __DIR__ . '/../Autoloader.php';

use Iresults\Core\Parser\AbstractFileParser;

class AbstractFileParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractFileParser
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = $this->getMockForAbstractClass(AbstractFileParser::class);
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Parser\Exception\ParserInvalidInputException
     * @expectedExceptionCode 1475670705
     */
    public function shouldThrowOnEmptyInputTest()
    {
        $this->fixture->parse('');
    }

    /**
     * @test
     * @expectedException \Iresults\Core\Parser\Exception\ParserInvalidInputException
     * @expectedExceptionCode 1475670706
     */
    public function shouldThrowOnInvalidFileInputTest()
    {
        $this->fixture->parse('/file/not/exists');
    }
}
<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 22.10.13 14:59
 */


namespace Iresults\Core\Tests\Unit\Parser;
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

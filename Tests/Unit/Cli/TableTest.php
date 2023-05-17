<?php

namespace Iresults\Core\Unit\Cli;
use Iresults\Core\Cli\Table;
use Iresults\Core\DataObject;

class TableTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        unset($_SERVER['TERM']);
    }

    /**
     * @test
     */
    public function renderWithoutHeaderTest()
    {
        $_SERVER['TERM'] = 'a-good terminal';

        $output = (new Table())->render(
            $this->getTestDataArrayCollection(),
            Table::HEADER_POSITION_NONE,
            '|',
            PHP_INT_MAX
        );
        $this->assertColoredOutputWithoutHeader($output);
    }


    /**
     * @test
     */
    public function renderWithColorsTest()
    {
        $_SERVER['TERM'] = 'a-good terminal';

        $output = (new Table())->render($this->getTestDataArrayCollection());
        $this->assertColoredOutputHeaderTop($output);
    }

    /**
     * @test
     */
    public function renderWithColorsHeaderLeftTest()
    {
        $_SERVER['TERM'] = 'a-good terminal';

        $output = (new Table())->render(
            $this->getTestDataArrayCollection(),
            Table::HEADER_POSITION_LEFT,
            '|',
            PHP_INT_MAX
        );
        $this->assertColoredOutputHeaderLeft($output);
    }

    /**
     * @test
     */
    public function renderWithoutColorsHeaderLeftTest()
    {
        $output = (new Table())->render(
            $this->getTestDataArrayCollection(),
            Table::HEADER_POSITION_LEFT,
            '|',
            PHP_INT_MAX
        );
        $this->assertSame($this->expectedOutputWithoutHeader(), $output);
    }

    /**
     * @test
     */
    public function renderWithColorsManuallyDisabledTest()
    {
        $_SERVER['TERM'] = 'a-good terminal';

        $table = new Table();
        $table->setUseColors(false);
        $output = $table->render($this->getTestDataArrayCollection());
        $this->assertOutput($output);
    }

    /**
     * @test
     */
    public function renderWithColorsManuallyEnabledTest()
    {
        $table = new Table();
        $table->setUseColors(true);
        $output = $table->render($this->getTestDataArrayCollection());
        $this->assertColoredOutputHeaderTop($output);
    }

    /**
     * @test
     */
    public function renderWithoutColorsTest()
    {
        $output = (new Table())->render($this->getTestDataArrayCollection());
        $this->assertOutput($output);
    }

    /**
     * @test
     */
    public function renderWithObjectsTest()
    {
        $output = (new Table())->render($this->getTestDataObjectCollection());
        $this->assertOutput($output);
    }

    /**
     * @test
     */
    public function renderWithDataObjectsTest()
    {
        $output = (new Table())->render($this->getTestDataDataObjectCollection());
        $this->assertOutput($output);
    }

    /**
     * @test
     */
    public function renderEmptyInputTest()
    {
        $this->assertSame('', (new Table())->render([]));
        $this->assertSame('|' . PHP_EOL . '|' . PHP_EOL, (new Table())->render([[]]));
        $this->assertNotSame('', (new Table())->render([[], [1]]));
    }

    /**
     * @test
     */
    public function renderTinyInputTest()
    {
        $this->assertSame('', (new Table())->render([]));
        $this->assertSame('|' . PHP_EOL . '|' . PHP_EOL, (new Table())->render([[]]));

        $expected = <<<EXPECTED
|   |
|   |
| 1 |

EXPECTED;
        $this->assertSame($expected, (new Table())->render([[], [1]]));
    }

    /**
     * @test
     */
    public function renderWithSeparatorTest()
    {
        $output = (new Table())->render(
            $this->getTestDataArrayCollection(),
            Table::HEADER_POSITION_TOP,
            '#',
            PHP_INT_MAX
        );
        $expected = $this->expectedOutput();

        $this->assertSame(str_replace('|', '#', $expected), $output);
    }

    /**
     * @test
     */
    public function stripMultiLineTest()
    {
        $output = (new Table())->render(
            [
                [
                    "uid\n\rPerson" => "12",
                    "message"       => "Hello\nI am Daniel\n\rI wrote this library",
                ],
            ]
        );

        $expected = <<<WITHOUT_NEWLINES
| uid Person | message                                |
| 12         | Hello I am Daniel I wrote this library |

WITHOUT_NEWLINES;
        $this->assertSame($expected, $output);
    }

    /**
     * @test
     */
    public function unicodeTest()
    {
        $output = (new Table())->render(
            [
                [
                    'uid'     => 1,
                    'message' => 'Comment ça va ?',
                ],
                [
                    'uid'     => 2,
                    'message' => 'Vielen Dank für deine Nachricht!',
                ],
            ]
        );

        $expected = <<<WITHOUT_NEWLINES
| uid | message                          |
| 1   | Comment ça va ?                  |
| 2   | Vielen Dank für deine Nachricht! |

WITHOUT_NEWLINES;
        $this->assertSame($expected, $output);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1475842895
     */
    public function throwForInvalidDataTest()
    {
        (new Table())->render(new \stdClass());
    }

    private function getTestDataArrayCollection()
    {
        return json_decode($this->getTestDataString(), true);
    }

    private function getTestDataDataObjectCollection()
    {
        return array_map(
            function ($row) {
                return new DataObject($row);
            },
            $this->getTestDataArrayCollection()
        );
    }

    private function getTestDataObjectCollection()
    {
        return json_decode($this->getTestDataString(), false);
    }

    /**
     * @return string
     */
    private function expectedOutput()
    {
        $expected = <<<EXPECTED
| 1  | Raymond    | Rodriguez | rrodriguez0@si.edu     | Male   | 213.76.135.143 |
| 2  | Michelle   | Turner    | mturner1@mediafire.com | Female | 24.196.215.163 |
| 3  | William    | Burke     | wburke2@nhs.uk         | Male   | 22.31.214.205  |
| 4  | Lois       | Willis    | lwillis3@youku.com     | Female | 68.111.41.71   |
| 5  | Judith     | Hall      | jhall4@etsy.com        | Female | 52.29.162.163  |

EXPECTED;

        return $this->expectedOutputHeader() . PHP_EOL . $expected;
    }

    /**
     * @return string
     */
    private function expectedOutputHeader()
    {
        $expected = <<<EXPECTED
| id | first_name | last_name | email                  | gender | ip_address     |
EXPECTED;

        return $expected;
    }

    /**
     * @return string
     */
    private function expectedOutputWithoutHeader()
    {
        $expected = <<<EXPECTED
| 1 | Raymond  | Rodriguez | rrodriguez0@si.edu     | Male   | 213.76.135.143 |
| 2 | Michelle | Turner    | mturner1@mediafire.com | Female | 24.196.215.163 |
| 3 | William  | Burke     | wburke2@nhs.uk         | Male   | 22.31.214.205  |
| 4 | Lois     | Willis    | lwillis3@youku.com     | Female | 68.111.41.71   |
| 5 | Judith   | Hall      | jhall4@etsy.com        | Female | 52.29.162.163  |

EXPECTED;

        return $expected;
    }

//    /**
//     * @return string
//     */
//    private function expectedOutputRotated()
//    {
//        $expected = <<<EXPECTED
//| id         | 1                  | 2                         | 3              | 4                  | 5               |
//| first_name | Raymond            | Michelle                  | William        | Lois               | Judith          |
//| last_name  | Rodriguez          | Turner                    | Burke          | Willis             | Hall            |
//| email      | rrodriguez0@si.edu |  mturner1@mediafire.com   | wburke2@nhs.uk | lwillis3@youku.com | jhall4@etsy.com |
//| gender     | Male               | Female                    | Male           | Female             | Female          |
//| ip_address | 213.76.135.143     | 24.196.215.163            | 22.31.214.205  | 68.111.41.71       | 52.29.162.163   |
//
//EXPECTED;
//
//        return $expected;
//    }

    /**
     * @return string
     */
    private function getTestDataString()
    {
        return <<<TEST_DATA
            [{"id":1,"first_name":"Raymond","last_name":"Rodriguez","email":"rrodriguez0@si.edu","gender":"Male","ip_address":"213.76.135.143"},
{"id":2,"first_name":"Michelle","last_name":"Turner","email":"mturner1@mediafire.com","gender":"Female","ip_address":"24.196.215.163"},
{"id":3,"first_name":"William","last_name":"Burke","email":"wburke2@nhs.uk","gender":"Male","ip_address":"22.31.214.205"},
{"id":4,"first_name":"Lois","last_name":"Willis","email":"lwillis3@youku.com","gender":"Female","ip_address":"68.111.41.71"},
{"id":5,"first_name":"Judith","last_name":"Hall","email":"jhall4@etsy.com","gender":"Female","ip_address":"52.29.162.163"}]
TEST_DATA;
    }

    /**
     * @param $output
     */
    private function assertOutput($output)
    {
        $this->assertSame($this->expectedOutput(), $output);
    }

    /**
     * @param $output
     */
    private function assertColoredOutputHeaderTop($output)
    {
        $expected = $this->expectedOutput();

        $this->assertSame(776, strlen($output));
        $this->assertSame(substr_count($expected, '|'), substr_count($output, '|'));
    }

    /**
     * @param $output
     */
    private function assertColoredOutputHeaderLeft($output)
    {
        $expected = $this->expectedOutputWithoutHeader();

        $this->assertSame(630, strlen($output));
        $this->assertSame(substr_count($expected, '|'), substr_count($output, '|'));
    }

    /**
     * @param $output
     */
    private function assertColoredOutputWithoutHeader($output)
    {
        $expected = $this->expectedOutputWithoutHeader();

        $this->assertSame(622, strlen($output));
        $this->assertSame(substr_count($expected, '|'), substr_count($output, '|'));
    }
}

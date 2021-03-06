<?php

namespace Iresults\Core\Ui;

use Iresults\Core\Cli\Table as CliTable;
use Iresults\Core\Core;
use Iresults\Core\Iresults;

/**
 * Displays given input data in a simple table
 */
class Table extends Core
{
    /**
     * The (prepared) table data that will be displayed
     *
     * @var array
     */
    protected $data = [];

    /**
     * The table header row
     *
     * @var array
     */
    protected $headerRow;

    /**
     * Defines if the first row contains the column headers
     *
     * @var bool
     */
    protected $useFirstRowAsHeaderRow = false;

    /**
     * Defines if the table should be colored
     *
     * @var bool
     */
    protected $useColors = true;

    /**
     * Saves the number of tables the view helper rendered
     *
     * @var integer
     */
    static protected $tableCounter = 0;

    /**
     * @param array|\Traversable $data
     */
    function __construct($data = null)
    {
        if ($data !== null) {
            $this->data = $this->prepareData($data);
        }
    }

    /**
     * Displays the rendered table
     *
     * @param array  $data       The data to output
     * @param string $tableClass The class of the table
     * @param string $rowClass   The class of the rows (TR-tags)
     * @param string $cellClass  The class of the cells (TD-tags)
     * @param string $headClass  The class of the header cells (TH-tags)
     * @return    string The rendered table
     */
    public function display($data = null, $tableClass = 'list', $rowClass = '', $cellClass = '', $headClass = '')
    {
        $output = $this->render($data, $tableClass, $rowClass, $cellClass, $headClass);
        Iresults::say($output);

        return $output;
    }

    /**
     * Returns the rendered table
     *
     * @param array  $data       The data to output
     * @param string $tableClass The class of the table
     * @param string $rowClass   The class of the rows (TR-tags)
     * @param string $cellClass  The class of the cells (TD-tags)
     * @param string $headClass  The class of the header cells (TH-tags)
     * @return    string The rendered table
     */
    public function render($data = null, $tableClass = 'list', $rowClass = '', $cellClass = '', $headClass = '')
    {
        if (Iresults::getEnvironment() === Iresults::ENVIRONMENT_SHELL) {
            return $this->renderShell($data, $tableClass, $rowClass, $cellClass, $headClass);
        }

        return $this->renderHtml($data, $tableClass, $rowClass, $cellClass, $headClass);
    }

    /**
     * Returns the rendered HTML table.
     *
     * @param array  $data       The data to output
     * @param string $tableClass The class of the table
     * @param string $rowClass   The class of the rows (TR-tags)
     * @param string $cellClass  The class of the cells (TD-tags)
     * @param string $headClass  The class of the header cells (TH-tags)
     * @return    string The rendered table
     */
    public function renderHtml($data = null, $tableClass = 'list', $rowClass = '', $cellClass = '', $headClass = '')
    {
        $list = '';

        if ($data === null) {
            $data = $this->getData();
        }
        $data = $this->prepareData($data);
        $head = $this->getHeaderRow($data);

        // Create the html ID of the table
        self::$tableCounter++;
        $tableId = 'iresults_table_id' . self::$tableCounter;

        // Check whether to use the default styles
        if ($this->getUseColors() && $tableClass === 'list' && !$rowClass && !$cellClass && !$headClass) {
            $list .= "<style type='text/css'>
			#$tableId td,
			#$tableId th{
				border:solid 1px #ccc;
			}
			#$tableId tr.even {
				background:#F6FFCE;
			}
			#$tableId tr.odd {
				background:#F1FFAD;
			}
			#$tableId tr:hover {
				background:#EAFF82;
			}
			</style>";
        }

        $list .= "<table id='$tableId' class='$tableClass'><tr>";
        foreach ($head as $col) {
            $list .= "<th class='$headClass'>$col</th>";
        }
        $list .= '</tr>';


        $even = true;

        foreach ($data as $row) {
            if ($even) {
                $even = false;
                $zebra = 'odd';
            } else {
                $even = true;
                $zebra = 'even';
            }

            $list .= "<tr class='$rowClass $zebra'>";
            foreach ($row as $col) {
                $list .= "<td class='$cellClass'>$col</td>";
            }
            $list .= '</tr>';
        }
        $list .= '</table>';

        return $list;
    }

    /**
     * Returns the rendered table for terminal output.
     *
     * @param array   $data           The data to output
     * @param integer $maxColumnWidth Maximum column width
     * @param boolean $disableHead    Indicates if the head row should be rendered
     * @param string  $separator      The column separator
     * @return    string                        The rendered table
     */
    public function renderShell($data = null, $maxColumnWidth = PHP_INT_MAX, $disableHead = false, $separator = '|')
    {
        // If renderShell() is invoked from render() with the default arguments
        if ($maxColumnWidth === 'list') {
            $maxColumnWidth = PHP_INT_MAX;
        }

        if ($data === null) {
            $data = $this->getData();
        }
        $data = $this->prepareData($data);
        $headerPosition = $disableHead ? CliTable::HEADER_POSITION_NONE : CliTable::HEADER_POSITION_TOP;
        $cliTable = new CliTable();

        return $cliTable->render(
            $data,
            $headerPosition,
            $separator,
            $maxColumnWidth,
            $this->getUseFirstRowAsHeaderRow()
        );
    }

    /**
     * Sets if the first row contains the column headers
     *
     * @param boolean $useFirstRowAsHeaderRow
     */
    public function setUseFirstRowAsHeaderRow($useFirstRowAsHeaderRow)
    {
        $this->useFirstRowAsHeaderRow = $useFirstRowAsHeaderRow;
    }

    /**
     * Returns if the first row contains the column headers
     *
     * @return boolean
     */
    public function getUseFirstRowAsHeaderRow()
    {
        return $this->useFirstRowAsHeaderRow;
    }

    /**
     * Returns if the table should be colored
     *
     * @return boolean
     */
    public function getUseColors()
    {
        return $this->useColors;
    }

    /**
     * Set if the table should be colored
     *
     * @param boolean $useColors
     */
    public function setUseColors($useColors)
    {
        $this->useColors = $useColors;
    }

    /**
     * Returns the header from the given input data
     *
     * @param array $data Reference to the array data
     * @return array
     */
    protected function getHeaderRow(&$data)
    {
        // Make sure the first row is an array
        $firstRow = reset($data);
        if (!is_array($firstRow)) {
            $firstRow = iterator_to_array($firstRow);
        }
        $header = array_keys($firstRow);
        if ($this->useFirstRowAsHeaderRow) {
            array_shift($data);

            return $firstRow;
        }

        return $header;
    }

    /**
     * Returns the data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns it the given array is an associative array or dictionary
     *
     * @param $testArray
     * @return bool
     */
    protected function arrayIsAssociative($testArray)
    {
        return array_keys($testArray) !== range(0, count($testArray) - 1);
    }

    /**
     * Prepares the input data
     *
     * @param mixed $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (!is_array($data) && ($data instanceof \Traversable)) {
            $data = iterator_to_array($data);
        }

        $firstRow = reset($data);
        if (!is_array($firstRow) && !($firstRow instanceof \Traversable)) {
            $data = [$data];
        }

        return $data;
    }

    /**
     * Returns a new instance with the given data
     *
     * @param array $data
     * @return static
     */
    static public function tableWithData($data)
    {
        return new static($data);
    }
}
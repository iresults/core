<?php
/**
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *            Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

namespace Iresults\Core\Command;

use Iresults\Core\Tools\StringTool;

/**
 * Displays given input data in a simple table
 */
class Table
{
    /**
     * Defines if the table should be colored
     *
     * @var bool
     */
    private $useColors;

    /**
     * @var int
     */
    private $tableColumnCount;

    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->useColors = (isset($_SERVER['TERM']) && trim(isset($_SERVER['TERM'])));
    }

    /**
     * Returns the rendered table for terminal output
     *
     * @param array|\Traversable $data                   The data to output
     * @param int                $maxColumnWidth         Maximum column width
     * @param boolean            $disableHead            Indicates if the head row should be rendered
     * @param string             $separator              The column separator
     * @param bool               $firstRowContainsHeader Defines if the first row contains the column headers
     * @return string The rendered table
     */
    public function render(
        $data,
        $maxColumnWidth = PHP_INT_MAX,
        $disableHead = false,
        $separator = '|',
        $firstRowContainsHeader = false
    ) {
        if (!$data) {
            return '';
        }


        $data = $this->prepareData($data);
        $head = $this->getHeaderRow($data, $firstRowContainsHeader);

        if (!$head && !$data) {
            return '';
        }

        $list = '';
        $columnWidths = $this->calculateColumnWidthsAndTableColumnCount($data, $maxColumnWidth, $disableHead, $head);

        if (!$disableHead) {
            $list = $this->renderHead($head, $separator, $columnWidths);
        }

        $even = true;
        foreach ($data as $row) {
            $list .= $this->renderRow($row, $separator, $columnWidths, $even);
            $even = !$even;
        }

        return $list;
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
     * @param array $data                   Reference to the array data
     * @param bool  $firstRowContainsHeader Defines if the first row contains the column headers
     * @return array
     */
    private function getHeaderRow(&$data, $firstRowContainsHeader)
    {
        if ($firstRowContainsHeader) {
            return array_shift($data);
        }

        // Make sure the first row is an array
        $firstRow = reset($data);
        if (!is_array($firstRow)) {
            $firstRow = iterator_to_array($firstRow);
        }

        return array_keys($firstRow);
    }

    /**
     * Prepares the input data
     *
     * @param mixed $data
     * @return array
     */
    private function prepareData($data)
    {
        if (!is_array($data) && ($data instanceof \Traversable)) {
            $data = iterator_to_array($data);
        }

        $firstRow = reset($data);
        if (!is_array($firstRow) && !($firstRow instanceof \Traversable)) {
            $data = array($data);
        }

        return $data;
    }

    /**
     * Calculate the column widths
     *
     * @param array $data
     * @param int   $maxColumnWidth
     * @param bool  $disableHead
     * @param array $head
     * @return int[]
     */
    private function calculateColumnWidthsAndTableColumnCount(array $data, $maxColumnWidth, $disableHead, $head)
    {
        $columnWidths = array();
        $columnCount = count($head);
        $tempTableColumnCount = $columnCount;

        if ($disableHead) {
            $columnWidths = array_fill(0, $columnCount - 1, 1);
        } else {
            for ($i = 0; $i < $columnCount; $i++) {
                $currentColumnWidth = strlen(utf8_decode($head[$i]));
                $columnWidths[] = $currentColumnWidth > $maxColumnWidth ? $maxColumnWidth : $currentColumnWidth;
            }
        }

        foreach ($data as $row) {
            $indexedRow = array_values($row);
            $columnCount = count($indexedRow);

            // Check if a new table column count is reached
            if ($tempTableColumnCount < $columnCount) {
                $tempTableColumnCount = $columnCount;
            }

            for ($i = 0; $i < $columnCount; $i++) {
                $storedColumnWidth = isset($columnWidths[$i]) ? $columnWidths[$i] : 0;
                $columnStringLength = strlen(utf8_decode($indexedRow[$i]));

                if ($storedColumnWidth < $columnStringLength) {
                    $currentColumnWidth = $columnStringLength;
                    $columnWidths[$i] = $currentColumnWidth > $maxColumnWidth ? $maxColumnWidth : $currentColumnWidth;;
                }
            }
        }

        $this->tableColumnCount = $tempTableColumnCount;

        return $columnWidths;
    }

    /**
     * @param $head
     * @param $separator
     * @param $columnWidths
     * @return string
     */
    private function renderHead($head, $separator, $columnWidths)
    {
        $useColors = $this->getUseColors();

        $list = PHP_EOL;
        if ($useColors) {
            $list .= ColorInterface::ESCAPE . ColorInterface::REVERSE;
        }

        $tableColumnCount = $this->tableColumnCount;
        for ($i = 0; $i < $tableColumnCount; $i++) {
            $col = isset($head[$i]) ? $head[$i] : '';
            $columnWidth = $columnWidths[$i];

            if (is_array($col)) {
                $col = reset($col);
            }
            $col = (string)$col;

            if (strlen(utf8_decode($col)) > $columnWidth) {
                $col = substr($col, 0, $columnWidth);
            }
            // Add spaces to fill the cell to the needed length
            $list .= $separator . ' ' . StringTool::pad($col, $columnWidth, ' ') . ' ';
        }

        // Final separator
        if ($useColors) {
            $list .= $separator . ColorInterface::SIGNAL_ATTRIBUTES_OFF . PHP_EOL;
        } else {
            $list .= $separator . PHP_EOL;
        }

        return $list;
    }

    /**
     * @param $row
     * @param $separator
     * @param $columnWidths
     * @param $even
     * @return string
     */
    private function renderRow($row, $separator, $columnWidths, $even)
    {
        $useColors = $this->getUseColors();
        $list = '';
        if (!$even && $useColors) {
            $list .= ColorInterface::ESCAPE . ColorInterface::GRAY . ColorInterface::ESCAPE . ColorInterface::REVERSE;
        }

        $indexedRow = array_values($row);
        $tableColumnCount = $this->tableColumnCount;

        for ($i = 0; $i < $tableColumnCount; $i++) {
            $col = isset($indexedRow[$i]) ? $indexedRow[$i] : '';
            $columnWidth = $columnWidths[$i];

            if (is_array($col)) {
                $col = reset($col);
            }
            $col = (string)$col;

            // Add spaces to fill the cell to the needed length
            if (strlen($col) > $columnWidth) {
                $col = substr($col, 0, $columnWidth - 1) . '…';
            }
            $list .= $separator . ' ' . StringTool::pad($col, $columnWidth, ' ') . ' ';
        }

        if ($useColors) {
            $list .= $separator . ColorInterface::SIGNAL_ATTRIBUTES_OFF . PHP_EOL;

        } else {
            $list .= $separator . PHP_EOL;

        }

        return $list;
    }
}

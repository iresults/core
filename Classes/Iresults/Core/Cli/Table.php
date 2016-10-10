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

namespace Iresults\Core\Cli;

use Iresults\Core\Tools\StringTool;

/**
 * Displays given input data in a simple table
 */
class Table
{
    const HEADER_POSITION_NONE = 0;
    const HEADER_POSITION_TOP = 1;
    const HEADER_POSITION_LEFT = 2;
    const STYLE_HEADER_ROW = ColorInterface::ESCAPE . ColorInterface::REVERSE;
    const STYLE_ODD_ROW = ColorInterface::ESCAPE . ColorInterface::GRAY . ColorInterface::ESCAPE . ColorInterface::REVERSE;

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
     * @param int                $headerPosition         Defines the position of the header
     * @param string             $separator              The column separator
     * @param bool               $firstRowContainsHeader Defines if the first row contains the column headers
     * @return string The rendered table
     */
    public function render(
        $data,
        $maxColumnWidth = PHP_INT_MAX,
        $headerPosition = self::HEADER_POSITION_TOP,
        $separator = '|',
        $firstRowContainsHeader = false
    ) {
        if (!$data) {
            return '';
        }

        $preparedData = $this->prepareData($data);
        $headerRowData = [];
        if ($headerPosition === self::HEADER_POSITION_TOP) {
            $headerRowData = $this->getHeaderRow($preparedData, $firstRowContainsHeader);
        }

        if (!$preparedData) {
            return '';
        }

        $list = '';
        $columnWidths = $this->calculateColumnWidthsAndTableColumnCount(
            $data,
            $maxColumnWidth,
            $headerPosition,
            $headerRowData
        );

        if ($headerPosition === self::HEADER_POSITION_TOP) {
            $list = $this->renderHeaderRow($headerRowData, $separator, $columnWidths);
        }

        $even = true;
        foreach ($preparedData as $row) {
            $list .= $this->renderRow($row, $separator, $columnWidths, $even, $headerPosition);
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
            $firstRow = $this->transformRowToDictionary($firstRow);
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
        if (is_array($data)) {
            return $data;
        } elseif ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new \InvalidArgumentException(
            sprintf('Data must be traversable, %s given', is_object($data) ? get_class($data) : gettype($data)),
            1475842895
        );
    }

    /**
     * Calculate the column widths
     *
     * @param array $data
     * @param int   $maxColumnWidth
     * @param bool  $headerPosition
     * @param array $head
     * @return \int[]
     */
    private function calculateColumnWidthsAndTableColumnCount(array $data, $maxColumnWidth, $headerPosition, $head)
    {
        $columnWidths = array();
        $tempTableColumnCount = 0;

        $dataForCalculation = $data;
        if ($headerPosition === self::HEADER_POSITION_TOP) {
            array_unshift($dataForCalculation, $head);
        }

        foreach ($dataForCalculation as $row) {
            $indexedRow = array_values($this->transformRowToDictionary($row));
            $columnCount = count($indexedRow);

            // Check if a new table column count is reached
            if ($tempTableColumnCount < $columnCount) {
                $tempTableColumnCount = $columnCount;
            }

            for ($i = 0; $i < $columnCount; $i++) {
                if (!isset($indexedRow[$i])) {
                    continue;
                }
                $storedColumnWidth = isset($columnWidths[$i]) ? $columnWidths[$i] : 0;
                $cellValueAsString = $this->transformCellToString($indexedRow[$i]);
                $columnStringLength = strlen(utf8_decode($cellValueAsString));

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
    private function renderHeaderRow($head, $separator, $columnWidths)
    {
        $row = $this->renderHeaderRowCells($head, $separator, $columnWidths);

        return $this->wrapRowOutput($row, $separator, self::STYLE_HEADER_ROW);
    }

    private function wrapRowOutput($row, $separator, $style)
    {
        if (!$this->getUseColors()) {
            return $separator . $row . PHP_EOL;
        }

        return ''
        . $style . $separator . ColorInterface::SIGNAL_ATTRIBUTES_OFF
        . $row
        . PHP_EOL;
    }

    /**
     * @param $row
     * @param $separator
     * @param $columnWidths
     * @param $even
     * @param $headerPosition
     * @return string
     */
    private function renderRow($row, $separator, $columnWidths, $even, $headerPosition)
    {
        $row = $this->renderRowCells(
            $this->transformRowToArray($row),
            $separator,
            $columnWidths,
            $headerPosition,
            $even
        );

        $style = '';
        if ($headerPosition === self::HEADER_POSITION_LEFT) {
            $style = self::STYLE_HEADER_ROW;
        } elseif ($even === false) {
            $style = self::STYLE_ODD_ROW;
        }

        return $this->wrapRowOutput($row, $separator, $style);
    }

    /**
     * @param array  $row
     * @param int    $columnPosition
     * @param string $separator
     * @param int[]  $columnWidths
     * @param bool   $style
     * @return string
     */
    private function renderCell($row, $columnPosition, $separator, $columnWidths, $style)
    {
        $columnWidth = $columnWidths[$columnPosition];

        if (isset($row[$columnPosition])) {
            $col = $this->transformCellToString($row[$columnPosition]);
            if (strlen(utf8_decode($col)) > $columnWidth) {
                $col = substr($col, 0, $columnWidth - 1) . '…';
            }
        } else {
            $col = '';
        }

        // Add spaces to fill the cell to the needed length
        $rowContent = ' ' . StringTool::pad($col, $columnWidth, ' ') . ' ' . $separator;

        if ($style && $this->getUseColors()) {
            return $style . $rowContent . ColorInterface::SIGNAL_ATTRIBUTES_OFF;
        }

        return $rowContent;
    }

    /**
     * @param mixed $input
     * @return array
     */
    private function transformRowToDictionary($input)
    {
        if (is_array($input)) {
            return $input;
        } elseif ($input instanceof \Traversable) {
            return iterator_to_array($input);
        } elseif (is_object($input)) {
            return method_exists($input, 'toArray') ? $input->toArray() : get_object_vars($input);
        }

        return (array)$input;
    }

    /**
     * @param mixed $input
     * @return array
     */
    private function transformRowToArray($input)
    {
        return array_values($this->transformRowToDictionary($input));
    }

    /**
     * @param mixed $input
     * @return string
     */
    private function transformCellToString($input)
    {
        if (is_scalar($input)) {
            return (string)$input;
        } elseif (is_array($input)) {
            return implode(',', $input);
        } elseif ($input instanceof \Traversable) {
            return implode(',', iterator_to_array($input));
        } elseif (is_object($input)) {
            return method_exists($input, '__toString') ? (string)$input : get_class($input);
        }

        return (string)$input;
    }

    /**
     * @param $row
     * @param $separator
     * @param $columnWidths
     * @param $headerPosition
     * @param $even
     * @return string
     */
    private function renderRowCells($row, $separator, $columnWidths, $headerPosition, $even)
    {
        $tableColumnCount = $this->tableColumnCount;
        $isHeader = $headerPosition === self::HEADER_POSITION_LEFT;
        $cells = array_fill(0, $tableColumnCount, '');
        for ($i = 0; $i < $tableColumnCount; $i++) {
            $style = $isHeader ? self::STYLE_HEADER_ROW : ($even ? null : self::STYLE_ODD_ROW);
            $cells[$i] = $this->renderCell($row, $i, $separator, $columnWidths, $style);

            // Following cells should not be rendered as header
            $isHeader = false;
        }

        return implode('', $cells);
    }

    private function renderHeaderRowCells($row, $separator, $columnWidths)
    {
        $tableColumnCount = $this->tableColumnCount;
        $cells = array_fill(0, $tableColumnCount, '');
        for ($i = 0; $i < $tableColumnCount; $i++) {
            $cells[$i] = $this->renderCell($row, $i, $separator, $columnWidths, self::STYLE_HEADER_ROW);
        }

        return implode('', $cells);
    }
}

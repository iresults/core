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
 * Created 10.10.16 13:55
 */


namespace Iresults\Core\Cli\Table;


use Iresults\Core\Cli\Table;

class CellFormatter implements CellFormatterInterface
{
    /**
     * Formats the given cell data
     *
     * @param mixed $data
     * @param Table $table
     * @return string
     */
    public function formatCellData($data, Table $table)
    {
        return $this->removeNewLines($this->transformDataToString($data));
    }

    /**
     * Transform the data into a string
     *
     * @param mixed $data
     * @return string
     */
    protected function transformDataToString($data)
    {
        if (is_scalar($data)) {
            return (string)$data;
        } elseif (is_array($data)) {
            return implode(',', $data);
        } elseif ($data instanceof \Traversable) {
            return implode(',', iterator_to_array($data));
        } elseif ($data instanceof \DateTimeInterface) {
            return $data->format('r');
        } elseif (is_object($data)) {
            return method_exists($data, '__toString') ? (string)$data : get_class($data);
        }

        return (string)$data;
    }

    /**
     * @param string $input
     * @return string
     */
    protected function removeNewLines($input)
    {
        $parts = array();
        $separator = "\r\n";
        $line = strtok($input, $separator);

        while ($line !== false) {
            $parts[] = trim($line);
            $line = strtok($separator);
        }

        return implode(' ', $parts);
    }
}
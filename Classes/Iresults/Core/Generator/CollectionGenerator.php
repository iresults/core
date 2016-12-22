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
 * @author Daniel Corn <cod@iresults.li>
 * Created 05.10.16 17:19
 */


namespace Iresults\Core\Generator;


use Iresults\Core\Parser\CsvFileParser;

/**
 *
 */
class CollectionGenerator
{
    /**
     * Create a collection of object with each entry in the given CSV file
     *
     * The callback will be invoked for each row from row two. The first row in the file will be used as keys for entry
     *
     * @param string   $url
     * @param callable $callback
     * @return array
     */
    public static function collectionFromCsvUrlWithCallback($url, callable $callback)
    {
        $parser = new CsvFileParser();
        $data = $parser->parse($url);
        if (!$data) {
            return [];
        }
        if (count($data) < 2) { // File only contains the header
            return [];
        }

        $header = array_shift($data);
        $headerColumnCount = count($header);
        $collection = [];
        foreach ($data as $row) {
            $columnCount = count($row);
            if ($headerColumnCount > $columnCount) {
                $row = array_pad($row, $headerColumnCount, null);
            }

            $collection[] = $callback(
                array_combine(
                    $header,
                    $row
                )
            );
        }

        return $collection;
    }
}

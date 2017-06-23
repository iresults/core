<?php
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

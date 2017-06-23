<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 05.10.16 14:20
 */


namespace Iresults\Core\DataObject;

use Iresults\Core\DataObject;
use Iresults\Core\Parser\CsvFileParser;

class Factory
{
    /**
     * Create a collection of Data Objects with each entry in the given CSV file
     *
     * The first row in the file will be used as keys for each DataObject
     *
     * @param string $url
     * @return DataObject[]
     */
    public static function collectionFromCsvUrl($url)
    {
        return \Iresults\Core\Generator\CollectionGenerator::collectionFromCsvUrlWithCallback(
            $url,
            function ($data) {
                return new DataObject($data);
            }
        );
    }
}

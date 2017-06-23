<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 22.10.13 14:52
 */


namespace Iresults\Core\Parser;

use Iresults\Core\Parser\Exception\ParserInvalidInputException;

/**
 * A parser for CSV files
 *
 * @package Iresults\Core\Parser
 */
class CsvFileParser extends AbstractFileParser
{
    /**
     * Parses the given input
     *
     * @param string $input
     * @return array Returns the parsed data
     * @throws ParserInvalidInputException if the input could not be parsed
     */
    public function parse($input)
    {
        $this->validateFile($input);
        $data = null;
        $parsedData = [];
        $lineString = null;
        $oldIniValue = ini_set('auto_detect_line_endings', true);

        $fileHandle = @fopen($input, 'r');
        if ($fileHandle === false) {
            throw new ParserInvalidInputException('Could not load CSV file from "' . $input . '".', 1315232117);
        }

        $firstTwoLines = fgets($fileHandle) . fgets($fileHandle);
        rewind($fileHandle);
        list($delimiter, $enclosure, $escape) = $this->autoDetectMissingParserConfiguration($firstTwoLines);
        rewind($fileHandle);

        /*
         * fgetcsv() can handle multi line cells, but does not work without an
         * enclosure
         */
        if ($enclosure) {
            while (($data = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape)) !== false) {
                $parsedData[] = $data;
            }
        } else {
            while (($lineString = fgets($fileHandle)) !== false) {
                $data = str_getcsv($lineString, $delimiter, $enclosure, $escape);
                $parsedData[] = $data;
            }
        }
        fclose($fileHandle);
        ini_set('auto_detect_line_endings', $oldIniValue);

        return $parsedData;
    }

    /**
     * Tries to read the delimiter, enclosure and escape character from the
     * given line
     *
     * @param string $lineString
     * @return array Returns the delimiter, enclosure and escape characters in an array
     */
    protected function autoDetectMissingParserConfiguration($lineString)
    {
        $delimiter = $this->getConfigurationForKey('delimiter');
        $enclosure = $this->getConfigurationForKey('enclosure');
        $escape = $this->getConfigurationForKey('escape');

        if (!$enclosure && $lineString) {
            $firstCharacter = $lineString[0];
            $lastCharacter = substr(trim($lineString), -1);
            if (!is_numeric($firstCharacter) && !ctype_alpha($firstCharacter)) {
                $enclosure = $firstCharacter;
            } elseif (!is_numeric($lastCharacter) && !ctype_alpha($lastCharacter)) {
                $enclosure = $lastCharacter;
            }
            $this->configuration['enclosure'] = $enclosure;
        }
        if (!$delimiter) {
            $i = -1;
            $lastDelimiterPosition = 5000;
            $delimiterArray = [';', ',', ':', "\t"];

            $delimiterArrayCount = count($delimiterArray);

            while (++$i < $delimiterArrayCount) {
                $currentDelimiterPosition = strpos($lineString, $delimiterArray[$i]);
                if ($currentDelimiterPosition !== false && $currentDelimiterPosition < $lastDelimiterPosition) {
                    $lastDelimiterPosition = $currentDelimiterPosition;
                    $delimiter = $delimiterArray[$i];
                }
            }
            $this->configuration['delimiter'] = $delimiter;
        }
        if (!$escape) {
            $escape = '\\';
        }

        return [$delimiter, $enclosure, $escape];
    }
}
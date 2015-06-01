<?php
/*
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
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
 * Created 22.10.13 14:52
 */


namespace Iresults\Core\Parser;
use Iresults\Core\Parser\Exception\ParserInvalidInputException;

/**
 * A parser for CSV files
 *
 * @package Iresults\Core\Parser
 */
class CsvParser extends AbstractParser {
	/**
	 * Parses the given input
	 *
	 * @param mixed $input
	 * @return mixed Returns the parsed data
	 * @throws ParserInvalidInputException if the input could not be parsed
	 */
	public function parse($input) {
		$data = NULL;
		$parsedData = array();
		$lineString = NULL;
		$oldIniValue = ini_set('auto_detect_line_endings', TRUE);

		$fileHandle = @fopen($input, 'r');
		if ($fileHandle === FALSE) {
			throw new ParserInvalidInputException('Could not load CSV file from "' . $input . '".', 1315232117);
		}

        $firstTwoLines = fgets($fileHandle) . fgets($fileHandle);
		rewind($fileHandle);
		list($delimiter, $enclosure, $escape) = $this->autoDetectMissingParserConfiguration($firstTwoLines);	rewind($fileHandle);

		/*
		 * fgetcsv() can handle multi line cells, but does not work without an
		 * enclosure
		 */
		if ($enclosure) {
			while (($data = fgetcsv($fileHandle, 0, $delimiter, $enclosure, $escape)) !== FALSE) {
				$parsedData[] = $data;
			}
		} else {
			while (($lineString = fgets($fileHandle)) !== FALSE) {
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
	protected function autoDetectMissingParserConfiguration($lineString) {
		$delimiter = $this->getConfigurationForKey('delimiter');
		$enclosure = $this->getConfigurationForKey('enclosure');
		$escape = $this->getConfigurationForKey('escape');

		if (!$enclosure) {
			$firstCharacter = $lineString[0];
			$lastCharacter = substr(trim($lineString), -1);
			if (!is_numeric($firstCharacter) && !ctype_alpha($firstCharacter)) {
				$enclosure = $firstCharacter;
			} else if (!is_numeric($lastCharacter) && !ctype_alpha($lastCharacter)) {
				$enclosure = $lastCharacter;
			}
			$this->configuration['enclosure'] = $enclosure;
		}
		if (!$delimiter) {
			$i = -1;
			$lastDelimiterPosition = 5000;
			$delimiterArray = array(';', ',', ':', "\t");

			$delimiterArrayCount = count($delimiterArray);

			while (++$i < $delimiterArrayCount) {
				$currentDelimiterPosition = strpos($lineString, $delimiterArray[$i]);
				if ($currentDelimiterPosition !== FALSE && $currentDelimiterPosition < $lastDelimiterPosition) {
					$lastDelimiterPosition = $currentDelimiterPosition;
					$delimiter = $delimiterArray[$i];
				}
			}
			$this->configuration['delimiter'] = $delimiter;
		}
		if (!$escape) {
			$escape = '\\';
		}
		return array($delimiter, $enclosure, $escape);
	}
}
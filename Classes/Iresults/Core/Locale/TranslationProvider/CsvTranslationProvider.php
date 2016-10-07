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
 * @author Daniel Corn <cod@iresults.li>
 * Created 03.10.13 12:11
 */


namespace Iresults\Core\Locale\TranslationProvider;

/**
 * Translation Provider that reads the translations from CSV files
 *
 * @package Iresults\Core\Locale\TranslationProvider
 */
class CsvTranslationProvider extends AbstractFileBasedTranslationProvider
{
    /**
     * Parses the translation file at the given path
     *
     * @param string $translationFilePath
     * @throws InvalidTranslationFileException
     * @return array
     */
    protected function parseTranslationFile($translationFilePath)
    {
        $translationData = array();
        $line = 0;
        $lineString = null;
        $oldIniValue = ini_set('auto_detect_line_endings', true);
        static $maxLines = 100000;

        $fileHandle = @fopen($translationFilePath, 'r');
        if ($fileHandle === false) {
            throw new InvalidTranslationFileException(
                'Couldn\'t load CSV file from ' . $translationFilePath, 1329750849
            );
        }

        while (($lineString = fgets($fileHandle)) !== false && $line++ < $maxLines) {
            $row = str_getcsv($lineString);
            if (count($row) < 2) {
                throw new InvalidTranslationFileException(
                    'Error in input file ' . $translationFilePath . ': The row at line ' . $line . ' has less than two values',
                    1329750916
                );
            }
            $originalMessage = trim($row[0]);
            $translatedMessage = trim($row[1]);

            $translationData[$originalMessage] = $translatedMessage;
        }
        fclose($fileHandle);
        ini_set('auto_detect_line_endings', $oldIniValue);

        return $translationData;
    }
}
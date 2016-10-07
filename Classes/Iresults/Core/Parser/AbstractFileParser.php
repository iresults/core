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
 * Created 22.10.13 14:27
 */


namespace Iresults\Core\Parser;

use Iresults\Core\Parser\Exception\ParserInvalidInputException;

/**
 * Abstract file based parser class
 */
abstract class AbstractFileParser extends AbstractParser
{
    /**
     * Parses the given input
     *
     * @param mixed $input
     * @return mixed Returns the parsed data
     * @throws ParserInvalidInputException if the input could not be parsed
     */
    public function parse($input)
    {
        $this->validateFile($input);

        return null;
    }

    /**
     * Checks the input file
     *
     * @param string $file
     */
    protected function validateFile($file)
    {
        $file = trim($file);
        if ($file && is_readable($file)) {
            return;
        }

        if (!$file) {
            throw new ParserInvalidInputException('No input URI given', 1475670705);
        }
        if (!file_exists($file)) {
            throw new ParserInvalidInputException(sprintf('File "%s" does not exist', $file), 1475670706);
        }
        throw new ParserInvalidInputException(sprintf('File "%s" is not readable', $file), 1475670707);
    }
}
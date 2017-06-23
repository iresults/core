<?php
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
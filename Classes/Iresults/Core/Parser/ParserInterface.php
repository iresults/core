<?php
/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 22.10.13 14:35
 */


namespace Iresults\Core\Parser;

use Iresults\Core\Parser\Exception\ParserInvalidInputException;

/**
 * Interface for parsers
 *
 * @package Iresults\Core\Parser
 */
interface ParserInterface
{
    /**
     * Parses the given input
     *
     * @param mixed $input
     * @return mixed Returns the parsed data
     * @throws ParserInvalidInputException if the input could not be parsed
     */
    public function parse($input);

    /**
     * Set the configuration array
     *
     * @param array $configuration
     * @return $this
     */
    public function setConfiguration($configuration);
}
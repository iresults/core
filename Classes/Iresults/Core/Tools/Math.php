<?php

namespace Iresults\Core\Tools;

use Iresults\Core\Tools\Exception\DivisionByZeroException;
use Iresults\Core\Tools\Exception\MathException;

/**
 * Utility class for mathematical operations
 */
class Math
{
    /**
     * Defines the precision of calculations and comparison
     *
     * @var integer
     */
    static protected $_precision = 9;

    /**
     * Sets the precision of calculations and comparison
     *
     * @param int|\Iresults\Core\Tools\Math $precision
     */
    static public function setPrecision($precision)
    {
        self::$_precision = $precision;
    }

    /**
     * Returns the precision of calculations and comparison
     *
     * @return int|\Iresults\Core\Tools\Math
     */
    static public function getPrecision()
    {
        return self::$_precision;
    }

    /**
     * Perform an addition
     */
    const ADD = '+';

    /**
     * Perform a subtraction
     */
    const SUBTRACT = '-';

    /**
     * Perform a multiplication
     */
    const MULTIPLY = '*';

    /**
     * Perform a division
     */
    const DIVIDE = '/';

    /**
     * Perform the given calculations in the order they are given
     *
     * @param int|float|string|bool $operand1
     * @param string                $operation
     * @param int|float|string|bool $operand2
     * @throws \UnexpectedValueException if an invalid operation is detected
     * @return float
     * @internal
     */
    static public function calculate($operand1, $operation, $operand2)
    {
        $arguments = func_get_args();
        $argumentsCount = func_num_args();

        $operand1 = $arguments[0];
        for ($i = 1; $i < $argumentsCount; $i += 2) {
            $operation = $arguments[$i];
            $operand2 = $arguments[$i + 1];

            switch ($operation) {
                case self::ADD:
                    $operand1 = static::add($operand1, $operand2, false);
                    break;

                case self::SUBTRACT:
                    $operand1 = static::subtract($operand1, $operand2, false);
                    break;

                case self::MULTIPLY:
                    $operand1 = static::multiply($operand1, $operand2, false);
                    break;

                case self::DIVIDE:
                    $operand1 = static::divide($operand1, $operand2, false);
                    break;

                default:
                    throw new \UnexpectedValueException('Invalid operation ' . $operation, 1406299265);
            }
        }

        return $operand1;
    }

    /**
     * Add two arbitrary precision numbers
     *
     * @param int|float|string|bool $augend
     * @param int|float|string|bool $addend
     * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function add($augend, $addend, $returnString = false)
    {
        if (static::_useBCMath()) {
            $augend = static::_prepareValueForBCMath($augend, 'augend');
            $addend = static::_prepareValueForBCMath($addend, 'addend');
            $result = bcadd($augend, $addend, self::$_precision);
            if (!$returnString) {
                return floatval($result);
            }

            return $result;
        }

        return -1;
    }

    /**
     * Adds all given arbitrary precision numbers
     *
     * @param            array         <int|float|string|bool> $values If no array is given all arguments will be multiplied
     * @param bool|float $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function addAll($values, $returnString = false)
    {
        if (!is_array($values)) {
            $values = func_get_args();
        }
        $augend = array_shift($values);
        foreach ($values as $addend) {
            $augend = static::add($augend, $addend, $returnString);
        }

        return $augend;
    }

    /**
     * Subtract one arbitrary precision number from another
     *
     * @param int|float|string|bool $minuend
     * @param int|float|string|bool $subtrahend
     * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function subtract($minuend, $subtrahend, $returnString = false)
    {
        if (static::_useBCMath()) {
            $minuend = static::_prepareValueForBCMath($minuend, 'minuend');
            $subtrahend = static::_prepareValueForBCMath($subtrahend, 'subtrahend');
            $result = bcsub($minuend, $subtrahend, self::$_precision);
            if (!$returnString) {
                return floatval($result);
            }

            return $result;
        }

        return -1;
    }

    /**
     * Subtracts all given arbitrary precision numbers
     *
     * @param            array         <int|float|string|bool> $values If no array is given all arguments will be multiplied
     * @param bool|float $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function subtractAll($values, $returnString = false)
    {
        if (!is_array($values)) {
            $values = func_get_args();
        }

        $minuend = array_shift($values);
        foreach ($values as $subtrahend) {
            $minuend = static::subtract($minuend, $subtrahend, $returnString);
        }

        return $minuend;
    }

    /**
     * Multiply two arbitrary precision numbers
     *
     * @param int|float|string|bool $multiplicand
     * @param int|float|string|bool $multiplier
     * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function multiply($multiplicand, $multiplier, $returnString = false)
    {
        if (static::_useBCMath()) {
            $multiplicand = static::_prepareValueForBCMath($multiplicand, 'multiplicand');
            $multiplier = static::_prepareValueForBCMath($multiplier, 'multiplier');
            $result = bcmul($multiplicand, $multiplier, self::$_precision);
            if (!$returnString) {
                return floatval($result);
            }

            return $result;
        }

        return -1;
    }

    /**
     * Multiplies all given arbitrary precision numbers
     *
     * @param            array         <int|float|string|bool> $values If no array is given all arguments will be multiplied
     * @param bool|float $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function multiplyAll($values, $returnString = false)
    {
        if (!is_array($values)) {
            $values = func_get_args();
        }
        $multiplicand = array_shift($values);
        foreach ($values as $multiplier) {
            $multiplicand = static::multiply($multiplicand, $multiplier, $returnString);
        }

        return $multiplicand;
    }

    /**
     * Divide two arbitrary precision numbers
     *
     * @param int|float|string|bool $dividend
     * @param int|float|string|bool $divisor
     * @param bool                  $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function divide($dividend, $divisor, $returnString = false)
    {
        if (static::_useBCMath()) {
            $dividend = static::_prepareValueForBCMath($dividend, 'dividend');
            $divisor = static::_prepareValueForBCMath($divisor, 'divisor');
            $result = bcdiv($dividend, $divisor, self::$_precision);
            if (!$returnString && $result !== null) {
                return floatval($result);
            }

            return $result;
        }

        return -1;
    }

    /**
     * Divide all given arbitrary precision numbers
     *
     * @param            array         <int|float|string|bool> $values If no array is given all arguments will be multiplied
     * @param bool|float $returnString If TRUE the string representation will be returned (BC Math uses strings)
     * @return float|string
     */
    static public function divideAll($values, $returnString = false)
    {
        if (!is_array($values)) {
            $values = func_get_args();
        }
        $dividend = array_shift($values);
        foreach ($values as $divisor) {
            if ($dividend == 0) {
                throw new DivisionByZeroException('Division by zero');
            }
            $dividend = static::divide($dividend, $divisor, $returnString);
        }

        return $dividend;
    }

    /**
     * Compare two arbitrary precision numbers
     *
     * @param int|float|string|bool $a
     * @param int|float|string|bool $b
     *
     * @return bool
     */
    static public function almostEquals($a, $b)
    {
        if (static::_useBCMath()) {
            $a = static::_prepareValueForBCMath($a);
            $b = static::_prepareValueForBCMath($b);

            return bccomp($a, $b, self::$_precision) === 0;
        }

        return -1;
    }

    /**
     * Returns if the value is nearly zero (0)
     *
     * @param int|float|string|bool $value
     * @param int                   $precision
     * @throws Exception\MathException if the given precision is smaller than one (1)
     * @return bool
     */
    static public function nearlyZero($value, $precision = null)
    {
        if ($precision === null) {
            $precision = self::$_precision;
        }
        if ($precision < 1) {
            throw new MathException('Precision can not be lower than 1', 1408631275);
        }
        $epsilon = floatval('0.' . str_repeat('0', $precision - 1) . '1');

        return abs($value) < $epsilon;
    }

    /**
     * Prepares the given value for BC Math functions
     *
     * @param mixed  $value
     * @param string $operandName Name of the operand to mention it in the exception
     * @throws Exception\MathException
     * @return string
     */
    static protected function _prepareValueForBCMath($value, $operandName = '')
    {
        switch (true) {
            case is_float($value):
                return number_format($value, self::$_precision, '.', '');

            case is_integer($value):
                return $value . '.00';

            case is_string($value) && is_numeric($value):
                return $value;

            case is_bool($value):
                return $value ? '1.00' : '0.00';

            case is_resource($value):
            case is_array($value):
            case is_object($value):
            default:
                if ($operandName) {
                    $exceptionMessage = 'Could not prepare ' . $operandName . ' of type ' . gettype(
                            $value
                        ) . ' for BC Math';
                } else {
                    $exceptionMessage = 'Could not prepare value of type ' . gettype($value) . ' for BC Math';
                }
                throw new MathException($exceptionMessage, 1405066983);
        }
    }

    /**
     * Returns if BC Math should be used
     *
     * @throws \LogicException currently only BC Math is supported
     * @return boolean
     */
    static protected function _useBCMath()
    {
        if (!function_exists('bccomp')) {
            throw new \LogicException('BC Math extension currently is required', 1405066207);
        }

        return true;
    }
}

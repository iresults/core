<?php
/**
 * copyright iresults gmbh
 */

namespace Iresults\Core;

use Iresults\Core\Exception\EnumException;
use Iresults\Core\Exception\EnumOutOfRangeException;
use Iresults\Core\Exception\InvalidEnumArgumentException;
use Iresults\Core\Exception\InvalidEnumCallException;
use Iresults\Core\Exception\InvalidEnumValueException;

abstract class Enum
{
    /**
     * @var int|float|string|array|bool
     */
    protected $value;

    /**
     * Enum constructor.
     *
     * @param array|bool|float|int|string $value
     */
    public function __construct($value)
    {
        $this->value = static::normalize($value);
    }

    /**
     * Returns the enum instance's value
     *
     * @return array|bool|float|int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the value for the given constant name
     *
     * @param string $constantName
     * @return int|float|string|array|bool Returns the value or FALSE if the constant doesn't exist
     */
    public static function getValueForName($constantName)
    {
        return static::hasConstant($constantName)
            ? static::retrieveValueForName($constantName)
            : false;
    }

    /**
     * Returns the if a constant with the given name exists
     *
     * @param string $constantName
     * @return bool
     */
    public static function hasConstant($constantName)
    {
        if (!is_string($constantName)) {
            throw new \InvalidArgumentException('Expected argument "constantName" to be of type string');
        }

        return defined(static::getCalledClass() . '::' . strtoupper($constantName));
    }

    /**
     * Returns the constant name for the given value
     *
     * If the enum contains multiple constants with the given value the behaviour is undefined
     *
     * @param int|float|string|array|bool $constantValue
     * @return string|bool Returns the name or FALSE if not found
     */
    public static function getNameForValue($constantValue)
    {
        static::assertValidValueType($constantValue);
        $reflection = new \ReflectionClass(static::getCalledClass());

        return array_search($constantValue, $reflection->getConstants(), true);
    }

    /**
     * Returns the constant value for the given input
     *
     * @param int|float|string|array|bool $input
     * @return int|float|string|array|bool
     * @throws EnumException if the input is of an invalid type or it is neither a constant name nor a value
     */
    public static function normalize($input)
    {
        if (!static::isValidValueType($input)) {
            throw new InvalidEnumArgumentException(
                sprintf('Type of value is not a valid constant type or name: "%s"', gettype($input))
            );
        }

        // Looks like a constant name
        if (is_string($input) && static::hasConstant($input)) {
            return static::retrieveValueForName($input);
        }

        if (!static::isValidValue($input)) {
            throw new EnumOutOfRangeException(
                'Can not normalize input because it is neither a constant name nor a value of this enum'
            );
        }

        return $input;
    }

    /**
     * Returns if the given value is contained within the enum
     *
     * @param int|float|string|array|bool $value
     * @return string|bool Returns the name or FALSE if not found
     */
    public static function isValidValue($value)
    {
        if (__CLASS__ === get_called_class()) {
            throw new InvalidEnumCallException(__METHOD__ . ' must be called on a Enum subclass');
        }

        return false !== static::getNameForValue($value);
    }

    /**
     * Returns if the given value is a valid type for an enum in general
     *
     * @param mixed $value
     * @return bool
     */
    public static function isValidValueType($value)
    {
        if (is_null($value) || is_scalar($value)) {
            return true;
        }
        if (is_array($value)) {
            // Loop through the elements of the array and return false if one of it is not a valid value type
            foreach ($value as $element) {
                if (!static::isValidValueType($element)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param $constantValue
     */
    protected static function assertValidValueType($constantValue)
    {
        if (!static::isValidValueType($constantValue)) {
            throw new InvalidEnumValueException(
                sprintf('Type of value is not a valid enum type: "%s"', gettype($constantValue))
            );
        }
    }

    /**
     * @param $constantName
     * @return mixed
     */
    protected static function retrieveValueForName($constantName)
    {
        return constant(static::getCalledClass() . '::' . strtoupper($constantName));
    }

    /**
     * Throws an exception if the method is called on the Enum class instead of a subclass
     *
     * @return string
     */
    protected static function getCalledClass()
    {
        $calledClass = get_called_class();
        if (__CLASS__ === $calledClass) {
            throw new InvalidEnumCallException(__METHOD__ . ' must be called on a Enum subclass');
        }

        return $calledClass;
    }
}

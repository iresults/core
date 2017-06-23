<?php

namespace Iresults\Core;


/**
 * The iresults value class is a simple container for any kind of data.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Model
 */
class Value extends \Iresults\Core\Core
{
    /**
     * The value.
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * The constructor
     *
     * @param mixed $value The value of the object
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Returns the value of the object.
     *
     * @return    mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the default description.
     *
     * @return    string    A string describing this object
     */
    public function description()
    {
        return '' . Iresults::descriptionOfValue($this->value);
    }

    /**
     * Factory method: Returns a new value object with the given value.
     *
     * @param mixed $value The value of the object
     * @return    \Iresults\Core\Value
     */
    static public function valueObjectWithValue($value)
    {
        return new static($value);
    }
}

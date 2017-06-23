<?php


/**
 * @author Daniel Corn <cod@iresults.li>
 * Created 05.10.16 11:49
 */

namespace Iresults\Core\Tests\Fixture;

class Person extends \Iresults\Core\Model
{
    /**
     * The name
     *
     * @var string
     */
    protected $name = 'Daniel';

    /**
     * The age
     *
     * @var int
     */
    protected $age = 26;

    /**
     * The address
     *
     * @var array<string>
     */
    protected $address = [
        'street'  => 'Bingstreet 14',
        'city'    => 'NYC',
        'country' => 'USA',
    ];

    /**
     * Returns the name.
     *
     * @return    string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $newValue The new value to set
     * @return    void
     */
    public function setName($newValue)
    {
        $this->name = $newValue;
    }

    public function gar()
    {
        return $this->address;
    }
}

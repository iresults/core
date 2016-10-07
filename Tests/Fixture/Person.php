<?php
/*
 * The MIT License (MIT)
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
    protected $address = array(
        'street'  => 'Bingstreet 14',
        'city'    => 'NYC',
        'country' => 'USA',
    );

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
     * @param    string $newValue The new value to set
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

<?php
namespace Iresults\Core\Model;

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
 * The interface for path containers which allow the store, analyse and find
 * objects assigned to any kind of paths, including property key paths, tree
 * branches and similar.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Model
 */
interface PathContainerInterface extends \Iresults\Core\Model\PathAccessInterface {
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* INITIALIZATION    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Initializes a path container instance with the data from the given array.
	 *
	 * @param	array	$array The associative array|dictionary to read the data from
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 *
	 * @throws InvalidArgumentException if the given value is not an object.
	 */
	public function initWithArray($array);


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns an empty path container instance.
	 *
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 */
	static public function container();

	/**
	 * Factory method: Returns a path container instance with the data from the
	 * given mutable.
	 *
	 * @param	\Iresults\Core\Mutable	$object The mutable object from which the data will be read
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 */
	static public function containerWithMutable($mutable);

	/**
	 * Factory method: Returns a path container instance with the data from the
	 * given array.
	 *
	 * @param	array	$array The associative array|dictionary to read the data from
	 * @return	\Iresults\Core\Model\PathContainerInterface
	 */
	static public function containerWithArray($array);

	/**
	 * Factory method: Returns a path container instance built with the data
	 * from the given object and the objects at the property key path as
	 * children.
	 *
	 * The object itself will be set at path 0 and the objects at the given
	 * property key path as children of path 0.
	 *
	 * @param	object	$object The object from which the data will be read, using the property key path
	 * @param	string	$propertyKeyPath The property key path to the object's children
	 * @return	\Iresults\Core\Model\DataTree
	 */
	//static public function containerWithObjectAndProperty($object, $propertyKeyPath = 'children');
}
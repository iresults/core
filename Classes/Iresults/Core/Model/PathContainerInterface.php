<?php
namespace Iresults\Core\Model;

/*
 * Copyright notice
 *
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *             Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
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
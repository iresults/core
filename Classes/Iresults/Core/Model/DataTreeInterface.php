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
 * The interface for data tree classes.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Model
 */
interface DataTreeInterface extends \Iresults\Core\Model\PathAccessInterface {
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* INITIALIZATION        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Initializes a data tree instance with the data from the given object and
	 * the objects at the property key path as children.
	 *
	 * The object itself will be set at path 0 and the objects at the given
	 * property key path as children of path 0.
	 *
	 * @param	object	$object The object from which the data will be read, using the property key path
	 * @param	string	$propertyKeyPath The property key path to the object's children
	 * @return	\Iresults\Core\Model\DataTree
	 */
	public function initWithObjectAndProperty($object, $propertyKeyPath = 'children');


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ACCESS BY PATH        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the whole data tree.
	 *
	 * Example of a tree:
	 *
	 *                Root
	 *                 |
	 *          0-----------1         ________________ Level 0 __
	 *          |           |
	 *         0.0   1.0---1.1---1.2  ________________ Level 1 __
	 *          |     |           |
	 *          |   1.0.0   1.2.0---1.2.1
	 *          |             |
	 *    0.0.0---0.0.1       |       ________________ Level 2 __
	 *                        |
	 *      1.2.0.0---1.2.0.1---1.2.0.2---1.2.0.3 ____ Level 3 __
	 *
	 * @return	array<array<...>>
	 */
	public function getTree();

	/**
	 * Returns the object at the given tree path.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0------------1                __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----1.2        __________ Level 1 __
	 *                |             |
	 *              1.0.0   [1.2.0]---1.2.1   __________ Level 2 __
	 *
	 *
	 * The path 1.2.0 would return the object in square brackets.
	 *
	 * @param	string	$path The path to the object
	 * @return	object	The object at the given path, or NULL if none exists
	 *
	 * public function getObjectAtPath($path);
	 */

	/**
	 * Sets the object at the given path.
	 *
	 * Example 1:
	 *
	 *                Root
	 *                 |
	 *          0------------1                __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----1.2        __________ Level 1 __
	 *                |             |
	 *              1.0.0   [1.2.0]---1.2.1   __________ Level 2 __
	 *
	 *
	 * The path 1.2.0 would point to the object in square brackets.
	 *
	 * Example 2:
	 *
	 *                Root
	 *                 |
	 *          0------------1           __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----1.2   __________ Level 1 __
	 *                :
	 *             [1.0.0]               __________ Level 2 __
	 *
	 * The path 1.0.0 would be created.
	 *
	 * Example 3:
	 *
	 *                Root
	 *                 |
	 *          0------------1           __________ Level 0 __
	 *          |            |
	 *         0.0      1.1----1.2       __________ Level 1 __
	 *
	 *             [1.0.0]               __________ Level 2 __
	 *
	 * An exception would be thrown when setting the path 1.0.0.
	 *
	 * @param	string	$path   The path to set
	 * @param	object	$object The new object
	 * @return	void
	 *
	 * @throws InvalidArgumentException if the given value is not an object.
	 * @throws \Iresults\Core\Model\PathAccess\Exception\DuplicateEntry if the object already exists in the tree.
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if no object exists at the path to the parent object.
	 *
	 * public function setObjectAtPath($path, $object);
	 */

	/**
	 * Returns if an object exists at the given tree path.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0------------1                __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----1.2        __________ Level 1 __
	 *                |             |
	 *              1.0.0   [1.2.0]---1.2.1   __________ Level 2 __
	 *
	 *
	 * The path 1.2.0 would return TRUE.
	 *
	 * @param	string	$path The path to the object
	 * @return	boolean	TRUE if an object exists at the given path, otherwise FALSE
	 *
	 * public function hasObjectAtPath($path);
	 */

	/**
	 * Returns the child objects at the given path.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *         0-------------(1)            __________ Level 0 __
	 *         |              |
	 *        0.0   [1.0]---[1.1]---[1.2]   __________ Level 1 __
	 *
	 * The path 1 would return the objects 1.0, 1.1 and 1.2.
	 *
	 * @param	string	$path   The path to the parent object
	 * @return	array<object> The child objects of the given path
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if no object exists at the path.
	 */
	public function getChildObjectsAtPath($path);

	/**
	 * Returns the child objects at the given path recursive.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0-------------(1)                  __________ Level 0 __
	 *          |              |
	 *         0.0   [1.0]---[1.1]---[1.2]         __________ Level 1 __
	 *                 |               |
	 *              [1.0.0]    [1.2.0]---[1.2.1]   __________ Level 2 __
	 *
	 *
	 * The path 1 would return the objects in square brackets on Level 1 and all
	 * their children.
	 *
	 * The returning array would look like this:
	 *  array(
	 * 	      array(
	 *             'obj'=> 1.0,
	 *             'children' => array(
	 *                   array(
	 *                         'obj' => 1.0.0
	 *                   )
	 *             )
	 *       ),
	 *       array(
	 *             'obj' => 1.1
	 *       ),
	 *       array(
	 *	           'obj' => 1.2,
	 *             'children' => array(
	 *                   array(
	 *                         'obj' => 1.2.0
	 *                   ),
	 *                   array(
	 *                         'obj' => 1.2.1
	 *                   )
	 *             )
	 *       )
	 *  )
	 *
	 * @param	string	$path The path to the parent object
	 * @return	array<array<...>>    An array of arrays of objects
	 */
	public function getChildObjectsAtPathRecursive($path);

	/**
	 * Adds a child object to the object at the given path.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0-----------(1)            __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----[1.2]   __________ Level 1 __
	 *
	 * If a child object is added to path 1, the path 1.2 would be created.
	 *
	 * @param	string	$path   The path to the parent object
	 * @param	object	$object The new object
	 * @return	string The path to the added object
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if no object exists at the path to the parent object.
	 * @throws \Iresults\Core\Model\PathAccess\Exception\DuplicateEntry if the object already exists in the tree.
	 */
	public function addChildObjectAtPath($path, $object);

	/**
	 * Adds a child object to the given object.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0-----------(1)            __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----[1.2]   __________ Level 1 __
	 *
	 * If a child object is added to the parent object 1, the path 1.2 would be
	 * created.
	 *
	 * @param	object	$child 	The child object to add
	 * @param	object	$parent 	The parent object
	 * @return	string The path to the added object
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the parent object doesn't exist in the tree.
	 * @throws \Iresults\Core\Model\PathAccess\Exception\DuplicateEntry if the object already exists in the tree.
	 */
	public function addChildObjectToObject($child, $parent);

	/**
	 * Adds the given object's children (defined through the given property) as
	 * tree node children at the given tree path.
	 *
	 * @param	object	$object   The object to get the children from
	 * @param	string	$propertyKeyPath The property key path to the object's children
	 * @param	string	$treePath The path to the node where the children should be attached
	 * @return	void
	 */
	public function addChildrenFromObjectAtPropertyToTreeAtPath($object, $propertyKeyPath = 'children', $treePath = '0');


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ACCESS BY OBJECT      MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the parent object of the given object.
	 *
	 * @param	object	$object A object in the tree
	 * @return	object    The object's parent object
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the object doesn't exist in the tree.
	 */
	public function getParentObjectOfObject($object);

	/**
	 * Returns the child objects of the given object.
	 *
	 * @param	object	$object A object in the tree
	 * @return	array<object>	The object's children
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the object doesn't exist in the tree.
	 */
	public function getChildObjectsOfObject($object);


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ACCESS BRANCHES       MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns a dictionary of objects that build the branch to the given path.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0-----------{1}                __________ Level 0 __
	 *          |            |
	 *         0.0   1.0----1.1----{1.2}       __________ Level 1 __
	 *                |             |
	 *              1.0.0   [1.2.0]---1.2.1    __________ Level 2 __
	 *
	 * If the object at 1.2.0 is passed, a dictionary of the objects in curly
	 * brackets will be returned.
	 *
	 * @param	string	$path The path to the object
	 * @return	array<object>	The branch to the path
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the no object exists at the given path.
	 */
	public function getBranchToPath($path);

	/**
	 * Returns a dictionary of objects that build the branch to the given object.
	 *
	 * @see getBranchToPath()
	 *
	 * @param	object	$object A object in the tree
	 * @return	array<object>	The branch to the object
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the object doesn't exist in the tree.
	 */
	public function getBranchToObject($object);

	/**
	 * Returns a dictionary of objects that build the branch defined in the
	 * given properties.
	 *
	 * Like getBranchOfObjectsWithProperties() but with a single property key.
	 *
	 * @see getBranchOfObjectsWithProperties()
	 *
	 * @param	string	$propertyKey   The property key to look for
	 * @param	array<mixed> $propertyValues	The property values to look for
	 * @param	array<Error> $errors Reference to an array that will be filled with errors that occure
	 *
	 * @return	array<object>	The branch to the object
	 */
	public function getBranchOfObjectsWithPropertyKeyAndValues($propertyKey, $propertyValues, &$errors = array());

	/**
	 * Returns a dictionary of objects that build the branch defined in the
	 * given properties.
	 *
	 * Property key paths may also be passed to compare those values.
	 *
	 * Example:
	 *
	 * @param	array<mixed> $properties   A dictionary of property keys and values the objects are tested for
	 * @param	array<Error> $errors Reference to an array that will be filled with errors that occure
	 *
	 * @return	array<object>	The branch to the object
	 */
	public function getBranchOfObjectsWithProperties($properties, &$errors = NULL);


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns an empty data tree instance.
	 *
	 * @return	\Iresults\Core\Model\DataTree
	 */
	static public function tree();

	/**
	 * Factory method: Returns a data tree instance built with the data from the
	 * given object and the objects at the property key path as children.
	 *
	 * The object itself will be set at path 0 and the objects at the given
	 * property key path as children of path 0.
	 *
	 * @param	object	$object The object from which the data will be read, using the property key path
	 * @param	string	$propertyKeyPath The property key path to the object's children
	 * @return	\Iresults\Core\Model\DataTree
	 */
	static public function treeWithObjectAndProperty($object, $propertyKeyPath = 'children');
}
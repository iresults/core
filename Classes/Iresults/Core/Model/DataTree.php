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
 * A iresults data tree represents a hierarchical tree of data objects.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Model
 */
class DataTree extends \Iresults\Core\Model\PathAccess\AbstractContainer implements \Iresults\Core\Model\DataTreeInterface {
	/**
	 * The dictionary holding the data tree.
	 *
	 * @var array<object>
	 */
	protected $pathToObject = array();

	/**
	 * The dictionary holding the object's hash and the path to the object.
	 *
	 * @var array<string>
	 */
	protected $hashToPath = array();

	/**
	 * The number of level inside the tree.
	 *
	 * @var integer
	 */
	protected $levelCount = 0;

	/**
	 * The preferred class for objects in the tree.
	 *
	 * If none is set the method \Iresults\Core\Helpers\ObjectHelper::createObjectWithValue()
	 * is invoked to create new objects.
	 *
	 * @var string
	 */
	protected $objectType = '';

	/**
	 * The (guessed) maximum number of children a node in the tree has.
	 *
	 * @var integer
	 */
	protected $maxChildrenCount = 5;


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
	public function initWithObjectAndProperty($object, $propertyKeyPath = 'children') {
		$objectClone = clone $object;
		$this->addChildrenFromObjectAtPropertyToTreeAtPath($objectClone, $propertyKeyPath);


		// Prepare the root level object
		$objectTypeL = $this->objectType;
		if (is_object($objectClone)) {# && get_class($objectClone) === 'stdClass') {
			if ($objectTypeL) {
				$newObjectClone = new $objectTypeL();
				\Iresults\Core\Helpers\ObjectHelper::setPropertiesOfObjectFromArray($objectClone, $newObjectClone, TRUE);
				$objectClone = $newObjectClone;
			} else {
				$objectClone = \Iresults\Core\Helpers\ObjectHelper::createObjectWithValue($objectClone);
			}
		}

		$this->setObjectAtPath('0', $objectClone);
		return $this;
	}




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
	public function getTree() {
		return $this->getChildObjectsAtPathRecursive('Root');
	}

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
	 */
	public function getObjectAtPath($path) {
		return parent::getObjectAtPath($path);
	}

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
	 */
	public function setObjectAtPath($path, $object) {
		$pathToParent = $this->getParentPathOfPath($path);
		if ($pathToParent && !$this->hasObjectAtPath($pathToParent)) {
			throw new \Iresults\Core\Model\PathAccess\Exception\EntryNotFound("No parent object exists at the parent tree path '$pathToParent' to set '$path'.", 1321352211);
		}

		return parent::setObjectAtPath($path, $object);
	}

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
	public function getChildObjectsAtPath($path) {
		if (!$this->hasObjectAtPath($path)) {
			throw new \Iresults\Core\Model\PathAccess\Exception\EntryNotFound("No object exists at the tree path $path.", 1321353681);
		}
		return $this->findAllObjectsWithPathsMatchingPattern($path . $this->pathSeparator . '*');
	}

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
	public function getChildObjectsAtPathRecursive($path) {
		$returnArray = array();
		$path = $path . $this->pathSeparator . '*';
		$foundObjects = $this->findAllObjectsWithPathsMatchingPattern($path);
		foreach ($foundObjects as $foundPath => $foundObject) {
			$currentObjectArray = array();
			$currentObjectArray['obj'] = $foundObject;
			#$currentObjectArray['path'] = $foundPath;
			$currentObjectArray['children'] = $this->getChildObjectsAtPathRecursive($foundPath);

			$returnArray[$foundPath] = $currentObjectArray;
		}
		return $returnArray;
	}

	/**
	 * Adds a child object to the object at the given path.
	 *
	 * Example:
	 *
	 *                Root
	 *                 |
	 *          0------------1             __________ Level 0 __
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
	 */
	public function addChildObjectAtPath($path, $object) {
		$i = 0;
		$newPath = '';
		$lastNodeId = -1;

		if (!$this->hasObjectAtPath($path)) {
			throw new \Iresults\Core\Model\PathAccess\Exception\EntryNotFound("No object exists at the tree path $path.", 1321368357);
		}

		/*
		 * Search the current children of the given path and get the ID that
		 * the given object will have.
		 */
		$foundPaths = $this->findAllPathsMatchingPattern($path . $this->pathSeparator . '*');
		$foundPaths = array_values($foundPaths);
		$foundPathsCount = count($foundPaths);
		$nodeIdLength = strlen($this->pathSeparator) + strlen($path);
		for($i = 0; $i < $foundPathsCount; $i++) {
			$currentNodeId = (int) substr($foundPaths[$i], $nodeIdLength);
			if ($currentNodeId > $lastNodeId) {
				$lastNodeId = $currentNodeId;
			}
		}
		$newPath = $path . $this->pathSeparator . ($lastNodeId + 1);

		// Set the given object
		$this->setObjectAtPath($newPath, $object);

		// Update the max children count
		if ($this->maxChildrenCount < $lastNodeId) {
			$this->maxChildrenCount = $lastNodeId + 5;
		}
		return $newPath;
	}

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
	public function addChildObjectToObject($child, $parent) {
		$path = $this->getPathOfObject($parent);
		return $this->addChildObjectAtPath($path, $child);
	}

	/**
	 * Adds the given object's children (defined through the given property) as
	 * tree node children at the given tree path.
	 *
	 * @param	object	$object   The object to get the children from
	 * @param	string	$propertyKeyPath The property key path to the object's children
	 * @param	string	$treePath The path to the node where the children should be attached
	 * @return	void
	 */
	public function addChildrenFromObjectAtPropertyToTreeAtPath($object, $propertyKeyPath = 'children', $treePath = '0') {
		$objectTypeL = $this->objectType;

		// Fetch the children and verify the result
		$children = \Iresults\Core\Helpers\ObjectHelper::getObjectForKeyPathOfObject($propertyKeyPath, $object);

		if (is_object($children) && !$children instanceof Traversable) {
			$children = array($children);
		}

		if (is_array($children) || $children instanceof Traversable) {
			// Loop through the children and add them
			$childrenCount = count($children);
			#\Iresults\Core\Iresults::pd("$childrenCount children for path $propertyKeyPath for tree path $treePath");
			for($i = 0; $i < $childrenCount; $i++) {
				$originalChild  = $children[$i];
				$child = $originalChild;
				$childPath = $treePath . '.' . $i;

				// If the current child is a stdClass object pass it through \Iresults\Core\Helpers\ObjectHelper::createObjectWithValue
				if (is_object($originalChild) && get_class($originalChild) === 'stdClass') {
					if ($objectTypeL) {
						$child = new $objectTypeL();
						\Iresults\Core\Helpers\ObjectHelper::setPropertiesOfObjectFromArray($originalChild, $child, TRUE);
					} else {
						$child = \Iresults\Core\Helpers\ObjectHelper::createObjectWithValue($child);
					}
				}
				$this->setObjectAtPath($childPath, $child);

				#\Iresults\Core\Iresults::pd("Set child at $childPath");
				#\Iresults\Core\Iresults::pd($child);

				// If the current child isn't a scalar, call the method recursive
				if (!is_scalar($originalChild)) {
					$this->addChildrenFromObjectAtPropertyToTreeAtPath($originalChild, $propertyKeyPath, $childPath);
				}
			}
		} else {
			#\Iresults\Core\Iresults::pd("No children for path $propertyKeyPath for tree path $treePath");
		}

		\Iresults\Core\Helpers\ObjectHelper::setObjectForKeyPathOfObject($propertyKeyPath, NULL, $object);
		\Iresults\Core\Helpers\ObjectHelper::setObjectForKeyPathOfObject(substr($propertyKeyPath , 0 , strpos($propertyKeyPath,'.')), NULL, $object);
	}


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
	public function getParentObjectOfObject($object) {
		$path = $this->getPathOfObject($object);
		$path = $this->getParentPathOfPath($path);
		return $this->getObjectAtPath($path);
	}

	/**
	 * Returns the child objects of the given object.
	 *
	 * @param	object	$object A object in the tree
	 * @return	array<object>	The object's children
	 *
	 * @throws \Iresults\Core\Model\PathAccess\Exception\EntryNotFound if the object doesn't exist in the tree.
	 */
	public function getChildObjectsOfObject($object) {
		$path = $this->getPathOfObject($object);
		return $this->findAllObjectsWithPathsMatchingPattern($path . $this->pathSeparator . '*');
	}


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
	public function getBranchToPath($path) {
		$branch = array();
		$currentPath = $path;
		if (!$this->hasObjectAtPath($path)) {
			throw new \Iresults\Core\Model\PathAccess\Exception\EntryNotFound("No object exists at the tree path $path.", 1321365775);
		}

		while ($currentPath !== '') {
			$branch[$currentPath] = $this->getObjectAtPath($currentPath);
			$currentPath = substr($currentPath, 0, (int)strrpos($currentPath, $this->pathSeparator));
		}
		return array_reverse($branch);
	}

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
	public function getBranchToObject($object) {
		$branch = array();
		if (!$this->containsObject($object)) {
			throw new \Iresults\Core\Model\PathAccess\Exception\EntryNotFound("The object of class " . get_class($object) . " doesn't exist in the tree.", 1321365995);
		}

		$currentPath = $this->getPathOfObject($object);
		return $this->getBranchToPath($currentPath);
	}

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
	public function getBranchOfObjectsWithPropertyKeyAndValues($propertyKey, $propertyValues, &$errors = array()) {
		$branch = array();
		$loopNumber = 1;
		$currentValue = reset($propertyValues);
		$objectCollectionSearchHelper = $this->_getObjectCollectionSearchHelper();

		// Start with the root objects
		$children = $this->getChildObjectsAtPath('Root');

		// As long as there is a current property value
		while ($currentValue) {
			$childrenWithMatchingProperty = $objectCollectionSearchHelper->findObjectsWithPropertyInCollection($propertyKey, $currentValue, $children);

			/*
			 * If none of the children matches the requirements (matching
			 * property key and value) add an error to the errors array.
			 */
			if (empty($childrenWithMatchingProperty)) {
				$userInfo = array('child' => $currentChild, 'children' => $children, 'propertyValue' => $currentValue, 'propertyKey' => $propertyKey, 'level' => $loopNumber);
				$message = '';
				if (is_scalar($currentValue)) {
					$message = "No children found matching the property key '$propertyKey' and value '$currentValue' of the current child at loop number $loopNumber.";
				} else {
					$message = "No children found matching the property key '$propertyKey' of the current child at loop number $loopNumber.";
				}
				$errors[] = \Iresults\Core\Error::errorWithMessageCodeAndUserInfo($message, 1321984280, $userInfo);
				break;
			}

			/*
			 * Use only the first found child and add the child with its path to
			 * the branch.
			 */
			$currentChild = reset($childrenWithMatchingProperty);
			$currentPath = $this->getPathOfObject($currentChild);
			$branch[$currentPath] = $currentChild;

			/*
			 * Get the next property value and test if there are children if
			 * there is another value. If no value exists no more children are
			 * required.
			 */
			$currentValue = next($propertyValues);
			$loopNumber++;
			if ($currentValue) {
				/*
				 * Get the children of the above object.
				 */
				$children = $this->getChildObjectsOfObject($currentChild);
				if (empty($children)) {
					$userInfo = array('child' => $currentChild, 'propertyValue' => $currentValue, 'propertyKey' => $propertyKey, 'level' => $loopNumber, 'p' => $this->getChildObjectsOfObject($currentChild));
					$errors[] = \Iresults\Core\Error::errorWithMessageCodeAndUserInfo("No children for current child at loop number $loopNumber.", 1321980307, $userInfo);
					break;
				}
			}
		}
		return $branch;
	}

	/**
	 * Returns a dictionary of objects that build the branch defined in the
	 * given properties.
	 *
	 * Property key paths may also be passed to compare those values.
	 *
	 * Example:
	 *  The properties array
	 *      array(
	 *          'property1' => 'value1',
	 *          'property2' => 'value2',
	 *           ...
	 *          'propertyM' => 'valueM'
	 *          'propertyN' => 'valueN'
	 *      )
	 *
	 *  would require a object branch
	 *      object1->property1 == 'value1',
	 *            |
	 *        has child
	 *           |
	 *           V
	 *      object2->property2 == 'value2',
	 *            |
	 *        has child
	 *           |
	 *           V
	 *       ...
	 *           |
	 *        has child
	 *          |
	 *          V
	 *      objectM->propertyM == 'valueM',
	 *            |
	 *        has child
	 *           |
	 *           V
	 *      objectN->propertyN == 'valueN',
	 *
	 *  The returned array would look like
	 *      array(
	 *          object1->property1 == 'value1',
	 *          object2->property2 == 'value2',
	 *           ...
	 *          objectM->propertyM == 'valueM',
	 *          objectN->propertyN == 'valueN',
	 *      )
	 *
	 * @param	array<mixed> $properties   A dictionary of property keys and values the objects are tested for
	 * @param	array<Error> $errors Reference to an array that will be filled with errors that occure
	 *
	 * @return	array<object>	The branch to the object
	 */
	public function getBranchOfObjectsWithProperties($properties, &$errors = NULL) {
		throw new Exception("TEST ME");
		$branch = array();
		$loopNumber = 0;
		$currentValue = reset($properties);
		$currentKey = key($properties);
		$objectCollectionSearchHelper = $this->_getObjectCollectionSearchHelper();
		$children = $this->getChildObjectsAtPath('Root');


		while ($currentValue) {
			$loopNumber++;
			$currentKey = key($properties);
			$childrenWithMatchingProperty = $objectCollectionSearchHelper->findObjectsWithPropertyInCollection($currentKey, $currentValue, $children);
			$currentChild = reset($childrenWithMatchingProperty);

			$currentPath = $this->getPathOfObject($currentChild);
			$branch[$currentPath] = $currentChild;

			$children = $this->getChildObjectsOfObject($currentChild);
			$currentValue = next($propertyValues);

			if ($currentValue && empty($children)) {
				$errors[] = new UnexpectedValueException("No children for current child at loop number $loopNumber.", 1321980307);
			}
		}
		return $branch;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* HELPER METHODS    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Searches the tree's branches for objects whose paths matching the given
	 * pattern.
	 *
	 * A pattern contains astrisks for variable path parts and may look like one
	 * of the following examples:
	 * 	2.0.*
	 * 	1.*.0
	 * 	2.*.*.1
	 * 	*.1.*.1
	 *
	 * @param	string	$pattern A path pattern to search for
	 * @return	array<object> An array of objects whose path match the given pattern
	 */
	//protected function _searchObjectsWithPathLikePattern($pattern) {
	//	$foundObjects = array();
	//	$foundPaths = $this->_searchPathsLikePattern($pattern);
	//	foreach ($foundPaths as $path) {
	//		$foundObjects[$path] = $this->pathToObject[$path];
	//		#$foundObjects[] = $this->getObjectAtPath($path);
	//	}
	//	return $foundObjects;
	//}

	/**
	 * Searches the tree's branches for paths matching the given pattern.
	 *
	 * A pattern contains astrisks for variable path parts and may look like one
	 * of the following examples:
	 * 	2.0.*
	 * 	1.*.0
	 * 	2.*.*.1
	 * 	*.1.*.1
	 *
	 * @param	string	$pattern A path pattern to search for
	 * @return	array<string> An array of the tree's paths matching the given pattern
	 */
	//protected function _searchPathsLikePattern($pattern) {
	//	$foundPaths = array();
	//	$maxNumbers = $this->_getMaxChildrenCount();
	//
	//
	//	if ($pattern === '.*' || $pattern === 'Root.*') {
	//		$pattern = '*';
	//	}
	//
	//	// Prepare the pattern
	//	$possiblePaths = array();
	//	for($i = 0; $i < $maxNumbers; $i++) {
	//		$possiblePaths[] = str_replace('*', "$i", $pattern);
	//	}
	//
	//	$paths = array_keys($this->pathToObject);
	//	foreach ($paths as $path) {
	//		$path = "".$path;
	//		if (in_array($path, $possiblePaths, TRUE)) {
	//			$foundPaths[] = $path;
	//		}
	//	}
	//	return $foundPaths;
	//}

	/**
	 * Returns guessed the maximum number of children a node has.
	 * @return	integer The maximum number of children in the tree
	 */
	protected function _getMaxChildrenCount() {
		return $this->maxChildrenCount;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Factory method: Returns an empty data tree instance.
	 *
	 * @return	\Iresults\Core\Model\DataTree
	 */
	static public function tree() {
		$dataTree = NULL;
		if (IR_MODERN_PHP) {
			$dataTree = new static();
		} else {
			$dataTree = new self();
		}
		return $dataTree;
	}

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
	static public function treeWithObjectAndProperty($object, $propertyKeyPath = 'children') {
		$dataTree = self::tree();
		$dataTree->initWithObjectAndProperty($object, $propertyKeyPath);
		return $dataTree;
	}
}
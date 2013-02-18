/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/



/**
 * 
 * 
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Persistence
 * @version 1.0.0
 */
Iresults.Class.create('Iresults.Persistence.RepositoryInterface', {
	/**
	 * Creates a new object in the repository.
	 * 
	 * @param{subclass of DS.Model} type
     * @param	{Object} properties a hash of properties to set on the newly created record
	 * @returns	{DS.Model}				The new object
	 */
	createRecord: function (type, hash) {},
	
	/**
	 * Adds an object to this repository.
	 *
	 * @param	{Object} object The object to add
	 * @returns {void}
	 * @api
	 */
	add: function (object) {},

	/**
	 * Removes an object from this repository.
	 *
	 * @param	{Object} object The object to remove
	 * @returns {void}
	 * @api
	 */
	remove: function (object) {},

	/**
	 * Replaces an object by another.
	 *
	 * @param	{Object} existingObject The existing object
	 * @param	{Object} newObject The new object
	 * @returns {void}
	 * @api
	 */
	replace: function (existingObject, newObject) {},

	/**
	 * Replaces an existing object with the same identifier by the given object
	 *
	 * @param	{Object} modifiedObject The modified object
	 * @api
	 */
	update: function (modifiedObject) {},

	/**
	 * Returns all objects of this repository add()ed but not yet persisted to
	 * the storage layer.
	 *
	 * @returns {Array} An array of objects
	 */
	getAddedObjects: function () {},

	/**
	 * Returns an array with objects remove()d from the repository that
	 * had been persisted to the storage layer before.
	 *
	 * @returns {Array}
	 */
	getRemovedObjects: function () {},

	/**
	 * Returns all objects of this repository.
	 *
	 * @returns {Array} An array of objects, empty if no objects found
	 * @api
	 */
	findAll: function () {},

	/**
	 * Returns the total number objects of this repository.
	 *
	 * @returns {Integer} The object count
	 * @api
	 */
	countAll: function () {},

	/**
	 * Removes all objects of this repository as if remove() was called for
	 * all of them.
	 *
	 * @returns {void}
	 * @api
	 */
	removeAll: function () {},

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param	{Integer} uid The identifier of the object to find
	 * @returns {Object} The matching object if found, otherwise NULL
	 * @api
	 */
	findByUid: function (uid) {},

	/**
	 * Sets the property names to order the result by per default.
	 * Expected like this:
	 * array(
	 *  'foo' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING,
	 *  'bar' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
	 * )
	 *
	 * @param	{Array} defaultOrderings The property names to order by
	 * @returns {void}
	 * @api
	 */
	setDefaultOrderings: function (defaultOrderings) {},

	/**
	 * Sets the default query settings to be used in this repository
	 *
	 * @param	{Tx_Extbase_Persistence_QuerySettingsInterface} defaultQuerySettings The query settings to be used by default
	 * @returns {void}
	 * @api
	 */
	setDefaultQuerySettings: function (defaultQuerySettings) {},

	/**
	 * Returns a query for objects of this repository
	 *
	 * @returns {Object}
	 * @api
	 */
	createQuery: function () {}
});

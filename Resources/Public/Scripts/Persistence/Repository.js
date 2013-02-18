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


var Iresults = window.Iresults || {};
Iresults.Persistence = Iresults.Persistence || {};


/**
 * The abstract repository class.
 *
 * @include Iresults.Core.Core
 * @include Iresults.Core.Helpers.Filter
 * @include Iresults.Core.Persistence.Backend
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Persistence
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Persistence.Repository', {
	/**
	 * The array of objects currently in the repository
	 *
	 * If a filter is applied to the repository, this will contain only the
	 * objects that pass the filter.
	 *
	 * @type {Ember.NativeArray}
	 */
	objects: null,

	/**
	 * The array of all objects in the repository
	 *
	 * Even if a filter is applied to the repository, this will contain all
	 * objects.
	 *
	 * @type {Ember.NativeArray}
	 */
	allObjects: null,

	/**
	 * The array of objects added to the repository
	 *
	 * @type {Ember.NativeArray}
	 */
	addedObjects: null,

	/**
	 * The array of objects deleted from the repository
	 *
	 * @type {Ember.NativeArray}
	 */
	removedObjects: null,

	/**
	 * The expected class name of the objects managed by the repository
	 *
	 * @type {String}
	 */
	objectType: '',

	/**
	 * Initialize the repository.
	 *
	 * @returns	{void}
	 */
	init: function () {
		var objectsFromBackend;
		this._super();
		console.log("init");
		objectsFromBackend = Iresults.Persistence.Backend.getSharedInstance().getStorageForRepository(this);
		this.allObjects = Ember.A(objectsFromBackend);

		this.objects = this.allObjects;
		this.addedObjects = Ember.A();
		this.removedObjects = Ember.A();


		console.log(Iresults.Persistence.Backend.getSharedInstance());
	},

	/**
	 * Creates a new object in the repository.
	 *
	 * @param{subclass of DS.Model} type
     * @param	{Object} properties a hash of properties to set on the newly created record
	 * @returns	{DS.Model}				The new object
	 */
	createRecord: function (type, hash) {
		throw Iresults.Exception.create(1341322094, "Creation of object's is currently not implemented.", arguments);
	},

	/**
	 * Adds an object to this repository.
	 *
	 * @param	{Object} object The object to add
	 * @returns {void}
	 * @api
	 */
	add: function (object) {
		this.addedObjects.push(object);
		this.objects.push(object);
	},

	/**
	 * Removes an object from this repository.
	 *
	 * @param	{Object} object The object to remove
	 * @returns {void}
	 * @api
	 */
	remove: function (object) {
		var indexOfObject = this.objects.indexOf(object);
		if (indexOfObject === -1) {
			throw Iresults.Exception.create(1341322437, "The object doesn't exist in the repository.", arguments);
		}
		this.removedObjects.push(object);
		this.objects.removeAt(indexOfObject);
	},

	/**
	 * Replaces an object by another.
	 *
	 * @param	{Object} existingObject The existing object
	 * @param	{Object} newObject The new object
	 * @returns {void}
	 * @api
	 */
	replace: function (existingObject, newObject) {
		var indexOfObjectInObjects         = this.objects.indexOf(existingObject),
			indexOfObjectInRemovedObjects  = this.removedObjects.indexOf(existingObject),
			indexOfObjectInAddedObjects    = this.addedObjects.indexOf(existingObject);
		if (indexOfObjectInObjects === -1) {
			throw Iresults.Exception.create(1341322710, "The object to replace doesn't exist in the repository.", arguments);
		}
		this.objects[indexOfObjectInObjects] = newObject;
		if (indexOfObjectInRemovedObjects !== -1) {
			this.removedObjects[indexOfObjectInRemovedObjects] = newObject;
		} else if (indexOfObjectInAddedObjects !== -1) {
			this.addedObjects[indexOfObjectInAddedObjects] = newObject;
		}
	},

	/**
	 * Replaces an existing object with the same identifier by the given object
	 *
	 * @param	{Object} modifiedObject The modified object
	 * @api
	 */
	update: function (modifiedObject) {
		var existingObject = this.findByUid(modifiedObject.get('uid'));
		this.replace(existingObject, modifiedObject);
	},

	/**
	 * Returns all objects of this repository add()ed but not yet persisted to
	 * the storage layer.
	 *
	 * @returns {Array} An array of objects
	 */
	getAddedObjects: function () {
		return this.addedObjects;
	},

	/**
	 * Returns an array with objects remove()d from the repository that
	 * had been persisted to the storage layer before.
	 *
	 * @returns {Array}
	 */
	getRemovedObjects: function () {
		return this.removedObjects;
	},

	/**
	 * Returns all objects of this repository.
	 *
	 * @returns {Array} An array of objects, empty if no objects found
	 * @api
	 */
	findAll: function () {
		return this.objects;
	},

	/**
	 * Returns the total number objects of this repository.
	 *
	 * @returns {Integer} The object count
	 * @api
	 */
	countAll: function () {
		return this.objects.length;
	},

	/**
	 * Removes all objects of this repository as if remove() was called for
	 * all of them.
	 *
	 * @returns {void}
	 * @api
	 */
	removeAll: function () {
		this.removedObjects = this.removedObjects.concat(this.objects);
		this.objects = [];
	},

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param	{Integer} uid The identifier of the object to find
	 * @returns {Object} The matching object if found, otherwise NULL
	 * @api
	 */
	findByUid: function (uid) {
		var allObjectsLocal = this.allObjects,
			length = allObjectsLocal.length,
			currentObject,
			i = 0;

		do {
			currentObject = allObjectsLocal[i];
			if (currentObject.get('uid') === uid) {
				return currentObject;
			}
		} while (++i < length);
		return null;
	},

	/**
	 * Finds all objects matching the given value for the property.
	 *
	 * @param	{String}  property             The key of the property
	 * @param	{Mixed}   value                The value to compare against
	 * @param	{String}	comparisonOperator   A string defining the kind of comparison ('><')
	 * @returns {Array}                      Returns the array of filtered elements
	 */
	findWithProperty: function (property, value, comparisonOperator) {
		var filter = Iresults.Helpers.Filter.create();
		filter.addCondition({key: property, value: value, operator: comparisonOperator});
		return filter.searchRepository(this);
	},

	/**
	 * Finds the questionnaires
	 *
	 * @param	array	$properties	A dictionary containing property keys and
	 * values to filter. @see addComparisonsToQuery() for more details on the filter
	 * possibilities.
	 *
	 * @return	array
	 */
	findWithProperties: function (properties) {
		throw Iresults.Exception.create(1341582232, "Filters currently only support one condition.", arguments);
	},



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
	 * Returns the expected class name of the objects managed by the repository.
	 *
	 * @example:
	 *
	 * If the class name (this.__classIdentifier) of the repository is
	 * "Iresults.Domain.Repository.BookmarkRepository" the generated object type
	 * would be "Iresults.Domain.Model.Bookmark".
	 *
	 * @returns	{String}
	 */
	getObjectType: function () {
		if (!this.objectType) {
			var objectTypeLocal = this.__classIdentifier.replace(/_Repository_(?!.*_Repository_)/g, '_Model_');
			objectTypeLocal = objectTypeLocal.replace(/Repository$/, '');
			this.objectType = objectTypeLocal;
		}
		return this.objectType;
	},

	/**
	 * Returns a query for objects of this repository
	 *
	 * @returns {Object}
	 * @api
	 */
	createQuery: function () {
		throw Iresults.Exception.create(1341322201, "Querys are currently not implemented.", arguments);
	}
}, Iresults.Singleton);

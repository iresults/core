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
 * The Search Filter object to attach to a repository.
 * 
 * @include Iresults.Core
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Persistence
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Helpers.Filter', {
	/**
	 * The array of conditions for the filter.
	 * 
	 * @type {Array}
	 */
	conditions: null,
	
	/**
	 * Initialize the filter.
	 * 
	 * @returns	{void}
	 */
	init: function () {
		this.conditions = Ember.A();
	},
	
	/**
	 * Filters the given repository.
	 * 
	 * @param	  {Iresults.Persistence.Repository}	repository	The repository to filter
	 * @returns {Array} The filtered objects
	 */
	searchRepository: function (repository) {
		var objects = repository.allObjects.filter(this._filterFunction, this);
		repository.set('objects', objects);
		return objects;
	},
	
	/**
	 * Returns all the conditions of the filter.
	 * 
	 * @returns {Array}    An array of conditions
	 */
	getConditions: function (condition) {
		return this.conditions;
	},
	
	/**
	 * Add a condition to the set of filter rules.
	 * 
	 * @param	{Object} condition The condition to add
	 * 
	 * @returns {void}
	 */
	addCondition: function (condition) {
		if (this.conditions.length >= 1) {
			throw Iresults.Exception.create(1341582232, "Filters currently only support one condition.", arguments);
		}
		this.conditions.addObject(condition);
	},
	
	/**
	 * Set/overwrite the set of filter rules.
	 * 
	 * @param	{Object} conditions A storage filled with conditions
	 * 
	 * @returns {void}
	 */
	setConditions: function (conditions) {
		if (conditions.length > 1) {
			throw Iresults.Exception.create(1341582232, "Filters currently only support one condition.", arguments);
		}
		this.conditions = conditions;
	},
	
	/**
	 * Removes a condition from the set of filter rules.
	 * 
	 * @param	{Object} condition The condition to remove
	 * 
	 * @returns {void}
	 */
	removeCondition: function (condition) {
		this.conditions.removedObject(condition);
	},
	
	/**
	 * Removes all conditions.
	 * 
	 * @returns {void}
	 */
	removeAllConditions: function () {
		this.conditions = Ember.A();
	},
	
	/**
	 * The filter callback function.
	 * 
	 * @param	{Object}	item		The current item in the iteration
	 * @param	{Integer}	index		The current index in the iteration
	 * @param	{Object}	enumerable	The enumerable object itself
	 * @returns	{Boolean}				Returns TRUE if the object should be included in the result, otherwise FALSE
	 */
	_filterFunction: function (item, index, enumerable) {
		var propertyNameLocal = this.conditions[0].key,
			testValue = this.conditions[0].value,
			operator = this.conditions[0].operator,
			value = item.get(propertyNameLocal),
			likeExpression = null,
			result = false;
		
		if (typeof testValue === 'number') {
			operator = '==';
		} else if (typeof testValue === 'string' && /^[<=>]{2}/.test(propertyNameLocal)) {
			operator = propertyNameLocal.substr(0, 2);
		}
		
		switch (operator) {
		case '><': // Like
			likeExpression = new RegExp('.*' + testValue + '.*');
			if (likeExpression.test(value) || value == testValue) {
				result = true;
			}
			break;
				
		case '>=': // Greater or equal
			if (value >= testValue) {
				result = true;
			}
			break;
		
		case '>': // Greater
			if (value > testValue) {
				result = true;
			}
			break;
		
		case '<=': // Less or equal
			if (value <= testValue) {
				result = true;
			}
			break;
		
		case '<': // Less
			if (value < testValue) {
				result = true;
			}
			break;
		
		case '<>': // Not equal
			if (value !== testValue) {
				result = true;
			}
			break;
		
		case '==': // Equal
		default:
			if (value === testValue) {
				result = true;
			}
			break;
		}
		return result;
	}
}, Iresults.Core);

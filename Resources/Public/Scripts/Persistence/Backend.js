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
 * The Persistence Backend.
 *
 * @include Iresults.Core.Core
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Persistence
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Persistence.Backend', {
	/**
	 * The object that contains the arrays of objects.
	 *
	 * @type {Object}
	 */
	storage: {},

	/**
	 * Registers the given objects for the given repository name.
	 *
	 * @param	{Array}		objects			The objects that belong to the given repository
	 * @param	{Object}	repository		The repository or it's name
	 * @returns	{void}
	 */
	addStorageForRepository: function (objects, repository) {
		var repositoryName = repository;
		if (typeof repository === 'object') {
			repositoryName = repository.__classIdentifier;
		}
		this.storage[repositoryName] = objects;
	},

	/**
	 * Returns all the objects that belong to the given repository.
	 *
	 * @param	{Object}	repository	The repository for which to get the objects
	 * @returns	{Array}
	 */
	getStorageForRepository: function (repository) {
		var repositoryName = repository;
		if (typeof repository === 'object') {
			repositoryName = repository.__classIdentifier;
		}
		return this.storage[repositoryName];
	},

	/**
	 * Commits the current persistence session
	 * @return	void
	 */
	commit: function () {
		throw Iresults.Exception.create(1341330444, "Commiting changes is currently not implemented.", arguments);
	}
}, Iresults.Singleton);
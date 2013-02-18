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


/**
 * The Iresults.Ajax.Cached class is a specialized version of Iresults.Ajax,
 * which caches formerly loaded content.
 *
 * The class is intended to be used to make AJAX request, whose responds are not
 * expected to change. Instead of performing a similar request again, the
 * responds will  be read from a cache array.
 *
 * @include Iresults.Core.Ajax
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ajax
 * @version 2.0.1
 */
Iresults.Class.extend('Iresults.Ajax.Cached', {
	/**
	 * The cache for requests.
	 *
	 * @type {Object}
	 */
	requestCache: {},

	/**
	 * Saves the index of the childs in the target with the hash as the keys.
	 *
	 * @type {Object}
	 */
	hashToIndex: {},

	/**
	 * Indicates if the current run is a run with a cached request.
	 *
	 * @type {Boolean}
	 */
	runCached: false,

	/**
	 * The timeout that invokes the callbacks.
	 *
	 * @type {Object}
	 */
	performCallbacksTimeout: null,

	/**
	 * Make a AJAX request and replace the DOM object with the given ID with the data
	 * returned from the server.
	 *
	 * @param	{String} url		The URL to call
	 * @param	{String} targetId The ID of the DOM object to replace
	 * @param	{mixed} data		Optional data to pass to the server
	 *
	 * @return	{void}
	 */
	update: function (para) {
		var cachedResponse, hash;

		// Load the configurations from para
		this._loadConfigurationFromArgument.apply(this, arguments);

		// Prepare the URL
		if (!this.url) {
			this.url = document.URL;
		}

		console.log('Ajax.Cached.update');

		// If there is no cached value for the URL make a fresh request
		hash = this.getHash();
		cachedResponse = Iresults.Ajax.Cached.requestCache[hash];
		if (!cachedResponse) {
			console.log("No cache for " + hash, Iresults.Ajax.Cached.requestCache);
			this._super(para);
			return;
		}
		console.log("Run cached");
		this.performCallbacksWithCachedValue(cachedResponse);
	},

	/**
	 * Sends the last request again.
	 *
	 * @return	{void}
	 */
	refresh: function () {
		var cachedResponse;
		if (!this.url) {
			return;
		}

		// If there is no cached value for the URL make a fresh request
		cachedResponse = Iresults.Ajax.Cached.requestCache[this.url];
		if (!cachedResponse) {
			this._super();
			return;
		}
		this.performCallbacksWithCachedValue(cachedResponse);
	},

	/**
	 * Invokes the callbacks with the cached response.
	 *
	 * @param	{Object} response The response from the cache
	 * @returns {void}
	 */
	performCallbacksWithCachedValue: function (response) {
		var virtualAjaxParameterObject = {caller: this},
			_this = this;

		if (this.performCallbacksTimeout) {
			window.clearTimeout(this.performCallbacksTimeout);
		}
		this.performCallbacksTimeout = setTimeout(function () {
			_this.runCached = true;
			_this.success(response.data, response.textStatus, response.jqXHR, virtualAjaxParameterObject);
			_this.complete(response.jqXHR, response.textStatus, virtualAjaxParameterObject);
			_this.performCallbacksTimeout = null;
			_this.runCached = false;
		}, 1);
	},

	/**
	 * The standard success callback invokes the display method.
	 *
	 * @param	{mixed} data The data returned from the server, formatted according to the dataType parameter
	 * @param	{String} textStatus A string describing the status
	 * @param	{XMLHttpRequest} jqXHR
	 * @param	{Objekt} ajaxParameterObject		The AJAX parameter object
	 *
	 * @return	{void}
	 */
	success: function (data, textStatus, jqXHR, ajaxParameterObject) {
		if (!this.runCached && textStatus !== 'abort') {
			var hash = this.getHash();
			Iresults.Ajax.Cached.requestCache[hash] = {
				data: data,
				textStatus: textStatus,
				jqXHR: jqXHR
			};
		}
		return this._super(data, textStatus, jqXHR, ajaxParameterObject);
	},

	/**
	 * The complete callback is called when the request completed.
	 *
	 * @param	{XMLHttpRequest}	jqXHR
	 * @param	{String}			textStatus				A string describing the status
	 * @param	{Objekt}			ajaxParameterObject		The AJAX parameter object
	 * @return	{void}
	 */
	complete: function (jqXHR, textStatus, ajaxParameterObject) {
		// Fill the cache, if it hasn't been already set
		var hash = this.getHash();
		if (!this.runCached && textStatus !== 'abort' && !Iresults.Ajax.Cached.requestCache[hash]) {
			Iresults.Ajax.Cached.requestCache[hash] = {
				textStatus: textStatus,
				jqXHR: jqXHR
			};
		}
		this._super(jqXHR, textStatus, ajaxParameterObject);
	},

	/**
	 * Serializes and sends a form.
	 *
	 * @return	{boolean} false Returns false to prevent the page from reloading
	 */
	sendForm: function () {
		throw 'sendForm can not be called with Iresults.Ajax.Cached';
	},

	/**
	 * The success callback replaces the HTML-content of the target-ID object.
	 *
	 * @param	{mixed} data The data returned from the server, formatted according to the dataType parameter
	 * @param	{String} textStatus A string describing the status
	 * @param	{XMLHttpRequest} jqXHR
	 *
	 * @return	{void}
	 */
	display: function (data, textStatus, jqXHR) {
		var hash = '',
			index = -1,
			newIndex,
			targetLocal = this.target;

		if (!targetLocal) {
			targetLocal = $("#" + this.targetId).first();
			this.target = targetLocal;
			if (!targetLocal) {
				console.log("Couldn't fetch target");
				return;
			}
		}

		hash = this.getHash();
		index = this.hashToIndex[hash];
		if (index !== undefined) {
			targetLocal.children().hide();
			targetLocal.children(':nth-child(' + index + ')').show();
		} else {
			// Hide all old
			targetLocal.children().hide();

			// Add the new content
			newIndex = targetLocal.children().length + 1;
			this.willAppendData(data, newIndex);
			targetLocal.append(data);
			this.didAppendData(data, newIndex);
			this.hashToIndex[hash] = newIndex;
		}

		return;
	},

	/**
	 * Returns the hash of the current URL.
	 *
	 * @returns	{string}
	 */
	getHash: function () {
		return this.convertUrlToHash(this.url);
	},

	/**
	 * Converts the given URL to a hash.
	 *
	 * @param	{String}	url
	 * @returns	{String}		Returns the hash for the URL
	 */
	convertUrlToHash: function (url) {
		var typeNumRegularExpression,
			iresultsAjaxPidRegularExpression;

		// Replace the typeNum
		typeNumRegularExpression = new RegExp("[&|?]type=" + Iresults.Ajax.typeNum);
		url = url.replace(typeNumRegularExpression, '');

		// Replace the AJAX PID
		iresultsAjaxPidRegularExpression = new RegExp("[&|?]iresults_ajax_pid=\\d*");
		url = url.replace(iresultsAjaxPidRegularExpression, '');

		// Replace the L and sys_language_uid parameters
		url = url.replace(new RegExp("[&|?]sys_language_uid=\\d*"), '');
		url = url.replace(new RegExp("[&|?]L=\\d*"), '');

		return url.replace(/[^a-zA-Z0-9_\-]/g, '-');
	},

	/**
	 * The method is invoked before the data, that
	 * has just been loaded via AJAX is inserted into
	 * the DOM.
	 *
	 * @param	{mixed}	data	The data returned from the server, formatted according to the dataType parameter
	 * @param	{Integer}		newIndex The index of the new element
	 * @returns	{void}
	 */
	willAppendData: function (data, newIndex) {
	},

	/**
	 * The method is invoked after the data, that
	 * has just been loaded via AJAX is inserted into
	 * the DOM.
	 *
	 * @param	{mixed}		data		The data returned from the server, formatted according to the dataType parameter
	 * @param	{Integer}	newIndex	The index of the new element
	 * @returns	{void}
	 */
	didAppendData: function (data, newIndex) {
	}

}, Iresults.Ajax);
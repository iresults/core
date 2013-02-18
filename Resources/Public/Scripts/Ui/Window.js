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
 * A widget to display a window or dialog with either data from an URL or
 * the contents of a DIV.
 *
 * @include Iresults.Core.Ajax
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ui
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Ui.Window', {
	/**
	 * @type {Object}	The settings for the window
	 */
	settings: null,

//	/**
//     * @type {jQuery}	The jQuery object containing the window's content
//     */
//	target: null,

	/**
	 * Identifying what to use as the window's content.
	 *
	 * It is either a HTML element's ID or an URL.
	 *
     * @type {String}
     */
	what: '',

	/**
	 * Indicates if the content must be loaded from an URL.
	 *
     * @type {Boolean}
     */
	isUrl: false,

	/**
	 * The AJAX object
	 *
     * @type {Iresults.Ajax}
     */
	ajaxObject: null,

	/**
	 * Called when the class is initialized.
	 *
	 * @returns	{void}
	 */
	initialize: function () {
	},

	/**
	 * Creates a new instance.
	 *
	 * @param	   {Object} parameters The parameters for the window
	 * @returns	{Iresults.Ui.Window}
	 */
	init: function (parameters) {
		/*
		 * If the given parameter is a string, use it as the 'what' parameter
		 */
		if (typeof parameters === 'string') {
			this.what = parameters;
			return;
		}
		this.what = parameters.what;
		this.settings = parameters.settings || {};
	},

	/**
	 * Invoke the display of the window.
	 *
	 * @param	{Object}	sender	The sender (i.e. a clicked button)
	 * @returns	{void}
	 */
	makeKeyAndOrderFront: function (sender) {
		this.what = this.what;

		/*
		 * If what contains a slash, it has to be an URL, otherwise it is
		 * expected to be a HTML element's ID.
		 */
		if (this.what.search('/') === -1 && $(this.what).length) {
			this.show(this.what);
		} else {
			this.isUrl = true;
			this.loadContentFromUrl(this.what);
		}
	},

	/**
     * Load the window's content from the given URL.
     *
     * @param	{String}	url	The URL to load
     * @returns	{void}
     */
	loadContentFromUrl: function (url) {
		var para = {},
			ajaxObject = this.ajaxObject;

		// Create the AJAX object
		if (!ajaxObject) {
			ajaxObject = Iresults.Ajax.create();
		}

		// Configure the AJAX request
		para.url = url;
		para.targetId = this.convertUrlToHash(url);
		para.mode = Iresults.Ajax.MODE_EID;

		// Overwrite the AJAX object's display() method
		ajaxObject.display = this.ccb(this.display);

		// Perform the request
		ajaxObject.update(para);
		this.ajaxObject = ajaxObject;
	},

	/**
     * Returns the jQuery object containing the window's content.
     *
     * If this.what is an URL, a frech DIV will be created.
     *
     * @returns	{jQuery}
     */
	getTarget: function () {
		if (!this.element) {
			// If no URL must be loaded get the jQuery element
			if (this.isUrl === false) {
				this.element = $(this.what);
			} else {
				// Create a new target
				this.element = $('<div id="' + this.convertUrlToHash(this.what)  + '" style="display:none;margin:0;padding:0;" />');
				$('body').append(this.element);
			}
		}
		return this.element;
	},

	/**
     * Invoked when the AJAX request did finish loading.
     *
     * @returns	{void}
     */
	display: function (data, textStatus, jqXHR) {
		var targetLocal = this.getTarget();
		targetLocal.html(data);

		this.show();
	},

	/**
	 * Actually display the window.
	 *
	 * @returns	{void}
	 */
	show: function () {
		this.getTarget().dialog(this.settings);
	},

	/**
	 * Returns the settings
	 * @return	{Object}
	 */
	getSettings: function () {
		return this.settings;
	},

	/**
	 * Configure the window's settings.
	 *
	 * See http://jqueryui.com/demos/dialog/ for details about the available
	 * configurations.
	 *
	 * @param	{Object} newValue The new value to set
	 * @return	void
	 */
	setSettings: function (newValue) {
		this.settings = newValue;
	},

	/**
	 * Returns what to use as the window's content.
	 *
	 * It is either a HTML element's ID or an URL.
	 * @return	{String}
	 */
	getWhat: function () {
		return this.what;
	},

	/**
	 * Sets what to use as the window's content.
	 *
	 * It is either a HTML element's ID or an URL.
	 *
	 * @param	{String} newValue The new value to set
	 * @return	void
	 */
	setWhat: function (newValue) {
		this.what = newValue;
	},

	/**
	 * Converts the given URL to a hash.
	 *
	 * @param	{String}	url
	 * @returns	{String}		Returns the hash for the URL
	 */
	convertUrlToHash: function (url) {
		// Replace the typeNum
		var typeNumRegularExpression = new RegExp("[&|?]type=" + Iresults.Ajax.typeNum);
		url = url.replace(typeNumRegularExpression, '');
		return url.replace(/[^a-zA-Z0-9_\-]/g, '-');
	}
}, Iresults.Core);









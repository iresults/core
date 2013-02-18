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
 * The iresults Ajax class uses jQuery to make Ajax calls.
 */
$(document).ready(
	function () {
		Iresults.Ajax.getSharedInstance().initSubmitListener();
	}
);



/**
 * The iresults Ajax class uses jQuery to make AJAX calls. Additionally it
 * automatically handles the transmisson of forms with the CSS classes
 * "iresults_ajax_form" and "iresults_ajax_hot_form".
 *
 * If you set "iresults_ajax_form" as the CSS class of your form it will be sent
 * via AJAX on submit.
 *
 * If you set "iresults_ajax_hot_form" as the CSS class of your form it will
 * automatically be sent via AJAX every time you change the form.
 *
 * = Examples =
 *
 * <code>
 * var para = {},
 *     ajaxObject;
 *
 * // Create the AJAX object
 * ajaxObject = Iresults.Ajax.create();
 *
 * // Configure the AJAX request
 * para.url = 'http://whattolo.ad';
 * para.targetId = '#HTML_ID_that_will_be_filled_with_the_response';
 * para.loadingId = '#HTML_ID_that_will_be_shown_during_the_request';
 *
 * // Perform the request
 * ajaxObject.update(para);
 * </code>
 *
 * @include Iresults.Core
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ajax
 * @version 3.0.0
 */
Iresults.Class.extend('Iresults.Ajax', {
	/**
	 * In eID mode the eID (this.eId) is attached to the request URL.
	 *
	 * @constant {String}
	 */
	MODE_EID: 'MODE_EID',

	/**
	 * In typeNum mode the typeNum (this.typeNum) is attached to the request URL.
	 *
	 * @constant {String}
	 */
	MODE_TYPENUM: 'MODE_TYPENUM',

	/**
	 * In raw mode no data is automatically attached to the request. This mode
	 * has the same effect as setting this.sendRaw to TRUE.
	 *
	 * @constant {String}
	 */
	MODE_RAW: 'MODE_RAW',

	/**
	 * The typeNum of the iresults bootstrap AJAX typeNum that will handle the
	 * request through the iresults AJAX Bootstrap class (Iresults_Ajax_Bootstrap).
	 * The Ajax_server TypoScript setup file must be included.
	 */
	TYPE_NUM_WIDGET: 310005,

	/**
	 * The typeNum of the full page AJAX typeNum that will start the whole
	 * TemplaVoila rendering pipeline.
	 * The Ajax_server TypoScript setup file must be included.
	 */
	TYPE_NUM_TEMPLAVOILA: 310004,

	/**
	 * The version of the class.
	 *
	 * @type {String}
	 */
	__version: '3.0.0',

	/**
	 * Static property to prevent Iresults.Ajax to init the submit listeners
	 * multiple times.
	 *
	 * @var {Boolean}
	 */
	didInitSubmitListener: false,

	/**
	 * The URL to fetch
	 *
	 * @type {String}
	 */
	url: '',

	/**
	 * The ID of the HTML element in which the fetched content will be inserted
	 *
	 * @type {String}
	 */
	targetId: '',

	/**
	 * The jQuery object representing the target.
	 *
	 * @type {jQuery}
	 */
	target: null,

	/**
	 * If set, the HTML element with this ID will be shown during loading.
	 *
	 * @type {String}
	 */
	loadingId: '',

	/**
	 * Additional data sent with the request.
	 *
	 * @type {Object}
	 */
	data: '',

	/**
	 * The request method.
	 *
	 * @type {String}
	 */
	method: 'GET',

	/**
	 * The type of data, expected from the server.
	 *
	 * @type {String}
	 */
	dataType: 'html',

	/**
	 * Indicates if the raw data should be sent.
	 *
	 * @type {Boolean}
	 */
	sendRaw: false,

	/**
	 * Indicates if the browser should cache the result.
	 *
	 * @type {Boolean}
	 */
	cache: true,

	/**
	 * Defines which render pipe the TYPO3 server should use.
	 *
	 * MODE_EID: Pass the eID to the server to let him render the content in the
	 *           eID mode defined by this.eId.
	 *
	 * MODE_TYPENUM: Pass the typeNum to the server to let him render the
	 *               content with the type defined by this.typeNum.
	 *
	 * MODE_RAW: Don't attach any automatic data to the request. This mode
	 *           has the same effect as setting this.sendRaw to TRUE.
	 *
	 * @type {String|MODE}
	 */
	mode: null,

	/**
	 * Indicates if the requests should be made in developer mode.
	 *
	 * @type {Boolean}
	 */
	developerMode: false,

	/**
	 * The default iresults AJAX eID.
	 *
	 * @type {String}
	 */
	eId: 'iresults_ajax',

	/**
	 * The default iresults typeNum.
	 *
	 * @type {String}
	 */
	typeNum: 310004,

	/**
	 * The request object.
	 *
	 * @type {jqXHR}
	 */
	xhr: null,

	/**
	 * Indicates if Iresults.Config is available.
	 *
	 * @type {Boolean}
	 */
	iresultsConfigAvailable: -1,

	/**
	 * Called when the class is initialized.
	 *
	 * @returns	{void}
	 */
	initialize: function () {
		/*
		 * If this.iresultsConfigAvailable is -1 the class has not been
		 * initialized. In this case the default mode is set and the
		 * availability of Iresults.Config is checked.
		 */
		if (this.iresultsConfigAvailable === -1) {
			// Set the default mode
			this.setMode(this.MODE_TYPENUM);

			// Check the availability of Iresults.Config
			if (Iresults.Config) {
				this.iresultsConfigAvailable = true;
				this.developerMode = Iresults.Config.debug;
			} else {
				this.iresultsConfigAvailable = false;
			}
		}
	},

	/**
	 * Creates a new instance
	 *
	 * @param	{Object} Optional parameters
	 * @returns {Object}
	 */
	create: function (para) {
		var instance = this._super();

		if (instance.developerMode) {
			console.log('New AJAX instance', instance);
		}

		/**
		 * @see https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Function/apply
		 */
		instance._loadConfigurationFromArgument.apply(instance, arguments);

		return instance;
	},

	/**
	 * Registers a submit listener for each DOM object with class "iresults_ajax_form".
	 *
	 * @return	{Iresults.Ajax}
	 */
	initSubmitListener: function () {
		if (!Iresults.Ajax.didInitSubmitListener) {
			/*
			 * Init the listener for the normal AJAX forms.
			 */
			$("form.iresults_ajax_form").submit(this.sendForm);

			/*
			 * Init the listeners for the "hot" AJAX forms.
			 */
			$("form.iresults_ajax_hot_form input").change(this.sendForm);
			$("form.iresults_ajax_hot_form").submit(this.sendForm);

			Iresults.Ajax.didInitSubmitListener = true;
		}
		return this;
	},

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
		var addition = '?',
			ajaxParameter;

		/**
		 * @see https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Function/apply
		 */
		if (!this._loadConfigurationFromArgument.apply(this, arguments)) {
		//	return;
		}

		/*
		 * Prepare the URL
		 */
		if (!this.url) {
			this.url = document.URL;
		}

		if (this.getMode() === Iresults.Ajax.MODE_RAW) {
			this.sendRaw = true;
		}

		if (!this.sendRaw) {
			if (this.url.search(/\?/) !== -1) {
				addition = '&';
			}

			// Add the eID to the request
			if (this.getMode() === Iresults.Ajax.MODE_EID &&
				this.eId &&
				this.url.search("eID=") === -1) {
				this.url = this.url + addition + "eID=" + this.eId;
			} else

			// Add the typeNum to the request
			if (this.getMode() === Iresults.Ajax.MODE_TYPENUM &&
				this.typeNum &&
				this.url.search("type=") === -1) {
				this.url = this.url + addition + "type=" + this.typeNum;
			}

			// Add the configurations from Iresults.Config
			if (this.iresultsConfigAvailable) {
				// Add the current PID to the URL
				if (Iresults.Config.pid) {
					this.url = this.url + '&iresults_ajax_pid=' + Iresults.Config.pid;
				}

				// Add the current language
				if (Iresults.Config.sys_language_uid  && this.url.search("sys_language_uid=") === -1 && this.url.search("L=") === -1) {
					this.url = this.url + '&sys_language_uid=' + Iresults.Config.sys_language_uid + '&L=' + Iresults.Config.sys_language_uid;
				}
			}
		}

		/*
		 * Show the loading object.
		 */
		if (this.loadingId) {
			this.showLoading();
		}

		/*
		 * Debug the current request, if the developer mode is on
		 */
		if (this.developerMode) {
			console.log("Will send AJAX request to URL " + this.url);
			console.log(this);
		}

		/*
		 * Create the AJAX parameter object.
		 * @type object ajaxParameter
		 */
		ajaxParameter = this._createAjaxParameterObject();

		try {
			// If no status is set, abort the old request
			if (this.xhr && typeof this.xhr.status === 'undefined') {
				this.xhr.abort();
			}

			// Send the request
			this.xhr = jQuery.ajax(ajaxParameter);
		} catch (e) {
			console.log("Error while trying to make AJAX call", e);
			if (this.developerMode) {
				throw e;
			}
		}
		return;
	},

	/**
	 * Sends the last request again.
	 *
	 * @return	{void}
	 */
	refresh: function () {
		if (!this.url) {
			return;
		}

		/*
		 * Create the AJAX parameter object.
		 * @type object ajaxParameter
		 */
		var ajaxParameter = this._createAjaxParameterObject();

		// Die Ajax-Anfrage senden
		try {
			// If no status is set, abort the old request
			if (this.xhr && typeof this.xhr.status === 'undefined') {
				this.xhr.abort();
			}

			// Send the request
			this.xhr = jQuery.ajax(ajaxParameter);
		} catch (e) {
			console.log("Error while trying to make AJAX call", e);
			if (this.developerMode) {
				throw e;
			}
		}
		return;
	},

	/**
	 * Cancels/aborts the current request, if it isn't finished.
	 *
	 * @returns {void}
	 */
	cancel: function () {
		// If no status is set, abort the old request
		if (this.xhr && typeof this.xhr.status === 'undefined') {
			this.xhr.abort();
		}
	},


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FORM AND HELPER METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Serializes and sends a form.
	 *
	 * @return	{boolean} false Returns false to prevent the page from reloading
	 */
	sendForm: function () {
		/*
		 * Get the form (if this is not one)
		 */
		var form = this,
			formsJQ = null,
			data,
			irAjax;

		if (this.nodeName !== 'FORM') {
			formsJQ = $(this).closest("form");
			if (formsJQ.length > 0) form = formsJQ[0];
		} else {
			formsJQ = $(this);
		}
		data = formsJQ.serialize();
		irAjax = Iresults.Ajax.create();

		try {
			irAjax.update({
				url: form.action,
				targetId: form.id + "_output",
				data: data,
				method: form.method,
				loadingId: form.id + "_loading"
			});
		} catch (e) {
			console.log("Error while sending form", e);
		}

		/*
		 * Return TRUE if this is anything other than a form.
		 * This is needed by Internet Explorer i.e. to keep the checkbox checked.
		 */
		if (this.nodeName !== 'FORM') {
			return true;
		}
		return false;
	},

	/**
	 * Toggle the visibility of the loading element.
	 *
	 * @returns {void}
	 */
	toggleLoading: function () {
		/*
		 * Toggle the loading object.
		 */
		if (this.loadingId && $("#" + this.loadingId)) {
			$("#" + this.loadingId).toggle();
		}
	},

	/**
	 * Hide the loading element.
	 *
	 * @returns {void}
	 */
	hideLoading: function () {
		/*
		 * Toggle the loading object.
		 */
		if (this.loadingId && $("#" + this.loadingId)) {
			$("#" + this.loadingId).hide();
		}
	},

	/**
	 * Show the loading element.
	 *
	 * @returns {void}
	 */
	showLoading: function () {
		/*
		 * Toggle the loading object.
		 */
		if (this.loadingId && $("#" + this.loadingId)) {
			$("#" + this.loadingId).show();
		}
	},



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* CALLBACKS            WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * The standard success callback invokes the display method.
	 *
	 * @param	{mixed} data The data returned from the server, formatted according to the dataType parameter
	 * @param	{String} textStatus A string describing the status
	 * @param	{XMLHttpRequest} jqXHR
	 * @param	  {Objekt} ajaxParameterObject		The AJAX parameter object
	 *
	 * @return	{void}
	 */
	success: function (data, textStatus, jqXHR, ajaxParameterObject) {
		return this.display(data, textStatus, jqXHR);
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
		var targetLocal = this.target;
		if (!targetLocal) {
			targetLocal = $("#" + this.targetId).first();
			this.target = targetLocal;
		}
		if (targetLocal) {
			/**
			 * @TODO: This eats a lot of performance
			 */
			targetLocal.html(data);
		}
	},

	/**
	 * The error callback is called if the request fails.
	 *
	 * @param	  {XMLHttpRequest} jqXHR
	 * @param	  {String} textStatus		A string describing the type of error that occurred
	 * @param	  {mixed} errorThrown		An optional exception object, if one occurred
	 * @param	  {Objekt} ajaxParameterObject		The AJAX parameter object
	 *
	 * @return	{void}
	 */
	error: function (jqXHR, textStatus, errorThrown, ajaxParameterObject) {
		if (this.developerMode) {
			console.log("AJAX ERROR");
			console.log(jqXHR);
			console.log(textStatus);
			console.log(errorThrown);
		}
		if (jqXHR.responseText) {
			var targetLocal = this.target;
			if (!targetLocal) {
				targetLocal = $("#" + this.targetId).first();
				this.target = targetLocal;
			}
			if (targetLocal) {
				/**
				 * @TODO: This eats a lot of performance
				 */
				targetLocal.html(jqXHR.responseText);
			}
		}
	},

	/**
	 * The complete callback is called when the request completed.
	 *
	 * @param	  {XMLHttpRequest} jqXHR
	 * @param	  {String} textStatus		A string describing the status
	 * @param	  {Objekt} ajaxParameterObject		The AJAX parameter object
	 *
	 * @return	{void}
	 */
	complete: function (jqXHR, textStatus, ajaxParameterObject) {
		this.hideLoading();
	},



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* PROTECTED METHODS    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the AJAX parameter object for the current configuration.
	 *
	 * @returns {Object} The AJAX parameter object
	 */
	_createAjaxParameterObject: function () {
		var _this = this,
			successFunctionCallback,
			completeFunctionCallback,
			errorFunctionCallback,
			ajaxParameter;

		/*
		 * Build the function callbacks.
		 */
		successFunctionCallback		= function (data, textStatus, jqXHR) {			_this.success(data, textStatus, jqXHR, this); };
		completeFunctionCallback	= function (jqXHR, textStatus) {				_this.complete(jqXHR, textStatus, this); };
		errorFunctionCallback		= function (jqXHR, textStatus, errorThrown) {	_this.error(jqXHR, textStatus, errorThrown, this); };

		/*
		 * Create the AJAX parameter object.
		 * @type object ajaxParameter
		 */
		ajaxParameter = {
			processData: true,
			targetId: this.targetId,
			loadingId: this.loadingId,
			url: this.url,
			type: this.method,
			data: this.data,
			cache: this.cache,
			dataType: this.dataType,
			error: errorFunctionCallback,
			success: successFunctionCallback,
			complete: completeFunctionCallback,
			caller: this
		};
		return ajaxParameter;
	},

	/**
	 * Load the configuration.
	 *
	 * @return	{boolean} Returns TRUE if configuration was loaded, otherwise FALSE
	 */
	_loadConfigurationFromArgument: function (para) {
		var result = false,
			argArray;

		/**
		 * Check if multiple arguments have been passed or an object
		 */
		if (arguments.length >= 2) { // Multiple arguments
			argArray = arguments;
			this.url		= argArray[0];
			this.targetId	= argArray[1];
			this.data		= argArray[2];
			result = true;
		} else if (arguments.length === 1 && typeof para === 'object') { // Object as argument
			this.url		= para.url;
			this.targetId	= para.targetId;
			this.data		= para.data;
			this.loadingId	= para.loadingId;

			if (para.dataType)	this.dataType = para.dataType;
			if (para.method)	this.method = para.method;
			if (para.sendRaw)	this.sendRaw = para.sendRaw;
			if (para.mode)		this.mode = para.mode;
			if (para.complete)	this.complete = para.complete;
			result = true;
		} else if (arguments.length === 1) { /* The only argument passed is assumed to
											be the target ID */
			this.targetId	= para;
			result = true;
		}

		return result;
	},


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* ACCESSOR METHODS    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the current mode of the AJAX object.
	 *
	 * @return	{String} The current mode as one of the Iresults.Ajax.MODE
	 * constants.
	 */
	getMode: function () {
		return this.mode;
	},

	/**
	 * Sets the current mode of the AJAX object.
	 *
	 * @param	  {String} The new mode as one of the Iresults.Ajax.MODE constants
	 *
	 * @return	{void}
	 */
	setMode: function (newMode) {
		this.mode = newMode;
	},

	/**
	 * Returns if the AJAX request should use the browser cache.
	 * @return	boolean
	 */
	getCache: function () {
		return this.cache;
	},

	/**
	 * Set if the AJAX request should use the browser cache.
	 *
	 * @param	boolean newValue The new value to set
	 * @return	void
	 */
	setCache: function (newValue) {
		this.cache = newValue;
	},

	/**
	 * Returns the ID of the DOM object to show while requesting the data.
	 * @return	string
	 */
	getLoadingId: function () {
		return this.loadingId;
	},

	/**
	 * Set the ID of the DOM object to show while requesting the data.
	 *
	 * @param	string newValue The new value
	 * @return	void
	 */
	setLoadingId: function (newValue) {
		this.loadingId = newValue;
	},

	/**
	 * Returns the complete callback function.
	 * @return	Function
	 */
	getCompleteFunction: function () {
		return this.complete;
	},

	/**
	 * Setter for the complete callback function.
	 *
	 * @param	Function callback The new value to set
	 * @return	void
	 */
	setCompleteFunction: function (callback) {
		this.complete = callback;
	},

	/**
	 * Returns the success callback function.
	 * @return	Function
	 */
	getSuccessFunction: function () {
		return this.success;
	},

	/**
	 * Setter for the success callback function.
	 *
	 * @param	Function callback The new value to set
	 * @return	void
	 */
	setSuccessFunction: function (callback) {
		this.success = callback;
	},

	/**
	 * Returns the error callback function.
	 * @return	Function
	 */
	getErrorFunction: function () {
		return this.error;
	},

	/**
	 * Setter for returns the error callback function.
	 *
	 * @param	Function callback The new value to set
	 * @return	void
	 */
	setErrorFunction: function (callback) {
		this.error = callback;
	},

	/**
	 * Returns the underlying request object.
	 *
	 * @returns {jqXHR}
	 */
	getRequest: function () {
		return this.xhr;
	}
}, Iresults.Singleton);

/**
 * Declare Iresults_Ajax for transition.
 *
 * @returns {Iresults.Ajax}
 */
var Iresults_Ajax = function () {
	return Iresults.Ajax;
};
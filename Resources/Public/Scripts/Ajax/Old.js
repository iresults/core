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
 * Return true if a is greater than or equal to b.
 *
 * @param	{String} a Version 1
 * @param	{String} b Version 2
 * @returns {Boolean}
 */
if (!window.compareVersions) {
	function compareVersions(a,b){var c=a.split(".");var d=b.split(".");for(var e=0;e<c.length;++e){c[e]=Number(c[e])}for(var e=0;e<d.length;++e){d[e]=Number(d[e])}if(c.length==2){c[2]=0}if(c[0]>d[0])return true;if(c[0]<d[0])return false;if(c[1]>d[1])return true;if(c[1]<d[1])return false;if(c[2]>d[2])return true;if(c[2]<d[2])return false;return true}
}

// Check if the new Ajax class is already loaded
if(Iresults.Ajax && Iresults.Ajax.__version && compareVersions(Iresults.Ajax.__version, '3.0.0')){
	throw "The current iresults Ajax and the old version must not be used simultaneously";
}


/**
 * The iresults Ajax class uses jQuery to make Ajax calls.
 */
$(document).ready(function(){
	var irAjax = Iresults.Ajax.initSubmitListener();
});

console.log('[INFO] You are using the old iresults AJAX API');


/**
 * This is the old version of the iresults Ajax class.
 *
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
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ajax
 * @version 2.0.1
 */
Iresults.Ajax = {
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
	 * The version of the class.
	 *
	 * @type {String}
	 */
	__version: '2.0.1',

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
	 * The complete function callback.
	 *
	 * @type {Function}
	 */
	completeFunctionCallback: null,

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
	typeNum: '310004',

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
	 * Creates a new instance
	 *
	 * @param	{Object} Optional parameters
	 * @returns {Object}
	 */
	create: function(para){
		/*
		 * If this.iresultsConfigAvailable is -1 the class has not been
		 * initialized. In this case the default mode is set and the
		 * availability of Iresults.Config is checked.
		 */
		if(this.iresultsConfigAvailable === -1){
			console.log(Iresults)
			// Set the default mode
			this.setMode(this.MODE_TYPENUM);

			// Check the availability of Iresults.Config
			if(window.Iresults && Iresults.Config){
				this.iresultsConfigAvailable = true;
				this.developerMode = Iresults.Config.debug;
			} else {
				this.iresultsConfigAvailable = false;
			}
		}

		// Create the instance
		var instance = jQuery.extend(true, {}, this);

		if(!para || !para.autoInitSubmitListener){
			instance.initSubmitListener();
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
	 * @return	{void}
	 */
	initSubmitListener: function(){
		if(!Iresults.Ajax.didInitSubmitListener){
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
	update: function(para){
		var addition = '?';

		/**
		 * @see https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Function/apply
		 */
		if(!this._loadConfigurationFromArgument.apply(this, arguments)){
		//	return;
		}


		/*
		 * Prepare the URL
		 */
		if(!this.url){
			this.url = document.URL;
		}

		if(this.getMode() == Iresults.Ajax.MODE_RAW){
			this.sendRaw = true;
		}



		if(!this.sendRaw){
			if(this.url.search(/\?/) != -1){
				addition = '&';
			}

			// Add the eID to the request
			if(this.getMode() == Iresults.Ajax.MODE_EID &&
			   this.eId &&
			   this.url.search("eID=") == -1){
				this.url = this.url + addition + "eID=" + this.eId;
			} else

			// Add the typeNum to the request
			if(this.getMode() == Iresults.Ajax.MODE_TYPENUM &&
			   this.typeNum &&
			   this.url.search("type=") == -1){
				this.url = this.url + addition + "type=" + this.typeNum;
			}

			// Add the configurations from Iresults.Config
			if(this.iresultsConfigAvailable){
				// Add the current PID to the URL and set the developerMode
				if(Iresults.Config.pid){
					this.url = this.url + '&iresults_ajax_pid=' + Iresults.Config.pid;
				}

				// Add the current language
				if(Iresults.Config.sys_language_uid  && this.url.search("sys_language_uid=") == -1 && this.url.search("L=") == -1){
					this.url = this.url + '&sys_language_uid=' + Iresults.Config.sys_language_uid + '&L=' + Iresults.Config.sys_language_uid;
				}
			}
		}

		/*
		 * Show the loading object.
		 */
		if(this.loadingId){
			this.showLoading();
		}

		/*
		 * Debug the current request, if the developer mode is on
		 */
		if(this.developerMode){
			console.log("Will send AJAX request to URL " + this.url);
			console.log(this);
		}

		/*
		 * Create the AJAX parameter object.
		 * @type object ajaxParameter
		 */
		var ajaxParameter = this._createAjaxParameterObject();

		try{
			// If no status is set, abort the old request
			if(this.xhr && typeof this.xhr.status == 'undefined'){
				this.xhr.abort();
			}

			// Send the request
			this.xhr = jQuery.ajax(ajaxParameter);
		} catch(e){
			console.log("Error while trying to make AJAX call", e);
			if(this.developerMode){
				throw e
			}
		}
		return;
	},

	/**
	 * Sends the last request again.
	 *
	 * @return	{void}
	 */
	refresh: function(){
		if(!this.url){
			return;
		}

		/*
		 * Create the AJAX parameter object.
		 * @type object ajaxParameter
		 */
		var ajaxParameter = this._createAjaxParameterObject();

		// Die Ajax-Anfrage senden
		try{
			// If no status is set, abort the old request
			if(this.xhr && typeof this.xhr.status == 'undefined'){
				this.xhr.abort();
			}

			// Send the request
			this.xhr = jQuery.ajax(ajaxParameter);
		} catch(e){
			console.log("Error while trying to make AJAX call", e);
			if(this.developerMode){
				throw e
			}
		}
		return;
	},



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FORM AND HELPER METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Serializes and sends a form.
	 *
	 * @return	{boolean} false Returns false to prevent the page from reloading
	 */
	sendForm: function(){
		/*
		 * Get the form (if this is not one)
		 */
		var form = this;
		var formsJQ = null;
		if(this.nodeName !== 'FORM'){
			formsJQ = $(this).closest("form")
			if(formsJQ.length > 0) form = formsJQ[0];
		} else {
			formsJQ = $(this)
		}
		var data = formsJQ.serialize();
		var irAjax = Iresults.Ajax.create();

		try{
			irAjax.update({
				url: form.action,
				targetId: form.id + "_output",
				data: data,
				method: form.method,
				loadingId: form.id + "_loading"
			});
		} catch(e){
			console.log("Error while sending form", e)
		}

		/*
		 * Return TRUE if this is anything other than a form.
		 * This is needed by Internet Explorer i.e. to keep the checkbox checked.
		 */
		if(this.nodeName !== 'FORM'){
			return true;
		}
		return false;
	},

	/**
	 * Toggle the visibility of the loading element.
	 *
	 * @returns {void}
	 */
	toggleLoading: function(){
		/*
		 * Toggle the loading object.
		 */
		if(this.loadingId && $("#" + this.loadingId)){
			$("#" + this.loadingId).toggle();
		}
	},

	/**
	 * Hide the loading element.
	 *
	 * @returns {void}
	 */
	hideLoading: function(){
		/*
		 * Toggle the loading object.
		 */
		if(this.loadingId && $("#" + this.loadingId)){
			$("#" + this.loadingId).hide();
		}
	},

	/**
	 * Show the loading element.
	 *
	 * @returns {void}
	 */
	showLoading: function(){
		/*
		 * Toggle the loading object.
		 */
		if(this.loadingId && $("#" + this.loadingId)){
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
	success: function(data, textStatus, jqXHR, ajaxParameterObject){
		return ajaxParameterObject.caller.display(data, textStatus, jqXHR);
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
	display: function(data, textStatus,jqXHR){
		// this zeigt auf das Ajax-Objekt

		/*
		 * Display the new data.
		 */
		if(this.targetId){
			$("#" + this.targetId).html(data);
		}
		return;
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
	error: function(jqXHR, textStatus, errorThrown, ajaxParameterObject){
		// Do nothing
		if(this.developerMode){
			console.log("AJAX ERROR:\n" + jqXHR + " \n " + textStatus + " \n " + errorThrown)
		}
		$("#" + this.targetId).html(jqXHR.responseText)
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
	completeFunction: function(jqXHR, textStatus, ajaxParameterObject){
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
	_createAjaxParameterObject: function(){
		/*
		 * Build the function callbacks.
		 */
		var successFunctionCallback 	= function(data, textStatus,jqXHR){this.caller.success(data, textStatus,jqXHR, this)}
		var completeFunctionCallback 	= function(jqXHR, textStatus){this.caller.completeFunction(jqXHR, textStatus, this)}
		var errorFunctionCallback 		= function(jqXHR, textStatus, errorThrown){this.caller.error(jqXHR, textStatus, errorThrown, this)}

		/*
		 * Create the AJAX parameter object.
		 * @type object ajaxParameter
		 */
		var ajaxParameter = {
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
		}
		return ajaxParameter;
	},

	/**
	 * Load the configuration.
	 *
	 * @return	{boolean} Returns TRUE if configuration was loaded, otherwise FALSE
	 */
	_loadConfigurationFromArgument: function(para){
		var result = false;

		/**
		 * Check if multiple arguments have been passed or an object
		 */
		if(arguments.length >= 2){ // Multiple arguments
			var argArray = arguments;
			this.url 		= argArray[0];
			this.targetId 	= argArray[1];
			this.data 		= argArray[2];
			result = true;
		} else if(arguments.length == 1 && typeof para == 'object' ){ // Object as argument
			this.url 		= para.url;
			this.targetId 	= para.targetId;
			this.data 		= para.data;
			this.loadingId 	= para.loadingId;

			if(para.dataType)	this.dataType = para.dataType;
			if(para.method) 	this.method = para.method;
			if(para.sendRaw) 	this.sendRaw = para.sendRaw;
			if(para.mode) 		this.mode = para.mode;
			if(para.completeFunction)	this.completeFunction = para.completeFunction;
			result = true;
		} else if(arguments.length == 1){ /* The only argument passed is assumed to
											be the target ID */
			this.targetId 	= para;
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
	getMode: function(){
		return this.mode;
	},

	/**
	 * Sets the current mode of the AJAX object.
	 *
	 * @param	  {String} The new mode as one of the Iresults.Ajax.MODE constants
	 *
	 * @return	{void}
	 */
	setMode: function(newMode){
		this.mode = newMode;
	},

	/**
	 * Returns if the AJAX request should use the browser cache.
	 * @return	boolean
	 */
	getCache: function(){
		return this.cache;
	},

	/**
	 * Set if the AJAX request should use the browser cache.
	 *
	 * @param	boolean newValue The new value to set
	 * @return	void
	 */
	setCache: function(newValue){
		this.cache = newValue;
	},

	/**
	 * Returns the ID of the DOM object to show while requesting the data.
	 * @return	string
	 */
	getLoadingId: function(){
		return this.loadingId;
	},

	/**
	 * Set the ID of the DOM object to show while requesting the data.
	 *
	 * @param	string newValue The new value
	 * @return	void
	 */
	setLoadingId: function(newValue){
		this.loadingId = newValue;
	},

	/**
	 * Returns the complete callback function.
	 * @return	Function
	 */
	getCompleteFunction: function(){
		return this.completeFunction;
	},

	/**
	 * Setter for the complete callback function.
	 *
	 * @param	Function callback The new value to set
	 * @return	void
	 */
	setCompleteFunction: function(callback){
		this.completeFunction = callback;
	},

	/**
	 * Returns the success callback function.
	 * @return	Function
	 */
	getSuccessFunction: function(){
		return this.success;
	},

	/**
	 * Setter for the success callback function.
	 *
	 * @param	Function callback The new value to set
	 * @return	void
	 */
	setSuccessFunction: function(callback){
		this.success = callback;
	},

	/**
	 * Returns the error callback function.
	 * @return	Function
	 */
	getErrorFunction: function(){
		return this.error;
	},

	/**
	 * Setter for returns the error callback function.
	 *
	 * @param	Function callback The new value to set
	 * @return	void
	 */
	setErrorFunction: function(callback){
		this.error = callback;
	},

	/**
	 * Returns the underlying request object.
	 *
	 * @returns {jqXHR}
	 */
	getRequest: function(){
		return this.xhr;
	}
}

/**
 * Declare Iresults_Ajax for transition.
 *
 * @returns {Iresults.Ajax}
 */
Iresults_Ajax = function(){
	return Iresults.Ajax;
}
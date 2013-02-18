/**************************************************************
*  Copyright notice
*
* (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
*          Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
*  All rights reserved
*
*  Permission is hereby granted, free of charge, to any person obtaining a copy
*  of this software and associated documentation files (the "Software"), to deal
*  in the Software without restriction, including without limitation the rights
*  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*  copies of the Software, and to permit persons to whom the Software is
*  furnished to do so, subject to the following conditions:
*
*  The above copyright notice and this permission notice shall be included in
*  all copies or substantial portions of the Software.
*
*  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
*  THE SOFTWARE.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * jQuery is required.
 *
 * @include Iresults.Core.jquery-1:8:3
 * @include Iresults.Core.jquery-ui-1:9:2
 */

/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* NAMESPACES             WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * Declare the nil object.
 *
 * @type {Object}
 */
var nil = window.nil || {};

/**
 * Create the Iresults namespace.
 *
 * @type {Object}
 */
var Iresults = window.Iresults || {};

/**
 * Declares a namespace.
 *
 * The function makes sure that the given namespace exists.
 * Thanks to Jan Van Ryswyck (http://elegantcode.com/2011/01/26/basic-javascript-part-8-namespaces/)
 *
 * @param	{String}	namespaceDefinition	The namespace in the format 'MyComany.Foo.(...).Model'
 * @returns	{Object}					The namespace object
 */
function namespace(namespaceDefinition) {
	var i, parts,
		length,
		parent = window,
		currentPart = '';

	if (typeof namespaceDefinition === 'string') {
		parts = namespaceDefinition.split('.');
	} else {
		parts = namespaceDefinition;
	}

	length = parts.length;
    for (i = 0; i < length; i++) {
        currentPart = parts[i];
		currentPart = currentPart.toString();

		parent[currentPart] = parent[currentPart] || {};
        parent = parent[currentPart];
    }

    return parent;
}



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* EXTENSIONS OF NATIVE JAVASCRIPT CODE    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * Create the console class and instance.
 *
 * @type {Object}
 */
var Console = window.Console || {};
if (typeof console === 'undefined') {
	Console.log = function () {};
	Console.debug = Console.log;
	Console.error = Console.log;
	Console.info = Console.log;
	Console.warn = Console.log;
	var console = Console;
}

/**
 * Defining extensions for the JavaScript runtime.
 */
if (!Array.indexOf) {
	/**
	 * Returns the index of the first occurrence of an item in an array, or -1
	 * if it doesn't exist.
	 *
	 * @param	{Array} obj The array to test
	 * @param	{Object} start The object to look for
	 * @returns {Integer}
	 */
	Array.prototype.indexOf = function (obj, start) {
		for (var i = (start || 0); i < this.length; i++) {
			if (this[i] === obj) {
				return i;
			}
		}
		return -1;
	};
}



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* CORE OBJECT            WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * The iresults Core class uses provides some core functionalities.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 2.0.0
 */
Iresults.Core = {
	/**
	 * The version of the class.
	 *
	 * @type {String}
	 */
	__version: '2.0.0',

	/**
	 * The objects class name.
	 *
	 * @type {String}
	 */
	__classIdentifier: 'Iresults.Core',

	/**
	 * Indicates if the object is an instance or a class.
	 *
	 * @type {Boolean}
	 */
	__isInstance: false,

	/**
	 * The unique identfier of this instance.
	 *
	 * @type {String}
	 */
	__uuid: 0,

	/**
	 * The reference to the DOM node.
	 *
	 * @property {jQuery}
	 */
	element: null,

	/**
	 * The objects and it's associated DOM element's unique ID
	 *
	 * @type {integer}
	 */
	uid: 0,

	/**
	 * Called when the class is initialized.
	 *
	 * @returns	{void}
	 */
	initialize: function () {
	},

	/**
	 * Creates a new instance
	 *
	 * @returns {Object}
	 */
	create: function () {
		var instance = Iresults.Core.extend({}, this);
		instance.__isInstance = true;
		instance.__uuid = +new Date();
		instance.init.apply(instance, arguments);
		return instance;
	},

	/**
	 * Invoked when a new instance is created
	 *
	 * @returns {Object}
	 */
	init: function () {
	},

	/**
	 * An empty function.
	 *
	 * @returns {Object}  Returns this
	 */
	ef: function () {
		return this;
	},

	///**
	// * The super method invokes the super method, of the current method.
	// *
	// * @returns {Object}  Returns the super methods return value
	// */
	//_super: function () {
	//	var methodName = '', propertyName,
	//		methodImp,
	//		subMethod = this._super.caller;
	//
	//	// Find the method name
	//	for (propertyName in this) {
	//		if (typeof this[propertyName] === 'function' && this[propertyName] === subMethod) {
	//			methodName = propertyName;
	//		}
	//	}
	//
	//	methodImp = this.__super__[methodName];
	//	if (!methodImp) {
	//		throw "Method " + methodName + " doesn't exist";
	//	}
	//	return methodImp.apply(this, arguments);
	//},

	/**
	 * The super method invokes the super method, of the current method.
	 *
	 * @returns {Object}  Returns the super methods return value
	 */
	_super: function () {
		var methodImp,
			methodName = '',
			subMethod = this._super.caller;

		if (!this.__super__) {
			return this;
		}

		// Find the method name
		methodName = this.__super_getMethodName(this, subMethod);
		methodImp = this.__super_getMethodImplementation(this.__super__, methodName, subMethod);

		return methodImp.apply(this, arguments);
		/*
		// Find the method name
		for (propertyName in this) {
			if (typeof this[propertyName] === 'function' && this[propertyName] === subMethod) {
				methodName = propertyName;
			}
		}
		console.log(methodName);

		methodImp = this.__super__[methodName];
		if (!methodImp) {
			throw "Method " + methodName + " doesn't exist";
		}
		return methodImp.apply(this, arguments);
		// */
	},

	/**
	 * Returns the method name for the given
	 * implementation.
	 *
	 * @param	{Function}	imp	The implementation to search for
	 * @returns	{String}		The method name
	 */
	__super_getMethodName: function (impTable, imp) {
		var propertyName,
			methodName = '';

		// Find the method name
		for (propertyName in impTable) {
			if (typeof impTable[propertyName] === 'function' && impTable[propertyName] === imp) {
				methodName = propertyName;
			}
		}
		if (!methodName) {
			if (impTable.__super__) {
				methodName = this.__super_getMethodName(impTable.__super__, imp);
			} else {
				throw Iresults.Exception.create(1340376396178, "Couldn't determine method name", [impTable, imp]);
			}
		}
		return methodName;
	},

	/**
	 * Searches the method in the class hierarchy
	 * and returns the implementation if one is
	 * found.
	 *
	 * @param	{Object}		impTable							The super class
	 * @param	{String}			methodName					The method name to get the implementation for
	 * @param	{Function}	originalImplementation	The original method implementation
	 * @returns	{Function}	Returns the super function implementation
	 */
	__super_getMethodImplementation: function (impTable, methodName, originalImplementation) {
		var methodImp;
		methodImp = impTable[methodName];

		if (!originalImplementation) {
			throw Iresults.Exception.create(1340377529061, "Required argument 'originalImplementation' not given");
		}

		/*
		 * If the method implementation was not
		 * found or it was found but is identical to
		 * the given imp, and the imp table has a
		 * __super__ property, search the
		 * implementation in __super__
		 */
		if ((!methodImp || methodImp === originalImplementation) && impTable.__super__) {
			return this.__super_getMethodImplementation(impTable.__super__, methodName, originalImplementation);
		}

		if (!methodImp || methodImp === originalImplementation) {
			console.log("__super_getMethodImplementation");
			throw Iresults.Exception.create(1340376396178, "Method " + methodName + " doesn't exist", impTable);
		}
		return methodImp;
	},

	/**
	 * Returns the UID of the object.
	 *
	 * @returns {Integer}
	 */
	getUid: function () {
		if (!this.uid) {
			this.uid = this.getUidFromString(this.getNode().attr('id'));
		}
		return this.uid;
	},

	/**
	 * Returns the DOM node of the object.
	 *
	 * @returns {jQuery}
	 */
	getElement: function () {
		return this.element;
	},
	/**
	 * @see getElement
	 */
	getNode: function () {
		return this.element;
	},

	/**
	 * Returns the object with the given name and the UID.
	 *
	 * @param	{String} name
	 *
	 * @returns {jQuery}
	 */
	getChildByName: function (name) {
		return this.getChildByNameAndUid(name, this.getUid());
	},

	/**
	 * Searches for a DOM element with an ID that matches '{name}_{uid}'.
	 *
	 * @param	{String} name
	 * @param	{Integer} uid
	 *
	 * @returns {jQuery} Returns the found object
	 */
	getChildByNameAndUid: function (name, uid) {
		return this.getElement().find('#' + name + '_uid_' + uid);
	},

	/**
	 * Returns the object with the given name and the UID.
	 *
	 * @param	{String} name
	 *
	 * @returns {jQuery}
	 */
	getElementByName: function (name) {
		return this.getElementByNameAndUid(name, this.getUid());
	},

	/**
	 * Searches for a DOM element with an ID that matches '{name}_{uid}'.
	 *
	 * @param	{String} name
	 * @param	{Integer} uid
	 *
	 * @returns {jQuery} Returns the found object
	 */
	getElementByNameAndUid: function (name, uid) {
		return jQuery('#' + name + '_uid_' + uid).first();
	},

	/**
	 * Reads the UID from a given string.
	 *
	 * @param	  {string} The string to read the UID from
	 *
	 * @return	{integer} Returns the extraced UID
	 */
	getUidFromString: function (inputString) {
		if (!inputString) return false;
		var matches = inputString.match(/_uid_([0-9]*)$/);
		return matches[1];
	},

	/**
	 * Creates a function callback to pass to a jQuery bind method.
	 *
	 * Once the callback is invoked, it will receive the following arguments:
	 *  originalArgument1
	 *  originalArgument2
	 *  ...
	 *  originalArgumentN
	 *  context (The value of this seen from inside the anonymous callback function)
	 *  userInfo (The userInfo passed to ccb())
	 *
	 *
	 * @param	{Function|String}	callBack	Either the function to invoke, or the name of a method
	 * @param	{mixed}				userInfo	An optional user info object, that will be sent to the callback as the penultimate
	 * @returns {Function}  Returns a function to pass to a jQuery bind method
	 */
	ccb: function (callBack, userInfo) {
		var _this = this;
		if (typeof callBack === 'string' && this[callBack]) {
			callBack = _this[callBack];
		}
		if (typeof callBack !== 'function') {
			return this.ef;
		}
		return function () {
			var argumentsAndThis = arguments,
				context = this;

			// Add the current context to the original arguments
			argumentsAndThis[argumentsAndThis.length] = context;
			argumentsAndThis.length++; // Increase the lenght

			// Add the userInfo if it is defined
			if (typeof userInfo !== 'undefined') {
				argumentsAndThis[argumentsAndThis.length] = userInfo;
				argumentsAndThis.length++; // Increase the lenght
			}

			return callBack.apply(_this, argumentsAndThis);
		};
	},
	/**
	 * @see ccb()
	 */
	createCallBack: function (callBack, userInfo) {
		return this.ccb(callBack, userInfo);
	},

	/**
	 * Merge the contents of two or more objects together into the first object.
	 *
	 * @returns {Object}  Returns the merged object
	 */
	extend: function () {
		var options, name, src, copy, copyIsArray, clone,
			target = arguments[0] || {},
			i = 1,
			length = arguments.length,
			deep = true,
			_this = this,
			localJQuery = window.jQuery,
			chars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
				'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
				'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
				'Y', 'Z'];


		// Handle a deep copy situation
		if (typeof target === "boolean") {
			deep = target;
			target = arguments[1] || {};
			// skip the boolean and the target
			i = 2;
		}

		// Handle case when target is a string or something (possible in deep copy)
		if (typeof target !== "object" && !localJQuery.isFunction(target)) {
			target = {};
		}

		// extend jQuery itself if only one argument is passed
		if (length === i) {
			target = localJQuery;
			--i;
		}

		for (; i < length; i++) {
			// Only deal with non-null/undefined values
			if ((options = arguments[i]) !== null) {
				// Extend the base object
				for (name in options) {
					/*
					 * Skip properties that start with an
					 * upper case and are no constants
					 */
					if (chars.indexOf(name.charAt(0)) === -1 || name === name.toUpperCase()) {
						src = target[name];
						copy = options[name];

						// Skip the __uuid
						if (name === '__uuid') {
							continue;
						}

						// Prevent never-ending loop
						if (target === copy || (copy && target === copy.__super__)) {
							continue;
						}

						// Recurse if we're merging plain objects or arrays
						if (deep && copy && (localJQuery.isPlainObject(copy) || (copyIsArray = localJQuery.isArray(copy)))) {
							if (copyIsArray) {
								copyIsArray = false;
								clone = src && localJQuery.isArray(src) ? src : [];

							} else {
								clone = src && localJQuery.isPlainObject(src) ? src : {};
							}

							// Never move original objects, clone them
							target[name] = _this.extend(deep, clone, copy);

						// Don't bring in undefined values
						} else if (copy !== undefined) {
							target[name] = copy;
						}
					}
				}
			}
		}

		// Return the modified object
		return target;
	},

	/**
	 * Checks if the given object is equal to this.
	 *
	 * @param	{Object} anotherObject The object to compare against
	 * @returns {Object}  Returns TRUE if the objects are equal, otherwise FALSE
	 */
	isEqualTo: function (theObject, anotherObject) {
		if (!anotherObject) {
			anotherObject = this;
		}
		if (theObject.__uuid === anotherObject.__uuid) {
			return true;
		}
		if (Object.keys) {
			if (Object.keys(theObject).length !== Object.keys(anotherObject).length) {
				return false;
			}
		}
		for (var property in theObject) {
			if (theObject[property] !== anotherObject[property]) {
				return false;
			}
		}
		return true;
	},

	/**
	 * Displays the given values using the console.log() function.
	 *
	 * @returns {void}
	 */
	debug: function () {
		var allArguments = arguments,
			logger;
		if (Iresults.Config && Iresults.Config.debug) {
			logger = console.log;
			logger.apply(console, allArguments);
		}
	},
	/**
	 * @see debug()
	 */
	pd: function () {
		var allArguments = arguments,
			logger;
		if (Iresults.Config && Iresults.Config.debug) {
			logger = console.log;
			logger.apply(console, allArguments);
		}
	},

	/**
	 * Return true if installed is greater than or equal to required.
	 *
	 * @param	{String} installed Version 1
	 * @param	{String} required Version 2
	 * @returns {Boolean}
	 */
    compareVersions: function (installed, required) {
		var a, b, i;

        a = installed.split('.');
        b = required.split('.');

        for (i = 0; i < a.length; ++i) {
            a[i] = Number(a[i]);
        }
        for (i = 0; i < b.length; ++i) {
            b[i] = Number(b[i]);
        }
        if (a.length === 2) {
            a[2] = 0;
        }

        if (a[0] > b[0]) return true;
        if (a[0] < b[0]) return false;
        if (a[1] > b[1]) return true;
        if (a[1] < b[1]) return false;
        if (a[2] > b[2]) return true;
        if (a[2] < b[2]) return false;

        return true;
    }
};
var Iresults_Core = Iresults.Core;



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* CLASS                  WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * The iresults Class object.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 1.0.0
 */
Iresults.Class = {
	/**
	 * Creates a new class with the given identifier (namespace.className) and
	 * definition.
	 *
	 * @param	{String}	classIdentifier	The class identifier in the format 'MyComany.Foo.Class'
	 * @param	{Object}	classDefinition	The class definition
	 * @returns	{Object}					The new class
	 */
	create: function (classIdentifier, classDefinition) {
		var parts, className, namespaceObject;

		// Initialize the class
		if (typeof classDefinition.initialize !== 'undefined') {
			// Invoke the initialize() method with the class definition as context
			classDefinition.initialize.call(classDefinition);
		}

		parts = classIdentifier.split('.');
		className = parts.pop();

		namespaceObject = namespace(parts);
		namespaceObject[className] = classDefinition;
		window['Tx_' + classIdentifier.replace(/\./g, '_')] = classDefinition;

		return classDefinition;
	},

	/**
	 * The object oriented interface to the namespace() function.
	 *
	 * @param	{String}	namespaceIdentifier	The namespace identifier in the format "MyComany.Package.SubPackage"
	 * @returns	{void}
	 */
	namespace: function (namespaceIdentifier) {
		return namespace(namespaceIdentifier);
	},

	/**
	 * Creates a new class by extending another class.
	 *
	 * @param	{String}	classIdentifier	The class identifier in the format 'MyComany.Foo.Class'
	 * @param	{Object}	newClass		The new class that should extend the superClass
	 * @param	{Object}	superClass		The class to extend
	 * @returns	{Object}					The new class
	 */
	extend: function (classIdentifier, newClass, superClass) {
		classIdentifier = arguments[0] || '';
		var useEmber = false,
			mergedClass = {};


		// If there are only 2 arguments, just create the class
		if (arguments.length === 2) {
			return this.create(classIdentifier, newClass);
		}


		// Check if ember is available and superClass is a subclass of Ember
		if (window.Ember && typeof superClass[window.Ember.GUID_KEY] !== 'undefined') {
			useEmber = true;
		}

		if (useEmber) {
			mergedClass = superClass.extend(newClass);
			mergedClass.__classIdentifier = classIdentifier;
			return this.create(classIdentifier, mergedClass);
		}

		// Set the _super object
		mergedClass = Iresults.Core.extend({}, superClass, newClass);
		mergedClass.__classIdentifier = classIdentifier;
		if (typeof newClass.__super__ === 'undefined') {
			mergedClass.__super__ = superClass;
		}
		return this.create(classIdentifier, mergedClass);
	},

	/**
	 * Checks if the given class identifier exists.
	 *
	 * @param	{String}	classIdentifier The class identifier in the format 'MyComany.Foo.Class'
	 * @returns	{Boolean}
	 */
	classExists: function (classIdentifier) {
		var classIdentifierParts = classIdentifier.replace(/\.|_/g, '/').split('/'),
			lastObject = window,
			currentClassIdentifierPart,
			i = 0;

		for (i; i < classIdentifierParts.length; i++) {
			currentClassIdentifierPart = classIdentifierParts[i];
			if (typeof lastObject[currentClassIdentifierPart] === 'undefined') {
				return false;
			}
			lastObject = lastObject[currentClassIdentifierPart];
		}
		return true;
	},

	/**
	 * Loads the given class.
	 *
	 * The method requires yepnope to be loaded.
	 *
	 * @param	{String}	className			The name of the class to load
	 * @param	{Function}	completeFunction	The function to call when loading completed
	 * @returns	{String}						Returns the path to the file
	 */
	load: function (className, completeFunction) {
		if (this.classExists(className)) {
			completeFunction();
			return true;
		}
		var i, parsedClassName = className.replace(/\.|_/g, '/'),
			classFilePath = '',
			classNameParts = parsedClassName.split('/'),
			extensionName = classNameParts[0] + '',
			partsCount = classNameParts.length;


		if (extensionName.toUnderscore) {
			extensionName = extensionName.toUnderscore();
		} else {
			extensionName = extensionName.toLowerCase();
		}

		classFilePath = 'typo3conf/ext/' + extensionName + '/Resources/Public/Scripts';
		for (i = 1; i < partsCount; i++) {
			classFilePath += '/' + classNameParts[i];
		}
		classFilePath += '.js';

		if (window.require) { // Use requirejs (http://requirejs.org/)
			window.require([classFilePath], completeFunction);
			return classFilePath;
		} else if (window.yepnope) { // Use yepnope (http://yepnopejs.com/)
			window.yepnope({
				load: classFilePath,
				complete: completeFunction
			});
			return classFilePath;
		} else {
			throw Iresults.Exception.create(1339591723726, "Couldn't load the class " + className);
		}
		return classFilePath;
	}
};



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* CORE OBJECT            WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
* The iresults Core object.
*
* @author	Daniel Corn <cod@iresults.li>
* @package	Iresults
* @subpackage	Iresults_Core
* @version 1.0.0
*/
Iresults.Class.extend('Iresults.CoreObject', {
	///**
	// * The Ember-getter returns the value for the given key.
	// *
	// * @param	{String}	keyName	The property key name to get
	// * @returns	{Mixed}				The value for the key
	// */
	//get: function (keyName) {
	//	var ret = this[keyName];
	//	if (ret === undefined && 'function' === typeof this.unknownProperty) {
	//		ret = this.unknownProperty(keyName);
	//	}
	//	return ret;
	//},
	//
	///**
	// * The Ember-setter sets the given value for the given key.
	// *
	// * @param	{String}	keyName	The property key name to set
	// * @param	{Object}	value	The new value to set
	// * @returns	{Object}			Returns the given value
	// */
	//set: function set(keyName, value) {
	//	if (!(keyName in this)) {
	//		if ('function' === typeof this.setUnknownProperty) {
	//			this.setUnknownProperty(keyName, value);
	//		} else if ('function' === typeof this.unknownProperty) {
	//			this.unknownProperty(keyName, value);
	//		} else {
	//			this[keyName] = value;
	//		}
	//	} else {
	//		this[keyName] = value;
	//	}
	//	return value;
	//}
}, Iresults.Core);
Iresults.Object = Iresults.CoreObject;



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* IRESULTS WITH EMBER    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * The iresults Ember Mixin.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 1.0.0
 */
Iresults.Ember = {};
if (window.Ember) {
	Iresults.Ember = Ember.Mixin.create(Iresults.Object);
	Iresults.Object.get = function (keyName) { return Ember.get(this, keyName); };
	Iresults.Object.set = function (keyName, value) { return Ember.set(this, keyName, value); };
}



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* SINGLETON              WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * The iresults Singleton object.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Singleton', {
	/**
	 * Returns a shared instance of Iresults.Persistence.Backend;
	 *
	 * @returns	{Iresults.Persistence.Backend}
	 */
	getSharedInstance: function () {
		if (!Iresults.Class.namespace(this.__classIdentifier).SharedInstance) {
			Iresults.Class.namespace(this.__classIdentifier).SharedInstance = Iresults.Class.namespace(this.__classIdentifier).create();
		}
		return Iresults.Class.namespace(this.__classIdentifier).SharedInstance;
	}
}, Iresults.Object);



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* CORE EXCEPTION         WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * The iresults Core exception class.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Core
 * @version 2.0.0
 */
Iresults.Exception = {
	/**
	 * The exceptions unique code.
	 *
	 * @type {Integer}
	 */
	code: 0,

	/**
	 * The human-readable description of the exception.
	 *
	 * @type {String}
	 */
	message: 'Unknown reason',

	/**
	 * Additional user data to describe the context the exception was raised in.
	 *
	 * @type {mixed}
	 */
	userInfo: null,

	/**
	 * Creates a new instance.
	 *
	 * @returns {Object}
	 */
	create: function () {
		var instance = Iresults.Core.extend({}, this);
		if (arguments.length >= 3) {
			instance.userInfo = arguments[2];
		}
		if (arguments.length >= 2) {
			instance.message = arguments[1];
		}
		if (arguments.length >= 1) {
			instance.code = arguments[0];
		}
		return instance;
	},

	/**
	 * Returns the exceptions unique code.
	 * @return	{Integer}
	 */
	getCode: function () {
		return this.code;
	},

	/**
	 * Returns message
	 * @return	{String}
	 */
	getMessage: function () {
		return this.message;
	},

	/**
	 * Returns userInfo
	 * @return	{Mixed}
	 */
	getUserInfo: function () {
		return this.userInfo;
	},

	/**
	 * Sets the value of userInfo
	 *
	 * @param	{Mixed} newValue The new value to set
	 * @return	void
	 */
	setUserInfo: function (newValue) {
		this.userInfo = newValue;
	},

	/**
	 * Returns the string representation of the exception.
	 *
	 * @returns {String}
	 */
	toString: function () {
		return '#' + this.code + ' ' + this.message;
	}
};
/**************************************************************
*  Copyright notice
*
 * (c) 2010 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  			Daniel Corn <cod@iresults.li>, iresultsaniel Corn <cod@iresults.li>
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

var Iresults = window.Iresults || {};
Iresults.Ui = Iresults.Ui || {};
Iresults.Ui.Helpers = Iresults.Ui.Helpers || {};

var nativeConsole = window.console || {};

/**
 * An experimental tool to display debug messages
 * using the console.log() function.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ui_Helpers
 * @version 0.0.1
 */
Iresults.Ui.Helpers.Console = {
	/**
	 * The DIV that contains the debug output
	 * 
	 * @type {HTMLElement}
	 */
	container: null,
	
	/**
	 * End of line delimiter
	 * 
	 * @type {String}
	 */
	eol: '\r\n',
	
	/**
	 * The current depth in object traversal
	 * 
	 * @type {Integer}
	 */
	currentDepth: 0,
	
	/**
	 * @var integer The maximum depth level in the object tree.
	 */
	maxLevel: 10,
	
	/**
	 * The output shown whenthe maximum (depth) level is reached
	 * 
	 * @type {String}
	 */
	MAX_LEVEL_REACHED_MESSAGE: '(ML)',
	
	/**
	 * The main function of the class. Used to
	 * display debug messages at the bottom of the
	 * document.
	 *
	 * @param	{mixed}	Takes an unknown number of arguments
	 * @returns	{void}
	 */
	log: function (arg0) {
		var output = this.logVariables.apply(this, arguments);
		this.display(output);
		return output;
	},
	
	/**
	 * The main function of the class. Used to
	 * display debug messages at the bottom of the
	 * document.
	 *
	 * @param	{mixed}	Takes an unknown number of arguments
	 * @returns	{void}
	 */
	debug: function (arg0) {
		var output = this.logVariables.apply(this, arguments);
		this.display(output, '#fff98a');
		return output;
	},
	
	/**
	 * The main function of the class. Used to
	 * display debug messages at the bottom of the
	 * document.
	 *
	 * @param	{mixed}	Takes an unknown number of arguments
	 * @returns	{void}
	 */
	error: function (arg0) {
		var output = this.logVariables.apply(this, arguments);
		this.display(output, '#f00');
		return output;
	},
	
	/**
	 * The main function of the class. Used to
	 * display debug messages at the bottom of the
	 * document.
	 *
	 * @param	{mixed}	Takes an unknown number of arguments
	 * @returns	{void}
	 */
	info: function (arg0) {
		var output = this.logVariables.apply(this, arguments);
		this.display(output);
		return output;
	},
	
	/**
	 * The main function of the class. Used to
	 * display debug messages at the bottom of the
	 * document.
	 *
	 * @param	{mixed}	Takes an unknown number of arguments
	 * @returns	{void}
	 */
	warn: function (arg0) {
		var output = this.logVariables.apply(this, arguments);
		this.display(output, '#ffb694');
		return output;
	},
	
	/**
	 * The main function of the class. Used to
	 * display debug messages at the bottom of the
	 * document.
	 *
	 * @param	{mixed}	Takes an unknown number of arguments
	 * @returns	{void}
	 */
	logVariables: function (arg0) {
		var i = 0,
			output = '',
			length = arguments.length;
		
		while (i < length) {
			output += this.logVariable(arguments[i]);
			i++;
		}
		return output + this.eol;
	},
	
	/**
	 * Debugs a single variable.
	 * 
	 * @param	{mixed}	variable	The variable to debug
	 * @returns	{string}		Returns a string representation of the variable
	 */
	logVariable: function (variable) {
		var output = '',
			leftPadding = '',
			property = '',
			indent = '... ',
			type = '',
			eol = this.eol,
			currentObject,
			functionLines,
			maxLevel = this.maxLevel,
			maxLevelReachedMessage = this.MAX_LEVEL_REACHED_MESSAGE;
		
		leftPadding += new Array(this.currentDepth + 1).join(indent);
		
		if (this.currentDepth >= maxLevel) {
			output += leftPadding + maxLevelReachedMessage + eol;
		}
		
		
		type = typeof variable;
		switch (type) {
		case 'undefined':
			output += leftPadding + '(undefined)' + eol;
			break;
		
		case 'boolean':
		case 'number':
		case 'string':
			output += leftPadding + '(' + type + ')' + variable + eol;
			break;
		
		case 'function':
			functionLines = (variable + '').match(/[^\r\n]+/g);
			variable = functionLines[0] + eol + functionLines[1] + eol;
			variable += new Array(this.currentDepth + 2).join('    ') + '(FUNCTION_BODY)' + eol;
			variable += functionLines[functionLines.length - 2] + eol + functionLines[functionLines.length - 1];
			output += leftPadding + '(' + type + ')' + variable + eol;
			break;
		
		case 'object':
			output += leftPadding + '(object) {' + eol;
			for (property in variable) {
				currentObject = variable[property];
				if (currentObject !== window &&
					currentObject !== this &&
					currentObject !== variable &&
					property !== '__super__' &&
					property !== '_super'
					) {
					this.currentDepth++;
					try {
						output += leftPadding + indent + property + ':' + eol;
						output += indent + this.logVariable(currentObject);
						//output += leftPadding + ';' + eol;
						//output += eol;
					} catch (exception) {
						output += leftPadding + property + ': Couldn\'t debug ' + exception + ';' + eol;
					}
					this.currentDepth--;
				}
			}
			output += leftPadding + '}' + eol;
			break;
		
		default:
			if (variable === null) {
				output += leftPadding + '(undefined)' + eol;
			}
		}
		return output;
	},
	
	/**
	 * Display the new content
	 *
	 * @param	{String} content  The debug content to display
	 * @param	{String} color      A color code
	 * @returns	{void}
	 */
	display: function (content, color) {
		var containerLocal = this.container;
		if (!containerLocal) {
			containerLocal = $('<div id="iresults_ui_helpers_console" class="iresults_ui_helpers_console" />');
			$('body').append(containerLocal);
			containerLocal = $('#iresults_ui_helpers_console').first();
			this.container = containerLocal;
			
			this.displayInput();
		}
		
		if (typeof color === 'undefined') {
			color = 'inherit';
		}
		containerLocal.append('<pre style="background:' + color + ';">' + content + '</pre>');
	},
	
	/**
	 * Display the input
	 * @returns	{Boolean}
	 */
	displayInput: function () {
		var _this = this;
		$('body').append('<div id="iresults_ui_helpers_console_input_container" style="position:fixed;bottom:0;left:0;width:850px;height:250px;z-index:2000;"><form action="#"><textarea id="iresults_ui_helpers_console_input" rows="15" cols="100" style="height:200px;"></textarea><input type="submit" value="eval" /></form></div>');
		$('#iresults_ui_helpers_console_input').parent().submit(function () {
			try {
				_this.log(eval($('#iresults_ui_helpers_console_input').val()));
			} catch (e) {
				
			}
			return false;
		});
	}
};


/*
 * Create the console instance
 */
var Console = Iresults.Ui.Helpers.Console;
var console = Console;
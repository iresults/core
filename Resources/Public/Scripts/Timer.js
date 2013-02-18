/***************************************************************
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


/**
 * The iresults timer class enables you to schedule repeating functions.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Timer
 */
function Iresults_Timer(intervalPara){
	this.interval = 500;
	this.started = false;
	this.scheduledFunctions = new Array();
	this.maxRepeat = 3;

	this._timer = null;
	this._repeatCount = 0;

	if(intervalPara){
		this.interval = intervalPara;
	}
}

/**
 * Schedule a new function.
 *
 * @param	  {function} The function to schedule
 * @param	  {boolean} silent            If set to TRUE the timer will not be
 * started if it is not runnning.
 *
 * @returns {integer} The index of the new function
 */
Iresults_Timer.prototype.add = function(newFunction, silent){
	if(!silent) silent = false;

	// Make sure the given argument is realy a function.
	if(typeof newFunction != "function"){
		newFunction = new Function(newFunction);
	}
	this.scheduledFunctions[this.scheduledFunctions.length] = newFunction;

	/**
	 * Check if the timer is running else, start it.
	 */
	if(!silent && !this._timer){
		this.start();
	}

	return this.scheduledFunctions.length - 1;
}

/**
 * Removes the scheduled function at the given index from the stack.
 *
 * @param	  {index} The index of to function to remove
 *
 * @returns {void}
 */
Iresults_Timer.prototype.remove = function(index){
	if(index){
		this.scheduledFunctions[index] = null;
	}
}

/**
 * Starts the timer.
 *
 * @param	{integer} [Optional] The new interval of the timer
 *
 * @returns {void}
 */
Iresults_Timer.prototype.start = function(intervalPara){
	// Set a new interval
	if(intervalPara) this.interval = intervalPara;

	// Return if no functions are scheduled
	if(this.scheduledFunctions.length == 0) return false;

	// Stop the last timer, if one exists.
	if(this._timer){
		window.clearInterval(this._timer);
	}

	var caller = this;
	this._timer = window.setInterval(function(){
			caller.trigger();
		},
		this.interval
	);
}

/**
 * The function that will be called by the interval.
 *
 * @returns {void}
 */
Iresults_Timer.prototype.trigger = function(){
	//console.log("triggered",this.scheduledFunctions.length)
	for(var i = 0; i < this.scheduledFunctions.length; i++){
		this.scheduledFunctions[i]();
	}

	this._repeatCount++;
	if(this._repeatCount >= this.maxRepeat){
		this.stop();
	}
}

/**
 * Returns if the timer is running.
 *
 * @returns {boolean} TRUE if the timer is running, otherwise FALSE
 */
Iresults_Timer.prototype.isValid = function(){
	if(this._timer){
		return true;
	}
	return false;
}

/**
 * Stops the timer.
 *
 * @returns {void}
 */
Iresults_Timer.prototype.stop = function(){
	if(this._timer){
		window.clearInterval(this._timer);
	}
	this._repeatCount = 0;
	this._timer = null;
}

/**
 * Stops the timer.
 *
 * @returns {void}
 */
Iresults_Timer.prototype.invalidate = function(){
	this.stop();
}

var Iresults = Iresults || {}
Iresults.Timer = Iresults_Timer;
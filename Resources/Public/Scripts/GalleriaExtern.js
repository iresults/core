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
 * The iresults Galleria Extern class moves the thumbnail container DIV of the
 * Galleria gallery (http://galleria.aino.se/) inside another DIV.
 *
 */
//

/**
 * Move the contents after the target.
 */
Iresults_GalleriaExtern.MODE_MOVE_AFTER_TARGET = 'after';

/**
 * Move the contents into the target.
 */
Iresults_GalleriaExtern.MODE_MOVE_APPEND_TO_TARGET = 'append';


/**
 * The iresults Galleria Extern class moves the thumbnail container DIV of the
 * Galleria gallery (http://galleria.aino.se/) inside another DIV.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ajax
 */
function Iresults_GalleriaExtern(galleria, targetId, mode){
	this.galleria = galleria;
	this.carouselSel = ".galleria-thumbnails-container";

	/**
	 * Set the defaults.
	 */
	this.mode = Iresults_GalleriaExtern.MODE_MOVE_AFTER_TARGET;
	this.targetId = 'Iresults_galleria_target';

	/**
	 * Apply the given configuration.
	 */
	if(mode){
		this.mode = mode;
	}
	if(targetId){
		this.targetId = targetId;
	}

	/**
	 * Moves the content to the new position.
	 *
	 * The new position is connected to the target, but the actual way the content
	 * is moved is described through the mode parameter.
	 *
	 * @param	  {jQuery object} content		The content to move
	 * @param	  {jQuery object} target		The target of the moving
	 * @param	  {string} mode				The way how the content is placed
	 * relative to the target as one of the MODE_MOVE constants.
	 *
	 * @returns {void}
	 */
	this._move = function(content, target, mode){
		if(mode == Iresults_GalleriaExtern.MODE_MOVE_AFTER_TARGET){
			target.after(content);
		} else if(mode == Iresults_GalleriaExtern.MODE_MOVE_APPEND_TO_TARGET){
			content.appendTo(target);
		}
	}

	if(this.targetId && $(this.carouselSel) && $("#" + this.targetId).length){
		this._move($(this.carouselSel), $("#" + this.targetId), this.mode);
	} else if($(this.carouselSel).length){
		//console.warn("The carousel (SEL: '"+this.carouselSel+"') was found, but the target with the ID '"+this.targetId+"' couldn't be found.");
	}

}


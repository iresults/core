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
 * 
 * 
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Forms
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Forms.LiveValidation', {
    _init: function () {
        var _this = this,
            defaults;
        
        // The default options
        defaults = {
            pattern: /[^a-zA-Z0-9]/g,
            didChangeValue: function () {},
            didNotChangeValue: function () {}
        };
        /*
         * Overwrite the defaults with the current options and save them as the
         * options
         */
        $.extend(defaults, this.options);
        this.options = defaults;
        
        // If an "allow" options was given, create a regular expression from it
        if (this.options.allowed) {
            this.options.pattern = new RegExp('[^' + this.options.allowed + ']', g);
        }
        
        // Bind the keyup event with the keyup method
        return this.element.each(function () {
            $(this).bind('keyup', function (event) {_this.keyup(event); });
        });
    },
    
    /**
     * Removes unallowed characters from the text field's value.
     *
     * @param	{jQuery.Event} event
     *
     * @returns {void}
     */
    keyup: function (event) {
        var textfieldValue = event.currentTarget.value,
            newTextfieldValue;
        
        newTextfieldValue = textfieldValue.replace(this.options.pattern, '');
        
        // If the value changed set the new value
        if (newTextfieldValue !== textfieldValue) {
            event.currentTarget.value = newTextfieldValue;
            this.options.didChangeValue(event.currentTarget);
        }
        this.options.didNotChangeValue(event.currentTarget);
    }
}, Iresults.Object);

$.widget("ui.irFormsLivevalidation", Iresults.Forms.LiveValidation);
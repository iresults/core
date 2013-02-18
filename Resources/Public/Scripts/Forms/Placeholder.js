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
Iresults.Class.extend('Iresults.Forms.Placeholder', {
    _init: function () {
        var _this = this,
            defaults;
        
        // The default options
        defaults = {
            placeholder: '',
            placeholderCssClass: 'shows_placeholder',
            showsPlaceholder: true
        };
        /*
         * Overwrite the defaults with the current options and save them as the
         * options
         */
        $.extend(defaults, this.options);
        this.options = defaults;
        
        /*
         * Check if modernizr is installed and if true, check if HTML5
         * placeholders are available
         */
        if (window.Modernizr && window.Modernizr.input.placeholder) {
            return this;
        }
        
        // Bind the blur event
        return this.element.each(function () {
            var placeholderAttribute = $(this).attr('placeholder');
            if (placeholderAttribute) {
                _this.options.placeholder = placeholderAttribute;
            }
            if (!_this.options.placeholder) {
                return;
            }
            
            $(this).bind('blur', function (event) {_this.checkIfPlaceholderShouldBeShown(event); });
            $(this).bind('focus', function (event) {_this.checkIfPlaceholderShouldBeRemoved(event); });
            
            // Trigger it the first time
            _this.checkIfPlaceholderShouldBeShown({currentTarget: this});
        });
    },
    
    /**
     * Sets the placeholder as the targets value if the original value is empty
     *
     * @param	{jQuery.Event} event
     *
     * @returns {void}
     */
    checkIfPlaceholderShouldBeShown: function (event) {
        var target = $(event.currentTarget);
        if (!target.val()) {
            target.val(this.options.placeholder);
            this.options.showsPlaceholder = true;
            
            // Add the CSS class
            if (this.options.placeholderCssClass) {
                target.addClass(this.options.placeholderCssClass);
            }
        } else {
            this.options.showsPlaceholder = false;
        }
    },
    
    /**
     * Removes the placeholder
     *
     * @param	{jQuery.Event} event
     *
     * @returns {void}
     */
    checkIfPlaceholderShouldBeRemoved: function (event) {
        var target = $(event.currentTarget);
        if (this.options.showsPlaceholder) {
            target.val('');
            this.options.showsPlaceholder = false;
            
            // Remove the CSS class
            if (this.options.placeholderCssClass) {
                target.removeClass(this.options.placeholderCssClass);
            }
        }
    }
}, Iresults.Object);

$.widget("ui.irFormsPlaceholder", Iresults.Forms.Placeholder);
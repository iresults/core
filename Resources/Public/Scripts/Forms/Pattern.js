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
 * Validate the bound jQuery object's value against a regular expression.
 *
 * The regular expression is taken from either the defaults, or if given, the
 * jQuery object's pattern-attribute.
 *
 * @include Iresults.Core.Core
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Forms
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Forms.Pattern', {
    _init: function () {
        var _this = this,
            defaults;

        // The default options
        defaults = {
            pattern: /.+/g,
            cssClassValidationFailed: 'validation_failed',
            focusOnFailedSubmit: true,
            validationFailed: function () {},
            validationPassed: function () {}
        };
        /*
         * Overwrite the defaults with the current options and save them as the
         * options
         */
        $.extend(defaults, this.options);
        this.options = defaults;

        // Bind the change event with the validate method
        return this.element.each(function () {
            var element = $(this),
                closestForm = element.closest('form');

            element.bind('blur', _this.ccb(_this.validate, element));
            closestForm.bind('submit', _this.ccb(_this.validate, element));
        });
    },

    /**
     * Removes unallowed characters from the text field's value.
     *
     * @param	{jQuery.Event} event
     *
     * @returns {void}
     */
    validate: function (event, context, userInfo) {
        var target = userInfo,
            _this = this,
            pattern;

        // Get the pattern
        pattern = target.attr('pattern');
        pattern = new RegExp(pattern);
        if (!pattern) {
            pattern = this.options.pattern;
        }

        // Test if the pattern matches and make sure it is not a placeholder text
        if (pattern.test(target.val()) && this.checkIfTargetShowsPlaceholder(target) === false) {
            target.removeClass(this.options.cssClassValidationFailed);
            setTimeout(function () {_this.options.validationPassed(event); }, 10);
            return true;
        } else {
            if (this.options.focusOnFailedSubmit && $(event.target).prop('nodeName') === 'FORM') {
                target.focus();
            }
            target.addClass(this.options.cssClassValidationFailed);
            setTimeout(function () {_this.options.validationFailed(event); }, 10);
            return false;
        }
    },

    /**
     * Checks if the target has a placeholder and if it is currently applied.
     *
     * This check has to be performed, because the placeholder is not value must
     * must not pass the validation.
     *
     * @param	{jQuery}	target	The target to test
     * @returns	{Boolean}			Returns TRUE if a placeholder is applied, otherwise FALSE
     */
    checkIfTargetShowsPlaceholder: function (target) {
        if (target.data('irFormsPlaceholder') && target.data('irFormsPlaceholder').options.showsPlaceholder) {
            return true;
        }
        return false;
    }
}, Iresults.Object);

$.widget("ui.irFormsPattern", Iresults.Forms.Pattern);
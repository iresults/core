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
Iresults.Forms = Iresults.Forms || {};
Iresults.Config = Iresults.Config || {};

/**
 * Set this to TRUE if you want to disable the browsers native HTML5 features.
 * @type {Boolean}
 */
Iresults.Forms.forceFallback = Iresults.Forms.forceFallback || Iresults.Config.uiFormsHTML5ForceFallback || false;

/**
 * The Iresults Forms HTML5 class automatically activates the fallbacks for
 * HTML5 form features.
 *
 * @include Iresults.Core.Core
 * @include Iresults.Core.Forms.Pattern
 * @include Iresults.Core.Forms.Placeholder
 * @include Iresults.Core.Forms.DatePicker
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Forms
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Forms.HTML5', {
    /**
     * @type {HTMLElement}	The dummy input element.
     */
    dummy: null,

    /**
     * @type {Boolean}  Indicates if the browser supports date inputs.
     */
    dateTime: -1,

    /**
     * @type {Boolean}  Indicates if the browser supports autofocus.
     */
    autofocus: -1,

    /**
     * @type {Boolean}  Indicates if the browser supports placeholder.
     */
    placeholder: -1,

    /**
     * @type {Boolean}  Indicates if the browser supports pattern.
     */
    pattern: -1,

    /**
     * @type {Boolean}  Indicates if the browser supports required.
     */
    required: -1,

    /**
     * Initialize the fallbacks.
     *
     * @returns	{void}
     */
    initialize: function () {
        var _this = this;

        $(function () {
            var patternUrl = /(https?|ftp):\/\/(-\.)?([^\s\/?\.#\-]+\.?)+(\/[^\s]*)?$/i,
                patternEmail = /^[a-zA-Z0-9\.!#$%&’*+\/=?\^_`{|}~\-]+@[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*$/,
                patternNumber = /^[0-9\.,']+$/,
                forceFallback = Iresults.Forms.forceFallback,
                autofocusElement,
                changeInputTypeToText;

            // Create the dummy input
            _this.dummy = document.createElement("input");

            // Date
            if (navigator.userAgent.indexOf('Chrome/2') !== -1) {
                /*
                 * Disable Chrome's date input
                 */
                changeInputTypeToText = function (index, element) {
                    element.type = 'text';
                };
                $('input[type=date]').each(changeInputTypeToText).irFormsDatePicker();
                $('input[type=week]').each(changeInputTypeToText).irFormsDatePicker();
                $('input[type=month]').each(changeInputTypeToText).irFormsDatePicker();
                this.dateTime = false;
            } else if (forceFallback || !_this.checkDateTime()) {
                $('input[type=date]').irFormsDatePicker();
                $('input[type=week]').irFormsDatePicker();
                $('input[type=month]').irFormsDatePicker();
            }

            // Placeholder
            if (forceFallback || !_this.checkPlaceholder()) {
                $('input[placeholder],textarea[placeholder]').irFormsPlaceholder();
            }

            // Autofocus
            if (forceFallback || !_this.checkAutoFocus()) {
                autofocusElement = $('input[autofocus]').first();
                if (autofocusElement.is(':visible')) {
                    autofocusElement.focus();
                }
            }

            // Validation
            if (forceFallback || !_this.checkPattern()) {
                $('input[pattern],textarea[pattern]').irFormsPattern();
            }
            if (forceFallback || !_this.checkRequired()) {
                $('input[required],textarea[required]').irFormsPattern();
            }
            $('input[type=url]').irFormsPattern({pattern: patternUrl});
            $('input[type=email]').irFormsPattern({pattern: patternEmail});
            $('input[type=number]').irFormsPattern({pattern: patternNumber}).irFormsPlaceholder({placeholder: '0'});

        });
    },

    /**
     * Returns if the browser supports pattern.
     * @returns	{Boolean}
     */
    checkPattern: function () {
        if (this.pattern === -1) {
            this.pattern = ("pattern" in this.dummy);
        }
        return this.pattern;
    },

    /**
     * Returns if the browser supports placeholder.
     * @returns	{Boolean}
     */
    checkRequired: function () {
        if (this.required === -1) {
            this.required = ("required" in this.dummy);
        }
        return this.required;
    },

    /**
     * Returns if the browser supports placeholder.
     * @returns	{Boolean}
     */
    checkPlaceholder: function () {
        if (this.placeholder === -1) {
            this.placeholder = ("placeholder" in this.dummy);
        }
        return this.placeholder;
    },

    /**
     * Returns if the browser supports autofocus.
     * @returns	{Boolean}
     */
    checkAutoFocus: function () {
        if (this.autofocus === -1) {
            this.autofocus = ("autofocus" in this.dummy);
        }
        return this.autofocus;
    },

    /**
     * Returns if the browser supports date inputs.
     *
     * @returns	{Boolean}
     */
    checkDateTime: function () {
        var input;
        if (this.dateTime === -1) {
            input = document.createElement("input");
            input.setAttribute("type", "date");
            if (input.type === "text") {
                this.dateTime = false;
            } else {
                this.dateTime = true;
            }
        }
        return this.dateTime;
    }

}, Iresults.Object);
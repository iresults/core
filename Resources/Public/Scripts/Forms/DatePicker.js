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
 * An object oriented interface to the jQuery UI datepicker, with support for
 * some HTML5 attributes.
 *
 * The class also sets some defaults.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Forms
 * @version 1.0.0
 */
jQuery.fn.irFormsDatePicker = function () {
    var i, element,
        min, max,
        options,
        elementCount = 0;
    
    elementCount = this.length;
    if (elementCount === undefined) {
        return;
    }
    
    for (i = 0; i < elementCount; i++) {
        element = $(this[i]);
        options = {};
        
        if (element.data('min') !== undefined) { // Attribute data-min
            min = element.data('min');
            options.minDate = min;
        } else if (element.attr('min') !== undefined) { // Attribute min
            min = element.attr('min');
            min = new Date(Date.parse(min));
            options.minDate = min;
        }
        
        if (element.data('max') !== undefined) { // Attribute data-max
            max = element.data('max');
            options.maxDate = max;
        } else if (element.attr('max') !== undefined) { // Attribute max
            max = element.attr('max');
            max = new Date(Date.parse(max));
            options.maxDate = max;
        }
        element.datepicker(options);
    }
};

Iresults.Class.create('Iresults.Forms.DatePicker', {
    /**
     * Set the defaults when the class is initialized.
     * 
     * @returns	{Object}
     */
    initialize: function () {
        var defaults = {
            changeMonth: true,
            changeYear: true,
            yearRange: 'c-60:c+60'
        };
        $.datepicker.setDefaults(defaults);
        if (Iresults.Config && Iresults.Config.Locale) {
            $.datepicker.setDefaults($.datepicker.regional[Iresults.Config.Locale.countryCode]);
        }
    },
    
	/**
	 * Initialize the tree with the given selector.
	 * 
	 * @param	{String}	selector	A jQuery selector
	 * @returns	{Object}				Each element matching the selector
	 */
	create: function (selector) {
		return $(selector).irFormsDatePicker();
	}
});
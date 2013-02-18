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
 * A widget to toggle the display of content associtated to a headline.
 *
 * @include Iresults.Core.Core
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ui
 * @version 1.0.0
 */
Iresults.Class.extend('Iresults.Ui.Helpers.Toggle', {
	_init: function () {
		var _this = this,
			defaults;

        // The default options
        defaults = {
            duration: 1,
            animationComplete: function () {}
        };
        /*
         * Overwrite the defaults with the current options and save them as the
         * options
         */
        $.extend(defaults, this.options);
        this.options = defaults;

        return this.element.each(function () {
            $(this).children('div').children('h1,h2,h3,h4,h5,h6').addClass('trigger').click(function () {return _this.toggle(this); });
            $(this).children('div').children('p,div,ul').css('display', 'none');
        });
	},

    /**
     * Toggles the visibility of the content.
     *
     * @param	{object} trigger The object that triggered the function
     *
     * @returns void
     */
    toggle: function (trigger) {
        $(trigger).toggleClass('active');
        $(trigger).next('p,div,ul').slideToggle(this.options.duration, this.options.animationComplete);
    }
}, Iresults.Core);

$.widget("ui.iresultsUiHelpersToggle", Iresults.Ui.Helpers.Toggle);
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
 * A jQuery Plugin to make tables sortable.
 *
 * The class uses the tablesorter jQuery Plugin from Christian Bach. See
 * http://tablesorter.com/docs/#Configuration for configuration options and
 * more.
 *
 * @include Iresults.Core.Lib.Tablesorter.jquery:tablesorter
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ui
 * @version 1.0.0
 */
jQuery.fn.iresultsUiHelpersTablesorter = jQuery.fn.tablesorter;
Iresults.Class.create('Iresults.Ui.Helpers.Tablesorter', {
	/**
	 * Initialize the tree with the given selector.
	 *
	 * @param	{String}	selector	A jQuery selector
	 * @returns	{Object}				Each element matching the selector
	 */
	create: function (selector) {
		return $(selector).tablesorter();
	}
});






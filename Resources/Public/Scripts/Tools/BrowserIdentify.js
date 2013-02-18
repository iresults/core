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
var Iresults_Tools_Browser = window.Iresults_Tools_Browser || {};

/**
 * The iresults BrowserIdentify class checks the client's browser and sets an
 * according CSS-class to the HTML node-element.
 *
 * The class determines the clients browser through navigator.userAgent.
 *
 * @include Iresults.Core.Tools.Browser
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Tools
 * @version 1.0.0
 */
Iresults.Class.create('Iresults.Tools.BrowserIdentify', {
	create: function () {
		var name = Iresults_Tools_Browser.getName().toLowerCase(),
			version = Iresults_Tools_Browser.getVersion();
		document.documentElement.className = document.documentElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +
		(name + ' v' + version + ' ' + name + version);
	}
});

/**
 * Identify the browser when the page rendered completly.
 *
 * @returns	{Object}
 */
$(function () {
	Iresults.Tools.BrowserIdentify.create();
});

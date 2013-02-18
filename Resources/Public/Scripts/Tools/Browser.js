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
 * The iresults browser class provides functions to retrieve and check the
 * clients browser.
 *
 * The class determines the clients browser through navigator.userAgent.
 *
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Tools
 * @version 1.0.0
 */
function Iresults_Tools_Browser() {
	/**
	 * The allowed browsers. Stored as properties and values in the format ".browserName = version".
	 * If the version is set to 0 every version is supported. To fully exclude a browser set the version to an unreleased value (i.e. "100").
	 * WARNING: Only major versions are checked!
	 */
	this.allowedBrowsers = new Object();
	this.allowedBrowsers.IE 			= 7;
	this.allowedBrowsers.Firefox 		= 3;
	this.allowedBrowsers.Safari 		= 3;
	this.allowedBrowsers.MobileSafari 	= 3;
	this.allowedBrowsers.Opera			= 7;
	this.allowedBrowsers.Chrome 		= 3;


	this.name = '';
	this.version = '';

	this.parseNavigatorString();
}

/**
 * Extract the information from the sent user agent string.
 * @return	void
 */
Iresults_Tools_Browser.prototype.parseNavigatorString = function () {
	this.parseName();
	this.parseVersion();
};

/**
 * Extracts the name from the user agent string.
 *
 * @return	void
 */
Iresults_Tools_Browser.prototype.parseName = function () {
	if (this.name) return;
	var agentString = navigator.userAgent;
	if (agentString.search('Chrome') !== -1) {
		this.name = 'Chrome';
	} else if (agentString.search('iPod') !== -1 ||
			agentString.search('iPad') !== -1 ||
			agentString.search('iPhone') !== -1) {
		this.name = 'MobileSafari';
	} else if (agentString.search('Safari') !== -1) {
		this.name = 'Safari';
	} else if (agentString.search('Opera') !== -1) {
		this.name = 'Opera';
	}  else if (agentString.search('Firefox') !== -1) {
		this.name = 'Firefox';
	}  else if (agentString.search('MSIE') !== -1) {
		this.name = 'IE';
	}
};

/**
 * Extracts the version from the user agent string.
 *
 * @return	void
 */
Iresults_Tools_Browser.prototype.parseVersion = function () {
	if (this.version) return;
	/*
	 * 5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-us) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5
	 *
	 * 5.0 (Windows; U; Windows NT 6.0; en-us) AppleWebKit/533.18.1 (KHTML, like Gecko) Version/5.0.2 Safari/533.18.5
	 *
	 * 5.0 (iPhone; U; CPU iPhone OS 4_0_2 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A400 Safari/6531.22.7
	 *
	 * 5.0 (iPad; U; CPU OS 3_2_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B500 Safari/531.21.10
	 *
	 * 4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)
	 *
	 * 4.0 (compatible; MSIE 7.0; Windows NT 6.0)
	 *
	 * 4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)
	 *
	 * 5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3
	 *
	 * 5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9
	 *
	 * 9.80 (Macintosh; Intel Mac OS X; U; en) Presto/2.5.24 Version/10.53
	 *
	 * 5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Iron/5.0.381.0 Chrome/5.0.381 Safari/533.4
	 *
	 */
	var results, msg,
		searchExpression,
		agentString = navigator.userAgent,
		searchName = this.getName(),
		i = 'i';

	if (typeof console !== "undefined" || window.console) {
	} else {
		if (!window.console) console = {};
		console.log = console.log || function () {};
		console.warn = console.warn || function () {};
		console.error = console.error || function () {};
		console.info = console.info || function () {};
	}


	if (this.name === 'Safari' || this.name === 'MobileSafari') { // Pattern 1: "Version/5.0.2" => Safari, MobileSafari
		searchExpression = new RegExp('Version/\\d*\.', i);
		results = agentString.match(searchExpression);
		if (!results) return;

		this.version = (results[0].			// Get the first match
				replace('Version/', '').    // Remove "Version/"
				replace('.', '')            // Remove the dot at the end
				) * 1;						// Parse as number

	} else if (this.name === 'IE') { // Pattern 2: "MSIE 7.0" => IE
		searchExpression = new RegExp('MSIE [0-9]*');
		results = agentString.match(searchExpression);
		if (!results) return;

		this.version = (results[0].			// Get the first match
				replace('MSIE ', '').		// Remove the name
				replace('.', '')			// Remove the dot at the end
				) * 1;						// Parse as number

	} else if (this.name === 'Firefox' || this.name === 'Chrome') { // Pattern 3: "Firefox/3.6.3" => Firefox, Chrome, Iron
		searchExpression = new RegExp(searchName + '/\\d*.', i);
		results = agentString.match(searchExpression);
		if (!results) return;

		this.version = (results[0].				// Get the first match
				replace(searchName + '/', '').	// Remove the name
				replace('.', '')				// Remove the dot at the end
				) * 1;							// Parse as number

	} else if (this.name == 'Opera') { // Pattern 4: "9.80 " => Opera
		searchExpression = new RegExp('\\d*.', i);
		results = agentString.match(searchExpression);
		if (!results) return;

		this.version = (results[0].			// Get the first match
				replace('.', '')			// Remove the dot at the end
				) * 1;						// Parse as number

	} else {
		msg = 'Bad browser name';
		if (this.name) {
			msg = msg + ' ' + this.name;
		}
		console.log(msg);
	}
};

/**
 * Check if the user agents version is higher or equal to the minimum version.
 * @return	boolean
 */
Iresults_Tools_Browser.prototype.check = function () {
	var minVersion = this.allowedBrowsers[this.name];
	if (!this.version) {
		return true;
	} else if (this.version >= minVersion) {
		return true;
	} else {
		return false;
	}
};

/**
 * Displays a message if the browser is not valid.
 * @return	void
 */
Iresults_Tools_Browser.prototype.validate = function () {
	if (!this.check()) {
		var msg1 = 'Your browser is out of date.',
			msg2 = 'Just click on one of the icons below to get to the download page of a current browser.',
			imgPath = 'skin/frontend/leo/default/images',
			msg =	'<div class="Iresults_Tools_Browser_container Iresults_Tools_Browser"><div><p class="msg1">{msg1}</p><p class="msg2">{msg2}</p></div><table>	<thead></thead>	<tbody>' +
					'<tr><td><a href="http://www.mozilla.com/en-US/"><img src="{imgPath}/browser_firefox.gif" alt="Firefox" /></a></td><td><a href="http://www.apple.com/safari/"><img src="{imgPath}/browser_safari.gif" alt="Safari" /></a></td><td><a href="http://www.microsoft.com/windows/internet-explorer/default.aspx"><img src="{imgPath}/browser_ie.gif" alt="Internet Explorer" /></a></td><td><a href="http://www.google.com/chrome"><img src="{imgPath}/browser_chrome.gif" alt="Google Chrome" /></a></td><td><a href="http://www.opera.com/"><img src="{imgPath}/browser_opera.gif" alt="Opera" /></a></td></tr>' +
					'<tr><td>Firefox</td><td>Safari</td><td>Internet Explorer</td><td>Google Chrome</td><td>Opera</td></tr></tbody></table></div>';

		msg = msg.replace('{msg1}', msg1);
		msg = msg.replace('{msg2}', msg2);
		msg = msg.replace(/\{imgPath\}/g, imgPath);

		$('body').first().html(msg);
	}
};

/**
 * Returns the browser name.
 *
 * @return	{string} The detected browser name
 */
Iresults_Tools_Browser.prototype.getName = function () {
	this.parseName();
	return this.name;
};

/**
 * Returns the browser version.
 *
 * @return	{string} The detected browser version
 */
Iresults_Tools_Browser.prototype.getVersion = function () {
	this.parseVersion();
	return this.version;
};



/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* STATIC METHODS            MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
/**
 * The shared instance of the iresults browser class.
 *
 * @var Iresults_Tools_Browser
 */
Iresults_Tools_Browser.sharedInstance = null;

/**
 * Returns the browser name.
 *
 * @return	{string} The detected browser name
 */
Iresults_Tools_Browser.getName = function () {
	return Iresults_Tools_Browser.makeInstance().getName();
};

/**
 * Returns the browser version.
 *
 * @return	{string} The detected browser version
 */
Iresults_Tools_Browser.getVersion = function () {
	return Iresults_Tools_Browser.makeInstance().getVersion();
};

/**
 * Returns the shared instance of the iresults browser class.
 *
 * @return	{Iresults_Tools_Browser} Returns the shared instance
 */
Iresults_Tools_Browser.makeInstance = function () {
	if (!Iresults_Tools_Browser.sharedInstance) {
		Iresults_Tools_Browser.sharedInstance = new Iresults_Tools_Browser();
	}
	return Iresults_Tools_Browser.sharedInstance;
};

Iresults.Class.create('Iresults.Tools.Browser', Iresults_Tools_Browser);
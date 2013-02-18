/***************************************************************
 * quickTree 0.4 - Simple jQuery plugin to create tree-structure
 * navigation from an unordered list
 * http://scottdarby.com/
 *
 * Copyright (c) 2009 Scott Darby
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * (c) 2011 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 * Daniel Corn <cod@iresults.li>, iresults
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/



/**
 *
 * @include Iresults.Core.Core
 * @author	Scott Darby
 * @author	Daniel Corn <cod@iresults.li>
 * @package	Iresults
 * @subpackage	Iresults_Ui
 * @version 1.1.0
 */
jQuery.fn.tree = function () {
    return this.each(function () {

		// Set variables
        var $tree = $(this),
			$roots = $tree.find('li');

		// Set last list-item as variable (to allow different background graphic to be applied)
        $tree.find('li:last-child').addClass('last');

		// Add class to allow styling
        $tree.addClass('tree');

		// Hide all lists inside of main list by default
        $tree.find('ul').hide();

		// Iterate through all list items
        $roots.each(function () {
			//if list-item contains a child list
            if ($(this).children('ul').length > 0) {
				//add expand/contract control
                $(this).addClass('root closed').prepend('<span class="expand" />');
            }
        }); //end .each


		// Mark the last elements if the tree is open
		$tree.children('li:last-child').children('ul').children('li:last-child').each(function () {
			$(this).addClass('verylast-inner').addClass('verylast-inner-level-1');

			// Go down the DOM another level
			// @TODO: Make this recursive
			$(this).children('ul').children('li:last-child').each(function () {
				$(this).addClass('verylast-inner').addClass('verylast-inner-level-2');
			});
		});

		// Mark the last element in the root tree
		$tree.children('li:last-child').addClass('verylast-outer');

		// Handle clicking on expand/contract control
        $('span.expand').toggle(
			//if it's clicked once, find all child lists and expand
            function () {
				$(this).parent().addClass('open').removeClass('closed');
                $(this).addClass('contract').nextAll('ul').slideDown();
            },
			// If it's clicked again, find all child lists and contract
            function () {
				$(this).parent().removeClass('open').addClass('closed');
                $(this).removeClass('contract').nextAll('ul').slideUp();
            }
        );
    });
};

jQuery.fn.iresultsUiHelpersTree = jQuery.fn.tree;

Iresults.Class.create('Iresults.Ui.Helpers.Tree', {
	/**
	 * Initialize the tree with the given selector.
	 *
	 * @param	{String}	selector	A jQuery selector
	 * @returns	{Object}				Each element matching the selector
	 */
	create: function (selector) {
		return $(selector).tree();
	}
});
<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009-2012
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['randomArticle']			= array('Random mode', 'This setting is per module for each visitor.');
$GLOBALS['TL_LANG']['tl_module']['keepArticle']				= array('Keep article', 'Enter how many page views the same article should be shown.');
$GLOBALS['TL_LANG']['tl_module']['showTeaser']				= array('Show teaser', 'Show the teaser text instead of the article followed by a "Read more..." link.');
$GLOBALS['TL_LANG']['tl_module']['numberOfArticles']		= array('Number of articles', 'Here you can enter a number of articles to be displayed. Set 0 to display all articles.');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_module']['randomArticle_ref']['']	= 'Fetch new random article on each page load';
$GLOBALS['TL_LANG']['tl_module']['randomArticle_ref']['1']	= 'Keep random article for a number of page loads';
$GLOBALS['TL_LANG']['tl_module']['randomArticle_ref']['2']	= 'Keep one article for the whole session';


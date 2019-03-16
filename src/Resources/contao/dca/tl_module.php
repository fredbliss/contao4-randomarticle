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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][]	= 'randomArticle';
$GLOBALS['TL_DCA']['tl_module']['palettes']['randomarticle'] = '{title_legend},name,type;{reference_legend},rootPage,inColumn;{config_legend},randomArticle,numberOfArticles,showTeaser;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['randomArticle_interval'] = 'keepArticle';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['showTeaser'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['showTeaser'],
	'inputType'		=> 'checkbox',
	'exclude'		=> true,
	'eval'			=> array('tl_class'=>'clr'),
	'sql'			=> "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['randomArticle'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['randomArticle'],
	'inputType'		=> 'radio',
	'exclude'		=> true,
	'options'		=> array('each', 'interval', 'session'),
	'reference'		=> &$GLOBALS['TL_LANG']['tl_module']['randomArticle_ref'],
	'eval'			=> array('submitOnChange'=>true, 'mandatory'=>true),
	'sql'			=> "varchar(16) NOT NULL default 'each'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['keepArticle'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['keepArticle'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'eval'			=> array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>5, 'tl_class'=>'w50 clr'),
	'sql'			=> "smallint(5) unsigned NOT NULL default '10'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['numberOfArticles'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['numberOfArticles'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'eval'			=> array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>5, 'tl_class'=>'w50 clr'),
	'sql'			=> "smallint(5) unsigned NOT NULL default '1'"
);

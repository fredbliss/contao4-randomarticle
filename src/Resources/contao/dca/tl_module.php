<?php

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
 * Config
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = [\IntelligentSpark\RandomArticle\DataContainer\ModuleCallbackListener::class, 'onLoadCallback'];

 
/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][]	= 'randomArticle';
$GLOBALS['TL_DCA']['tl_module']['palettes']['randomarticle']	= '{title_legend},name,type;{reference_legend},rootPage,inColumn;{config_legend},randomArticle,showTeaser,numberOfArticles;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['randomarticle_each']	= '{title_legend},name,type;{reference_legend},rootPage,inColumn;{config_legend},randomArticle,showTeaser,numberOfArticles;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['randomarticle_interval']	= '{title_legend},name,type;{reference_legend},rootPage,inColumn;{config_legend},randomArticle,keepArticle,showTeaser,numberOfArticles;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['randomarticle_session']	= '{title_legend},name,type;{reference_legend},rootPage,inColumn;{config_legend},randomArticle,showTeaser,numberOfArticles;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
 

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['showTeaser'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['showTeaser'],
	'inputType'		=> 'checkbox',
	'exclude'		=> true,
	'sql'           => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['randomArticle'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['randomArticle'],
	'inputType'		=> 'radio',
	'exclude'		=> true,
	'default'       => 'each',
	'options'		=> array('each', 'interval', 'session'),
	'reference'		=> &$GLOBALS['TL_LANG']['tl_module']['randomArticle_ref'],
	'eval'			=> array('submitOnChange'=>true, 'mandatory'=>true),
	'sql'           => "varchar(16) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['keepArticle'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['keepArticle'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'default'		=> 10,
	'eval'			=> array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>3),
	'sql'           => "varchar(3) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['numberOfArticles'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['numberOfArticles'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'default'		=> 1,
	'eval'			=> array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>5),
	'sql'           => "smallint(5) unsigned NOT NULL default '1'"
);

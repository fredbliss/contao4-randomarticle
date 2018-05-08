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
 * @author     Jan Reuteler <jan.reuteler@iserv.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
namespace IntelligentSpark\RandomArticle;

use Contao\Module as Contao_Module;
 
class ModuleRandomArticle extends Module
{
	/**
	 * Tempalte
	 */
	protected $strTemplate = 'mod_randomarticle';


	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### RANDOM ARTICLE ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = $this->Environment->script.'?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$strFile = 'system/modules/frontend/ModuleArticle.php';

		// Check the file in Contao 3
		if (version_compare(VERSION, '3.0', '>='))
		{
			$strFile = 'system/modules/core/modules/ModuleArticle.php';
		}

		if (!file_exists(TL_ROOT . '/' . $strFile))
		{
			$this->log('Class ModuleArticle does not exist', 'ModuleRandomArticle compile()', TL_ERROR);
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		global $objPage;
		
		if (!strlen($this->inColumn))
		{
			$this->inColumn = 'main';
		}
		
		switch ($this->randomArticle)
		{
			// Keep the whole session
			case '2':
				if (is_array($_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles']) && !empty($_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles']))
				{
					$objArticlesStmt = $this->Database->prepare("SELECT tl_article.*, tl_page.id AS page_id, tl_page.alias AS page_alias FROM tl_article LEFT OUTER JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.id IN (" . implode(',', array_map('intval', $_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles'])) . ")");

					// Limit items
					if ($this->numberOfArticles > 0)
					{
						$objArticlesStmt->limit($this->numberOfArticles);
					}

					$objArticles = $objArticlesStmt->execute();
					break;
				}
				
			// Keep a number of times
			case '1':
				if (is_array($_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles']) && !empty($_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles']) && $this->keepArticle > 0 && $this->keepArticle > $_SESSION['MOD_RANDOMARTICLE'][$this->id]['count'])
				{
					$objArticlesStmt = $this->Database->prepare("SELECT tl_article.*, tl_page.id AS page_id, tl_page.alias AS page_alias FROM tl_article LEFT OUTER JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.id IN (" . implode(',', array_map('intval', $_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles'])) . ")");

					// Limit items
					if ($this->numberOfArticles > 0)
					{
						$objArticlesStmt->limit($this->numberOfArticles);
					}

					$objArticles = $objArticlesStmt->execute();
					break;
				}
			
			default:
				$_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles'] = array();
				$_SESSION['MOD_RANDOMARTICLE'][$this->id]['count'] = 0;
				$objArticlesStmt = $this->Database->prepare("SELECT tl_article.*, tl_page.id AS page_id, tl_page.alias AS page_alias FROM tl_article LEFT OUTER JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.pid=? AND tl_article.inColumn=? " . ((is_array($GLOBALS['RANDOMARTICLES']) && count($GLOBALS['RANDOMARTICLES'])) ? ' AND tl_article.id NOT IN (' . implode(',', $GLOBALS['RANDOMARTICLES']) . ') ' : '') . "AND (tl_article.start=? OR tl_article.start<?) AND (tl_article.stop=? OR tl_article.stop>?)" . (!BE_USER_LOGGED_IN ? ' AND tl_article.published=1' : '') . " ORDER BY RAND()");

				// Limit items
				if ($this->numberOfArticles > 0)
				{
					$objArticlesStmt->limit($this->numberOfArticles);
				}

				$objArticles = $objArticlesStmt->execute($this->rootPage, $this->inColumn, '', time(), '', time());
		}

		if ($objArticles->numRows < 1)
		{
			return;
		}
		
		$_SESSION['MOD_RANDOMARTICLE'][$this->id]['count'] = strlen($_SESSION['MOD_RANDOMARTICLE'][$this->id]['count']) ? ($_SESSION['MOD_RANDOMARTICLE'][$this->id]['count']+1) : 1;
		$arrArticles = array();

		// Generate articles
		while ($objArticles->next())
		{
			$_SESSION['MOD_RANDOMARTICLE'][$this->id]['articles'][] = $objArticles->id;
			$GLOBALS['RANDOMARTICLES'][] = $objArticles->id;

			// Print article as PDF
			if ($this->Input->get('pdf') == $objArticles->id)
			{
				$this->printArticleAsPdf($objArticles);
			}

			$objArticles->headline = $objArticles->title;
			$objArticles->showTeaser = $this->showTeaser;
			$objArticles->multiMode = $this->showTeaser ? true : false;
	
			$objArticle = new ModuleArticle($objArticles, $this->inColumn);
			$objArticle->cssID = $this->cssID;
			$objArticle->space = $this->space;
			
			// Overwrite article url
			if ($objArticles->numRows == 1)
			{
				$pageAlias = $objPage->alias;
				$pageId = $objPage->id;
				$objPage->alias = $objArticle->page_alias;
				$objPage->id = $objArticle->page_id;
			}

			$arrArticles[] = $objArticle->generate();
		}

		$this->Template->article = implode($arrArticles, "\n");
		
		// Reset page options
		if ($objArticles->numRows == 1)
		{
			$objPage->alias = $pageAlias;
			$objPage->id = $pageId;
		}
	}
}

 
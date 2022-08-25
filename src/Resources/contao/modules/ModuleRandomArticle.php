<?php

namespace IntelligentSpark\RandomArticle;

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

use Contao\Module as Contao_Module;
use Contao\ModuleArticle as Contao_ModuleArticle;
use Contao\System;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
 
class ModuleRandomArticle extends Contao_Module
{
	/**
	 * Template
	 */
	protected $strTemplate = 'mod_randomarticle';

	/**
	 * Session data
	 */
	protected $sessionData;


	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### RANDOM ARTICLE ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = $this->Environment->script.'?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		/** @var AttributeBagInterface $objSessionBag */
		$sessionBag = System::getContainer()->get('session')->getBag('contao_frontend');
		$sessionData = $sessionBag->has('MOD_RANDOMARTICLE') ? $sessionBag->get('MOD_RANDOMARTICLE') : [];

		if (isset($sessionData[$this->id])) {
			$this->sessionData = $sessionData[$this->id];
		}
		else {
			$this->sessionData = [
				'articles' => [],
				'count' => 0,
			];
		}

		$result = parent::generate();

		$sessionData[$this->id] = $this->sessionData;
		$sessionBag->set('MOD_RANDOMARTICLE', $sessionData);

		return $result;
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
			case 'session':
				if (is_array($this->sessionData['articles']) && !empty($this->sessionData['articles']))
				{
					$objArticlesStmt = $this->Database->prepare("SELECT tl_article.*, tl_page.id AS page_id, tl_page.alias AS page_alias FROM tl_article LEFT OUTER JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.id IN (" . implode(',', array_map('intval', $this->sessionData['articles'])) . ")");

					// Limit items
					if ($this->numberOfArticles > 0)
					{
						$objArticlesStmt->limit($this->numberOfArticles);
					}

					$objArticles = $objArticlesStmt->execute();
					break;
				}
			// Keep a number of times
			case 'interval':
				if (is_array($this->sessionData['articles']) && !empty($this->sessionData['articles']) && $this->keepArticle > 0 && $this->keepArticle > $this->sessionData['count'])
				{
					$objArticlesStmt = $this->Database->prepare("SELECT tl_article.*, tl_page.id AS page_id, tl_page.alias AS page_alias FROM tl_article LEFT OUTER JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.id IN (" . implode(',', array_map('intval', $this->sessionData['articles'])) . ")");

					// Limit items
					if ($this->numberOfArticles > 0)
					{
						$objArticlesStmt->limit($this->numberOfArticles);
					}

					$objArticles = $objArticlesStmt->execute();
					break;
				}
			case 'each':
			default:
				$this->sessionData['articles'] = array();
				$this->sessionData['count'] = 0;
				$objArticlesStmt = $this->Database->prepare("SELECT tl_article.*, tl_page.id AS page_id, tl_page.alias AS page_alias FROM tl_article LEFT OUTER JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.pid=? AND tl_article.inColumn=? " . ((is_array($GLOBALS['RANDOMARTICLES']) && count($GLOBALS['RANDOMARTICLES'])) ? ' AND tl_article.id NOT IN (' . implode(',', $GLOBALS['RANDOMARTICLES']) . ') ' : '') . "AND (tl_article.start=? OR tl_article.start<?) AND (tl_article.stop=? OR tl_article.stop>?)" . (!BE_USER_LOGGED_IN ? ' AND tl_article.published=1' : '') . " ORDER BY RAND()");

				// Limit items
				if ($this->numberOfArticles > 0)
				{
					$objArticlesStmt->limit($this->numberOfArticles);
				}

				$objArticles = $objArticlesStmt->execute($this->rootPage, $this->inColumn, '', time(), '', time());
				break;
		}

		if ($objArticles->numRows < 1)
		{
			return;
		}
		
		$this->sessionData['count']++;
		$arrArticles = array();

		// Generate articles
		while ($objArticles->next())
		{
			$this->sessionData['articles'][] = $objArticles->id;
			$GLOBALS['RANDOMARTICLES'][] = $objArticles->id;

			// Print article as PDF
			if ($this->Input->get('pdf') == $objArticles->id)
			{
				$this->printArticleAsPdf($objArticles);
			}

			$objArticles->headline = $objArticles->title;
			$objArticles->showTeaser = $this->showTeaser;
			$objArticles->multiMode = $this->showTeaser ? true : false;
	
			$objArticle = new Contao_ModuleArticle($objArticles, $this->inColumn);
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

		$this->Template->article = is_array($arrArticles) ? implode($arrArticles, "\n") : $arrArticles;;
		
		// Reset page options
		if ($objArticles->numRows == 1)
		{
			$objPage->alias = $pageAlias;
			$objPage->id = $pageId;
		}
	}
}

 

<?php

namespace IntelligentSpark\RandomArticle\DataContainer;

use Contao\ModuleModel;
use Contao\DataContainer;
use Contao\Input;

class ModuleCallbackListener
{
	/**
	 * Make root page selection mandatory for randomarticle module.
	 */
	public function onLoadCallback(DataContainer $dc)
	{
		if ($_POST || Input::get('act') != 'edit') {
			return;
		}

		$element = ModuleModel::findByPk($dc->id);

		if ($element === null) {
			return;
		}

		if ('randomarticle' === $element->type) {
			$GLOBALS['TL_DCA']['tl_module']['fields']['rootPage']['eval']['mandatory'] = true;
		}
	}
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class MultiWhisperer extends MultiSelectBox
{
	public function getControl(): Html
	{
		$control = parent::getControl();

		if($control->getAttribute('data-placeholder'))
		{
			$control->setAttribute('data-placeholder', $this->translate($control->getAttribute('data-placeholder')));
		}

		return $control;
	}
}

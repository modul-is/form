<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

/**
 * Multiselect with a search (combobox)
 */
class MultiWhisperer extends MultiSelectBox
{
	/**
	 * @param ?array<mixed> $items
	 */
	public function __construct
	(
		string|\Stringable|null $label = null,
		?array $items = null
	)
	{
		parent::__construct($label, $items);

		$this->setSearch();
	}


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

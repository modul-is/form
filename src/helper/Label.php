<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait Label
{
	public function getCoreLabel()
	{
		$required = $this->isRequired() ? 'required' : '';

		$label = $this->getLabel()->class('col-form-label ' . $required);

		if(!$this->tooltip)
		{
			return $label;
		}

		$tooltip = Html::el('span')
			->title($this->tooltip)
			->addAttributes(['data-placement' => 'right', 'data-toggle' => 'tooltip'])
			->addHtml(\Kravcik\Macros\FontAwesomeMacro::renderIcon('question-circle', ['color' => 'blue']));

		return $label . $tooltip;
	}
}

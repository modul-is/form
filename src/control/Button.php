<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class Button extends \Nette\Forms\Controls\Button
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;

	public function getCoreControl()
	{
		$input = $this->getControl();

		$label = $this->getCaption();

		$color = !empty($this->color) ? $this->color : 'gray';

		$button = \Nette\Utils\Html::el('button')
			->name($this->getName())
			->class('btn ' . $input->getAttribute('class') . ' btn-' . $color)
			->addHtml($this->icon ? \Kravcik\Macros\FontAwesomeMacro::renderIcon($this->icon, []) : '')
			->addHtml($label);

		if($this->getOption('id'))
		{
			$button->id($this->getOption('id'));
		}

		return $button;
	}


	public function render()
	{
		return $this->getCoreControl();
	}
}

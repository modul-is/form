<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class Button extends \Nette\Forms\Controls\Button implements Renderable
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;

	public function getCoreControl(): Html|string
	{
		$input = $this->getControl();

		$label = $this->getCaption();

		$color = !empty($this->color) ? $this->color : 'gray';

		$button = Html::el('button')
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


	public function render(): Html|string
	{
		return $this->getCoreControl();
	}
}

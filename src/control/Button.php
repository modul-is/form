<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Helper;
use Nette\Utils\Html;

class Button extends \Nette\Forms\Controls\Button implements Renderable
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;
	use Helper\ButtonRounded;

	public function getCoreControl(): Html|string
	{
		$input = $this->getControl();

		$label = $this->getCaption();

		$color = !empty($this->color) ? $this->color : 'gray';

		$button = Html::el('button')
			->name($this->getName())
			->type('button');

		$button->appendAttribute('class', 'btn px-4')
			->appendAttribute('class', 'btn-' . $color)
			->appendAttribute('class', $this->getFormButtonClass())
			->appendAttribute('class', (string) $input->getAttribute('class'));

		$button->addHtml($this->icon ? Extension::render($this->icon) : '')
			->addHtml($this->translate($label));

		if($this->getOption('id'))
		{
			$button->id($this->getOption('id'));
		}

		foreach($input->attrs as $name => $value)
		{
			if(in_array($name, ['name', 'required', 'data-nette-rules', 'class'], true))
			{
				continue;
			}

			$button->$name = $value;
		}

		return $button;
	}


	public function render(): Html|string
	{
		return $this->getCoreControl();
	}
}

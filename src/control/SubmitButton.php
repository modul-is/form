<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class SubmitButton extends \Nette\Forms\Controls\SubmitButton implements Renderable
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;
	use Helper\ButtonRounded;

	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$color = !empty($this->color) ? $this->color : 'gray';

		$button = Html::el('button');

		$button->name($this->getName())
			->type('submit')
			->formnovalidate(true);

		$button->addClass('btn px-4')
			->addClass('btn-' . $color)
			->addClass($this->getFormButtonClass())
			->addClass($input->getAttribute('class'));

		if($this->icon)
		{
			$iconHtml = \Kravcik\LatteFontAwesomeIcon\Extension::render($this->icon);
			$button->addHtml($iconHtml . '&nbsp;');
		}

		$button->addHtml($this->translate($this->getCaption()));

		$scopeString = 'data-nette-validation-scope';

		if($input->getAttribute($scopeString))
		{
			$button->setAttribute($scopeString, $input->getAttribute($scopeString));
		}

		if($this->getOption('id'))
		{
			$button->id($this->getOption('id'));
		}

		foreach($input->attrs as $name => $value)
		{
			if(in_array($name, ['name', 'required', 'data-nette-rules', 'class', 'formnovalidate'], true))
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

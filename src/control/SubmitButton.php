<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class SubmitButton extends \Nette\Forms\Controls\SubmitButton
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;

	public function getCoreControl()
	{
		$input = $this->getControl();

		$color = !empty($this->color) ? $this->color : 'gray';

		$button = Html::el('button')
			->name($this->getName())
			->class('btn ' . $input->getAttribute('class') . ' btn-' . $color)
			->type('submit')
			->formnovalidate('')
			->addHtml($this->icon ? \Kravcik\Macros\FontAwesomeMacro::renderIcon($this->icon, []) . '&nbsp;' : '')
			->addHtml($this->getCaption());

		$scopeString = 'data-nette-validation-scope';

		if($input->getAttribute($scopeString))
		{
			$button->$scopeString($input->getAttribute($scopeString));
		}

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

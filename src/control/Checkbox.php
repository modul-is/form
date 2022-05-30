<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class Checkbox extends \Nette\Forms\Controls\Checkbox
{
	use Helper\Color;
	use Helper\Input;
	use Helper\ControlPart;
	use Helper\AutoRenderSkip;

	public function getCoreLabel()
	{
		return null;
	}


	public function getCoreControl()
	{
		$input = $this->getControlPart();

		$label = Html::el('span')
			->class('label-text ' . $input->getAttribute('class'))
			->addHtml($this->caption);

		if($this->color)
		{
			$input->setAttribute('class', $input->getAttribute('class') . ' checkbox-' . $this->color);
		}

		$labelWrap = Html::el('label')
			->addHtml($input . $label);

		$wrap = Html::el('div')
			->class('form-check ');

		$errorMessage = '';

		if($this->hasErrors())
		{
			$errorMessage = Html::el('div')
				->class('check-invalid')
				->addHtml($this->getError());
		}

		if($this->tooltip)
		{
			$tooltip = Html::el('span')
				->title($this->tooltip)
				->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
				->addHtml(\Kravcik\Macros\FontAwesomeMacro::renderIcon('question-circle', ['color' => 'blue']));

			return $wrap->addHtml($labelWrap)
				->addHtml($tooltip)
				->addHtml($errorMessage);
		}
		else
		{
			return $wrap->addHtml($labelWrap)
				->addHtml($errorMessage);
		}
	}


	public function render()
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return null;
		}

		$input = $this->getCoreControl();

		$inputDiv = Html::el('div')
			->class('col-sm-8 offset-sm-4')
			->addHtml($input);

		$outerDiv = Html::el('div')
			->class('form-group row')
			->addHtml($inputDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

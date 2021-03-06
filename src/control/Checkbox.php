<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class Checkbox extends \Nette\Forms\Controls\Checkbox implements Renderable
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\ValidationSuccessMessage;

	public function getCoreLabel()
	{
		return null;
	}


	public function getCoreControl(): Html
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

		$validationFeedBack = null;

		if($this->hasErrors())
		{
			$validationFeedBack = Html::el('div')
				->class('check-invalid')
				->addHtml($this->getError());
		}
		elseif($this->getValidationSuccessMessage())
		{
			$validationFeedBack = Html::el('div')
				->class('valid-feedback')
				->addHtml($this->getValidationSuccessMessage());
		}

		$wrap->addHtml($labelWrap);
		
		if($this->tooltip)
		{
			$tooltip = Html::el('span')
				->title($this->tooltip)
				->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
				->addHtml(\Kravcik\Macros\FontAwesomeMacro::renderIcon('question-circle', ['color' => 'blue']));

			$wrap->addHtml($tooltip);
		}

		return $wrap->addHtml($validationFeedBack);
		
	}


	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->getOption('template'))
		{
			return (new \Latte\Engine)->renderToString($this->getOption('template'), $this);
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

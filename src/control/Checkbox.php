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
	use Helper\Validation;
	use Helper\WrapClassHelper;
	use Helper\RenderInlineHelper;

	private bool $switch = false;


	public function setSwitch(bool $switch = true): self
	{
		$this->switch = $switch;

		return $this;
	}


	public function getCoreLabel()
	{
		return null;
	}


	public function getCoreControl(): string|Html
	{
		$input = $this->getControlPart();

		$validationClass = $this->getValdiationClass();
		$validationMessage = $this->getValidationFeedback();

		$inputColorClass = $this->color ? ' checkbox-' . $this->color : null;

		$input->class('form-check-input ' . $input->getAttribute('class') . ($validationClass ? ' ' . $validationClass : null) . $inputColorClass);

		$label = Html::el('label')
			->setAttribute('for', $this->getHtmlId())
			->class('form-check-label')
			->addHtml($this->caption);

		$switchClass = $this->switch ? ' form-switch' : null;

		$wrapDiv = Html::el('div')
			->class('form-check' . $switchClass)
			->addHtml($input . $label);

		if($this->tooltip)
		{
			$tooltip = Html::el('span')
				->title($this->tooltip)
				->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
				->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('question-circle', color: 'blue'));

			$wrapDiv->addHtml($tooltip);
		}

		return $wrapDiv->addHtml($validationMessage);
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
			->class('mb-3 row')
			->addHtml($inputDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

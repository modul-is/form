<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Helper;
use Nette\Utils\Html;

class Checkbox extends \Nette\Forms\Controls\Checkbox implements Renderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\ToggleButton;

	private bool $switch = false;

	private ?string $inputClass = null;

	private ?string $labelClass = null;

	private ?string $checkboxClass = null;

	private ?string $wrapClass = null;


	public function setSwitch(bool $switch = true): self
	{
		$this->switch = $switch;

		return $this;
	}


	public function getCoreLabel()
	{
		return null;
	}


	public function setInputWrapClass(string $class): self
	{
		$this->inputClass = $class;

		return $this;
	}


	public function setLabelWrapClass(string $class): self
	{
		$this->labelClass = $class;

		return $this;
	}


	public function setCheckboxWrapClass(string $class): self
	{
		$this->checkboxClass = $class;

		return $this;
	}


	public function setWrapClass(string $class): self
	{
		$this->wrapClass = $class;

		return $this;
	}


	public function getCoreControl(): string|Html
	{
		$input = $this->getControlPart();

		$validationClass = $this->getValidationClass();
		$validationMessage = $this->getValidationFeedback();

		$input->class($input->getAttribute('class') . ($validationClass ? ' ' . $validationClass : null));

		if($this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$boxSpan = Html::el('span')
			->class('box')
			->addHtml(Extension::render('check', 'white'));

		$labelClass = 'checkbox' . ($this->isRequired() ? ' required' : null);
		$labelClass = $this->labelClass ? $labelClass . ' ' . $this->labelClass : $labelClass;

		$label = Html::el('label')
			->class($labelClass)
			->addHtml($input)
			->addHtml($boxSpan)
			->addHtml($this->translate($this->caption));

		$checkboxClass = $this->checkboxClass ? ' ' . $this->checkboxClass : null;

		$wrapDiv = Html::el('div')
			->class('new-design-checkbox-row' . $checkboxClass)
			->addHtml($label);

		if($this->tooltip)
		{
			$tooltip = Html::el('span')
				->title($this->tooltip)
				->addAttributes(['data-bs-placement' => 'top', 'data-bs-toggle' => 'tooltip', 'data-bs-html' => 'true'])
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

		if($this->templatePath)
		{
			return (new \Latte\Engine)->renderToString($this->templatePath, $this->templateParams);
		}

		$input = $this->getCoreControl();

		$inputClass = $this->inputClass ?? 'col-sm-8 offset-sm-4';

		$wrapClass = 'mb-3 ' . ($this->wrapClass ?? 'col-12');

		$inputDiv = Html::el('div')
			->class($inputClass)
			->addHtml($input);

		$rowDiv = Html::el('div')
			->class('row')
			->addHtml($inputDiv);

		$outerDiv = Html::el('div')
			->class($wrapClass)
			->addHtml($rowDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

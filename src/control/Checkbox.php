<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class Checkbox extends \Nette\Forms\Controls\Checkbox implements Renderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\ControlClass;
	use Helper\Signals;

	private bool $switch = false;

	private ?string $inputClass = null;

	private ?string $labelClass = null;

	private ?string $checkboxClass = null;

	private ?string $wrapClass = null;

	private bool $checkboxLeft = true;


	public function setSwitch(bool $switch = true): static
	{
		$this->switch = $switch;

		return $this;
	}


	public function setCheckboxLeft(bool $checkboxLeft = true): static
	{
		$this->checkboxLeft = $checkboxLeft;

		return $this;
	}


	public function getCoreLabel(): null
	{
		return null;
	}


	public function setInputWrapClass(string $class): static
	{
		$this->inputClass = $class;

		return $this;
	}


	public function setLabelWrapClass(string $class): static
	{
		$this->labelClass = $class;

		return $this;
	}


	public function setCheckboxWrapClass(string $class): static
	{
		$this->checkboxClass = $class;

		return $this;
	}


	public function setWrapClass(string $class): static
	{
		$this->wrapClass = $class;

		return $this;
	}


	public function getCoreControl(): string|Html
	{
		$input = $this->getControlPart();

		$validationClass = $this->getValidationClass();
		$validationMessage = $this->getValidationFeedback();

		if($this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$polyline = Html::el('polyline')
			->setAttribute('points', '20 6 9 17 4 12');

		$svg = Html::el('svg')
			->setAttribute('viewBox', '0 0 24 24')
			->setAttribute('fill', 'none')
			->setAttribute('stroke-linecap', 'round')
			->addHtml($polyline);

		$boxSpan = Html::el('span')
			->class('box')
			->addHtml($svg);

		$required = '';

		if($this->isRequired())
		{
			$required = Html::el('span')
				->class('required')
				->addText('*');
		}

		$labelClass = 'checkbox' . ($validationClass ? ' ' . $validationClass : null);
		$labelClass = $this->switch ? $labelClass . ' checkbox-switch' : $labelClass;
		$labelClass = $this->labelClass ? $labelClass . ' ' . $this->labelClass : $labelClass;

		if($this->checkboxLeft)
		{
			$label = Html::el('label')
				->class($labelClass)
				->addHtml($input)
				->addHtml($boxSpan)
				->addText($this->translate($this->getCaption()))
				->addHtml($required);

			$checkboxClass = $this->checkboxClass ? ' ' . $this->checkboxClass : null;

			$wrapDiv = Html::el('div')
				->class('mis-checkbox' . $checkboxClass)
				->addHtml($label);

			if($this->tooltip)
			{
				$tooltip = Html::el('span')
					->title($this->tooltip)
					->addAttributes(['data-bs-placement' => 'top', 'data-bs-toggle' => 'tooltip', 'data-bs-html' => 'true'])
					->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('question-circle', color: 'blue'));

				$wrapDiv->addHtml($tooltip);
			}
		}
		else
		{
			 $label = Html::el('label')
				 ->class('rf-label')
				 ->for($input->getAttribute('id'))
				 ->addText($this->translate($this->getCaption()))
				 ->addHtml($required);

			 $inputLabel = Html::el('label')
				 ->class($labelClass)
				 ->addHtml($input)
				 ->addHtml($boxSpan);

			$control = Html::el('div')
				->class('rf-control')
				->addHtml($inputLabel);

			$wrapDiv = Html::el('div')
				->class('mis-checkbox-right')
				->addHtml($label)
				->addHtml($control);
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

		$inputClass = $this->inputClass ?? 'col-12';

		$wrapClass = $this->wrapClass ?? 'col-12';

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

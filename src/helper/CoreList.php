<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait CoreList
{
	public array $tooltips = [];

	public int $itemsPerRow = 1;

	public ?string $itemClass = null;


	public function getCoreControl(): Html|string
	{
		$inputs = null;

		foreach($this->getItems() as $key => $input)
		{
			$input = $this->getControlPart($key);

			$inputColorClass = $this->color ? ' checkbox-' . $this->color : null;

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : null;

			$input->class('form-check-input' . $currentClass . $inputColorClass);

			$label = $this->getLabelPart($key);

			$label->class('form-check-label');

			$tooltip = null;

			if(isset($this->tooltips[$key]))
			{
				$tooltip = Html::el('span')
					->title($this->tooltips[$key])
					->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
					->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('question-circle', color: 'blue'));
			}

			$class = 'form-check-inline mr-0 col-' . 12 / $this->itemsPerRow;

			if($this->itemClass)
			{
				$class .= ' ' . $this->itemClass;
			}

			$inputs .= Html::el('div')
				->class('form-check ' . $class)
				->addHtml($input . $label . $tooltip);
		}

		$validationFeedBack = null;
		$validationClass = null;

		if($this->getForm()->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationClass = ' is-invalid';
				$validationFeedBack = Html::el('div')
					->class('check-invalid')
					->addHtml($this->getError());
			}
			elseif($this->getValidationSuccessMessage())
			{
				$validationClass = ' is-valid';
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		$wrapRow = Html::el('div')
			->class('row')
			->addHtml($inputs);

		$wrapContainer = Html::el('div')
			->class('container' . $validationClass)
			->addHtml($wrapRow);

		return $wrapContainer . $validationFeedBack;
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

		$label = $this->getCoreLabel();

		$labelDiv = Html::el('div')
			->class('col-sm-4 control-label align-self-center')
			->addHtml($label);

		$input = $this->getCoreControl();

		$inputDiv = Html::el('div')
			->class('col-sm-8')
			->addHtml($input);

		$outerDiv = Html::el('div')
			->class('mb-3 row')
			->addHtml($labelDiv . $inputDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}


	public function setTooltips(array $tooltips): self
	{
		$this->tooltips = $tooltips;
		return $this;
	}


	public function setItemsPerRow(int $number): self
	{
		$this->itemsPerRow = $number;
		return $this;
	}


	public function setItemClass(string $itemClass): self
	{
		$this->itemClass = $itemClass;
		return $this;
	}
}

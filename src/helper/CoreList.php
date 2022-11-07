<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait CoreList
{
	protected array $tooltips = [];

	protected int $itemsPerRow = 1;

	protected ?string $itemClass = null;

	protected array $wrapRowAttributes = [];


	public function getCoreControl(): Html|string
	{
		$inputs = null;

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

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

			if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($input);
			}

			$inputs .= Html::el('div')
				->class('form-check ' . $class)
				->addHtml($input . $label . $tooltip);
		}

		$validationFeedBack = null;
		$validationClass = null;

		if($form->isAnchored() && $form->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationClass = ' is-invalid';
				$validationFeedBack = Html::el('div')
					->class('invalid-feedback')
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

		foreach($this->wrapRowAttributes as $attribute => $value)
		{
			$wrapRow->addAttributes([$attribute => $value]);
		}

		$wrapContainer = Html::el('div')
			->class('container' . $validationClass)
			->addHtml($wrapRow);

		return $wrapContainer . $validationFeedBack;
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


	public function setWrapAttributes(array $attributes): self
	{
		$this->wrapRowAttributes = $attributes;

		return $this;
	}
}

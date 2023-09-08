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

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		foreach($this->getItems() as $key => $input)
		{
			$input = $this->getControlPart($key);

			$inputColorClass = $this->color ? ' checkbox-' . $this->color : null;

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : null;

			if($this->toggleButton)
			{
				if(is_array($this->buttonColor))
				{
					$buttonColor = $this->buttonColor[$key] ?? 'primary';
				}
				else
				{
					$buttonColor = $this->buttonColor;
				}

				$inputClass = 'btn-check';
				$labelClass = 'me-2 btn btn-' . $buttonColor;
				$labelAttribute = 'width: calc(100% - 7.5px)';
			}
			else
			{
				$inputClass = 'form-check-input';
				$labelClass = 'form-check-label';
				$labelAttribute = 'width: auto';
			}

			$input->class($inputClass . $currentClass . $inputColorClass);

			$label = $this->getLabelPart($key);

			$label->class($labelClass)
				->setAttribute('style', $labelAttribute);

			$tooltip = null;

			if(isset($this->tooltips[$key]))
			{
				$tooltip = Html::el('span')
					->title($this->tooltips[$key])
					->addAttributes(['data-bs-placement' => 'top', 'data-bs-toggle' => 'tooltip'])
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
				->class(($this->toggleButton ? 'p-0 ' : 'form-check ') . $class)
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
			->addAttributes($this->wrapRowAttributes)
			->class('row');

		if($inputs)
		{
			$wrapRow->addHtml($inputs);
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

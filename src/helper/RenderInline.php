<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Form;
use Nette\Utils\Html;

trait RenderInline
{
	protected ?string $labelClass = null;

	protected ?string $inputClass = null;

	protected ?string $rowClass = null;

	protected ?Html $wrapControl = null;


	public function setLabelWrapClass(string $class): self
	{
		$this->labelClass = $class;

		return $this;
	}


	public function getLabelWrapClass(): ?string
	{
		return $this->labelClass;
	}


	public function setInputWrapClass(string $class): self
	{
		$this->inputClass = $class;

		return $this;
	}


	public function getInputWrapClass(): ?string
	{
		return $this->inputClass;
	}


	public function setRowClass(string $class): self
	{
		$this->rowClass = $class;

		return $this;
	}


	public function setWrapClass(string $class): self
	{
		$this->getWrapControl()
			->setAttribute('class', $class);

		return $this;
	}


	public function setWrapId(string $id): self
	{
		$this->getWrapControl()
			->setAttribute('id', $id);

		return $this;
	}


	public function getWrapControl(): Html
	{
		if(!$this->wrapControl)
		{
			$form = $this->getForm();
			assert($form instanceof Form);

			$this->wrapControl = Html::el('div')
				->class($form->getDefaultInputWrapClass());
		}

		return $this->wrapControl;
	}


	public function renderInline(): Html
	{
		$label = $this->getCoreLabel();
		$input = $this->getControl();

		$input->appendAttribute('class', $this->controlClass);

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$quickCopyHtml = $this instanceof QuickCopyable && $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$input->appendAttribute('class', $validationClass);

		$inputControl = Html::el('div')
			->class('input-group' . ($validationClass ? ' ' . $validationClass : ''))
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $quickCopyHtml);

		$labelDiv = Html::el('div')
			->class('text-input-new-label')
			->addHtml($label);

		$inputDiv = Html::el('div')
			->class('text-input-new-control')
			->addHtml($inputControl)
			->addHtml($validationFeedBack);

		return $this->getWrapControl()
			->class('text-input-new-inline ' . ($this->getWrapControl()->getAttribute('class') ?: ''))
			->addHtml($labelDiv)
			->addHtml($inputDiv);
	}
}

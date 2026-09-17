<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Form;
use Nette\Utils\Html;
use function assert;

trait WrapControl
{
	protected ?string $labelClass = null;

	protected ?string $inputClass = null;

	protected ?string $rowClass = null;

	protected ?Html $wrapControl = null;


	public function setLabelWrapClass(string $class): static
	{
		$this->labelClass = $class;

		return $this;
	}


	public function getLabelWrapClass(): ?string
	{
		return $this->labelClass;
	}


	public function setInputWrapClass(string $class): static
	{
		$this->inputClass = $class;

		return $this;
	}


	public function getInputWrapClass(): ?string
	{
		return $this->inputClass;
	}


	public function setRowClass(string $class): static
	{
		$this->rowClass = $class;

		return $this;
	}


	public function setWrapClass(string $class): static
	{
		$this->getWrapControl()
			->setAttribute('class', $class);

		return $this;
	}


	public function setWrapId(string $id): static
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
}

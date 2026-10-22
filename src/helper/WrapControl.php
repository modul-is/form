<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Form;
use Nette\Utils\Html;

trait WrapControl
{
	protected ?string $labelClass = null;

	protected ?string $inputClass = null;

	protected ?string $rowClass = null;

	/** @var array<string, int> breakpoint => columns */
	protected array $width = [];

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


	public function getRowClass(): ?string
	{
		return $this->rowClass;
	}


	/**
	 * Replaces the whole class of the wrapper (default is set by Form::setDefaultInputWrapClass())
	 */
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


	/**
	 * Width of the field in grid columns - an int is the width from the md breakpoint up (full width below it),
	 * an array sets it per breakpoint, e.g. ['md' => 6, 'xl' => 4]. Empty breakpoint '' means all screens.
	 *
	 * @param int|array<string, int> $width
	 */
	public function setWidth(int|array $width): static
	{
		$this->width = is_int($width) ? ['md' => $width] : $width;

		return $this;
	}


	/**
	 * Prototype of the wrapper - holds only what was set by setWrapClass()/setWrapId().
	 * Use createWrap() for rendering, otherwise repeated render() would fill the prototype twice.
	 */
	public function getWrapControl(): Html
	{
		if(!$this->wrapControl)
		{
			$form = $this->getForm();
			\assert($form instanceof Form);

			$this->wrapControl = Html::el('div')
				->class($form->getDefaultInputWrapClass());
		}

		return $this->wrapControl;
	}


	/**
	 * Fresh wrapper for one render - keeps id and classes of the prototype, adds width classes
	 */
	public function createWrap(?string $class = null): Html
	{
		$wrap = clone $this->getWrapControl();

		foreach($this->width as $breakpoint => $columns)
		{
			$wrap->appendAttribute('class', ($breakpoint === '' ? '' : $breakpoint . ':') . 'col-' . $columns);
		}

		if($class)
		{
			$wrap->appendAttribute('class', $class);
		}

		return $wrap;
	}
}

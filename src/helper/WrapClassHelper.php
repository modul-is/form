<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait WrapClassHelper
{
	protected ?string $wrapClass = null;

	protected ?string $labelClass = null;

	protected ?string $inputClass = null;


	public function setLabelWrapClass(string $class): self
	{
		$this->labelClass = $class;

		return $this;
	}


	public function setInputWrapClass(string $class): self
	{
		$this->inputClass = $class;

		return $this;
	}


	public function setWrapClass(string $class): self
	{
		$this->wrapClass = $class;

		return $this;
	}
}

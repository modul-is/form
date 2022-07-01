<?php

namespace ModulIS\Form\Helper;

trait WrapClassHelper
{
	private ?string $wrapClass = null;
	
	private ?string $labelClass = null;
	
	private ?string $inputClass = null;

	
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

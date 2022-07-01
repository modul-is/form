<?php

namespace ModulIS\Form\Helper;

trait ClassHelper
{
	private ?string $labelClass = null;
	
	private ?string $inputClass = null;
	
	
	
	public function setLabelWrapClass(string $class): self
	{
		$this->labelClass = $class;
		
		return $this;
	}
	
	
	public function setInputWrapClass(string $class): self
	{
		$this->labelClass = $class;
		
		return $this;
	}
}

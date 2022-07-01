<?php

namespace ModulIS\Form\Helper;

trait WrapClassHelper
{
	private ?string $wrapClass = null;
	
	
	public function setWrapClass(string $class): self
	{
		$this->wrapClass = $class;
		
		return $this;
	}
}

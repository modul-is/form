<?php

namespace ModulIS\Form\Helper;

trait ControlClass
{
	public function setClass(string $class): self
	{
		$this->setHtmlAttrbute('class', $class);
		
		return $this;
	}
	
	
	public function getClass()
	{
		return $this->getControl()->getAttribute('class');
	}
}

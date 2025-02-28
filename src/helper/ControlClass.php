<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait ControlClass
{
	public function setClass(string $class): self
	{
		$this->setHtmlAttribute('class', $class);

		return $this;
	}


	public function getClass()
	{
		return $this->getControl()->getAttribute('class');
	}
}

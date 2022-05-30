<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait Template
{
	public function setTemplate(string $path): static
	{
		$this->setOption('template', $path);
		
		return $this;
	}
}

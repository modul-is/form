<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait Template
{
	protected ?string $templatePath = null;


	public function setTemplate(string $path): static
	{
		$this->templatePath = $path;

		return $this;
	}
}

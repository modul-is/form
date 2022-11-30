<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use \Nette\Bridges\ApplicationLatte\Template;

trait Template
{
	protected ?string $templatePath = null;

	protected array $templateParams = [];

	protected ?Template $templateEngine = null;


	public function setTemplate(?string $path, array $params = [], Template $templateEngine = null): static
	{
		$this->templatePath = $path;
		$this->templateParams = $params;
		$this->templateEngine = $templateEngine;

		return $this;
	}
}
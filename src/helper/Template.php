<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Bridges\ApplicationLatte\Template as TemplateEngine;

trait Template
{
	protected ?string $templatePath = null;

	protected array $templateParams = [];

	protected ?TemplateEngine $templateEngine = null;


	public function setTemplate(?string $path, array $params = [], TemplateEngine $templateEngine = null): static
	{
		$this->templatePath = $path;
		$this->templateParams = $params;
		$this->templateEngine = $templateEngine;

		return $this;
	}
}

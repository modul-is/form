<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Application\UI\TemplateFactory;

trait Template
{
	protected ?string $templatePath = null;

	protected array $templateParams = [];

	protected ?TemplateFactory $TemplateFactory = null;


	public function setTemplate(?string $path, array $params = []): static
	{
		$this->templatePath = $path;
		$this->templateParams = $params;

		return $this;
	}


	public function setTemplateFactory(TemplateFactory $templateFactory): static
	{
		$this->TemplateFactory = $templateFactory;

		return $this;
	}
}

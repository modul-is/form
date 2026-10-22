<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Bridges\ApplicationLatte\Template as TemplateEngine;

trait Template
{
	protected ?string $templatePath = null;

	/** @var array<string, mixed> */
	protected array $templateParams = [];

	protected ?TemplateEngine $templateEngine = null;


	/**
	 * Custom Latte template instead of the default render, the control is available as $input
	 * @param array<string, mixed> $params
	 */
	public function setTemplate(?string $path, array $params = [], ?TemplateEngine $templateEngine = null): static
	{
		$this->templatePath = $path;
		$this->templateParams = $params;
		$this->templateEngine = $templateEngine;

		return $this;
	}


	/**
	 * Null when no template is set. While the template is being rendered it is switched off,
	 * so the template may call $input->render() to get the default markup of the control.
	 */
	public function renderTemplate(): ?string
	{
		if($this->templatePath === null)
		{
			return null;
		}

		$path = $this->templatePath;
		$params = ['input' => $this] + $this->templateParams;

		$this->templatePath = null;

		try
		{
			return $this->templateEngine
				? $this->templateEngine->renderToString($path, $params)
				: (new \Latte\Engine)->renderToString($path, $params);
		}
		finally
		{
			$this->templatePath = $path;
		}
	}
}

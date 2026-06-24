<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Enum\RenderType;
use Nette\Utils\Html;

trait Render
{
	protected ?RenderType $renderType = null;


	public function setRenderType(RenderType $renderType): self
	{
		$this->renderType = $renderType;

		return $this;
	}


	public function getRenderType(): ?RenderType
	{
		return $this->renderType;
	}


	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->templatePath)
		{
			$path = $this->templatePath;

			$this->setTemplate(null, $this->templateParams, $this->templateEngine);

			$template = $this->templateEngine ?: new \Latte\Engine;

			return $template->renderToString($path, array_merge(['input' => $this], $this->templateParams));
		}

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$effectiveRenderType = $this->getRenderType() ?: $form->getRenderType();

		$outerDiv = match($effectiveRenderType)
		{
			RenderType::Floating => $this->renderFloating(),
			RenderType::Inline => $this->renderInline(),
			RenderType::Default => $this->renderDefault()
		};

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait RenderBasic
{
	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->templatePath)
		{
			$path = $this->templatePath;

			$this->setTemplate(null, $this->templateParams);

			if($this->TemplateFactory)
			{
				$template = $this->TemplateFactory->createTemplate();
			}
			else
			{
				$template = new \Latte\Engine;
			}

			return $template->renderToString($path, array_merge(['input' => $this], $this->templateParams));
		}

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		if($this instanceof \ModulIS\Form\Control\FloatingRenderable && ($this->getRenderFloating() ?? $form->getRenderFloating()))
		{
			$outerDiv = $this->renderFloating();
		}
		else
		{
			$outerDiv = $this->renderWrap();
		}

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

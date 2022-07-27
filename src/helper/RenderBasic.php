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
			return (new \Latte\Engine)->renderToString($this->templatePath, $this);
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

<?php

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait RenderBasic
{
	protected function renderBasic()
	{
		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();
		
		$label = $this->getCoreLabel();
		$input = $this->getCoreControl();

		$wrapClass = 'mb-3 ' . ($this->wrapClass ?? 'col-12');
		$inputClass = 'align-self-center';
		$labelClass = 'align-self-center';

		if($this->getRenderInline() ?? $form->getRenderInline())
		{
			$inputClass .= $this->inputClass ? ' ' . $this->inputClass : ' col-sm-12';
			$labelClass .= $this->labelClass ? ' ' . $this->labelClass : ' col-sm-12';
		}
		else
		{
			$inputClass .= $this->inputClass ? ' ' . $this->inputClass : ' col-sm-8';
			$labelClass .= $this->labelClass ? ' ' . $this->labelClass : ' col-sm-4';
		}

		$labelDiv = Html::el('div')
			->class($labelClass)
			->addHtml($label);

		$inputDiv = Html::el('div')
			->class($inputClass)
			->addHtml($input);

		$rowDiv = Html::el('div')
			->class('row')
			->addHtml($labelDiv . $inputDiv);

		return Html::el('div')
			->class($wrapClass)
			->addHtml($rowDiv);
	}

	
	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->getOption('template'))
		{
			return (new \Latte\Engine)->renderToString($this->getOption('template'), $this);
		}

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		if($this instanceof \ModulIS\Form\Control\FloatingRenderable && ($this->getRenderFloating() ?? $form->getRenderFloating()))
		{
			$outerDiv = $this->renderFloating();
		}
		else
		{
			$outerDiv = $this->renderBasic();
		}

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

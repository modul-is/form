<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait RenderFloating
{
	public ?bool $renderFloating = null;


	public function setRenderFloating(bool $renderFloating = true): self
	{
		$this->renderFloating = $renderFloating;

		return $this;
	}


	public function getRenderFloating(): ?bool
	{
		return $this->renderFloating;
	}
	
	
	protected function renderFloating(): Html
	{
		$wrapClass = 'mb-3 ' . ($this->wrapClass ?? 'col-12');
		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$input = $this->getControl();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

		$input->class('form-control' . $currentClass . $validationClass);
		$input->placeholder($this->getCaption());

		$label = $this->getLabel();

		$floatingDiv = Html::el('div')
			->class('form-floating')
			->addHtml($input . $label . $validationFeedBack);

		return Html::el('div')
			->class($wrapClass)
			->addHtml($floatingDiv);
	}
}

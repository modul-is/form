<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait RenderFloating
{
	protected ?bool $renderFloating = null;


	public function setRenderFloating(bool $renderFloating = true): self
	{
		$this->renderFloating = $renderFloating;

		return $this;
	}


	public function getRenderFloating(): ?bool
	{
		return $this->renderFloating;
	}


	public function renderFloating(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'mb-3 col-12';
		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$input = $this->getControl();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';
		$inputClass = $this->controlClass . $currentClass . $validationClass;

		$input->class($inputClass);
		$input->placeholder($this->getCaption());

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$label = $this->getCoreLabel();

		$floatingDiv = Html::el('div')
			->class('form-floating')
			->addHtml($input . $label . $validationFeedBack);

		$inputGroup = Html::el('div')
			->class('input-group')
			->addHtml($this->getPrepend() . $floatingDiv . $this->getAppend());

		return Html::el('div')
			->class($wrapClass)
			->addHtml($inputGroup);
	}
}

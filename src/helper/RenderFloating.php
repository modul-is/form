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

		$prepend = null;
		$append = null;
		$floatingClass = 'form-floating';
		$inputClass = $this->controlClass . $currentClass . $validationClass;

		if($this->getPrepend() || $this->getAppend())
		{
			$wrapClass .= ' input-group';
			$floatingClass .= ' flex-grow-1';
			$inputClass .= ' rounded-0';

			$prepend = $this->getPrepend();
			$append = $this->getAppend();

			if($prepend && !$append)
			{
				$inputClass .= ' rounded-end';
			}
			elseif($append && !$prepend)
			{
				$inputClass .= ' rounded-start';
			}
		}

		$input->class($inputClass);
		$input->placeholder($this->getCaption());
		
		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$label = $this->getCoreLabel();

		$floatingDiv = Html::el('div')
			->class($floatingClass)
			->addHtml($input . $label . $validationFeedBack);

		return Html::el('div')
			->class($wrapClass)
			->addHtml($prepend . $floatingDiv . $append);
	}
}

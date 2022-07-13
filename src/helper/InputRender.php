<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait InputRender
{
	public string $controlClass = 'form-control';


	public function getCoreControl()
	{
		$input = $this->getControl();

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

		$input->addAttributes(['class' => $this->controlClass . $currentClass . $validationClass]);

		$signalTooltip = null;

		if(in_array(\ModulIS\Form\Helper\Signals::class, class_uses($this)) && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
			$signalTooltip = $this->getSignalTooltip();
		}

		$hasValidationClass = $this->getValidationClass() && $this->hasErrors() ? ' has-validation' : null;

		return Html::el('div')
			->class('input-group' . $hasValidationClass)
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $signalTooltip . $validationFeedBack);
	}
	
	
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
}

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

		if($this->hasSignal())
		{
			$this->addSignalsToInput($input);
			$signalTooltip = $this->getSignalTooltip();
		}

		$hasValidationClass = $this->getValidationClass() && $this->hasErrors() ? ' has-validation' : null;

		return Html::el('div')
			->class('input-group' . $hasValidationClass)
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $signalTooltip . $validationFeedBack);
	}
}

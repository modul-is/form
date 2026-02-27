<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait InputCoreControl
{
	public function getCoreControl()
	{
		$input = $this->getControl();

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

		$input->addAttributes(['class' => $this->controlClass . $currentClass . $validationClass]);

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$hasValidationClass = $this->getValidationClass() && $this->hasErrors() ? ' has-validation' : null;

		$quickCopyHtml = method_exists($this, 'getQuickCopy') && method_exists($this, 'getQuickCopyButton')
			&& $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		return Html::el('div')
			->class('input-group' . $hasValidationClass)
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $quickCopyHtml . $validationFeedBack);
	}
}

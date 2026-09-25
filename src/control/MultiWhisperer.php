<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class MultiWhisperer extends MultiSelectBox
{
	public function getControl(): Html
	{
		$control = parent::getControl();

		if($control->getAttribute('data-placeholder'))
		{
			$control->setAttribute('data-placeholder', $this->translate($control->getAttribute('data-placeholder')));
		}

		return $control;
	}


	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$validationClass = $this->getValidationClass();
		$validationFeedBack = $this->getValidationFeedback();

		$chosenClass = $this->isRequired() ? ' form-control-chosen-required' : ' form-control-chosen';

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . ($validationClass ? ' ' . $validationClass : null) . $chosenClass]);

		if($this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		return Html::el('div')->class('input-group')
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $validationFeedBack);
	}
}

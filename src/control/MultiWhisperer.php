<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class MultiWhisperer extends MultiSelectBox
{
	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$validationClass = $this->getValidationClass();
		$validationFeedBack = $this->getValidationFeedback();

		$chosenClass = $this->isRequired() ? ' form-control-chosen-required' : ' form-control-chosen';

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . ($validationClass ? ' ' . $validationClass : null) . $chosenClass]);

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		return Html::el('div')->class('input-group')
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $validationFeedBack);
	}


	public function validate(): void
	{
		parent::validate();

		foreach($this->getRules() as $rule)
		{
			if($rule->validator == \ModulIS\Form\Form::Filled && !$this->getValue())
			{
				$this->addError(\Nette\Forms\Validator::formatMessage($rule, true), false);
			}
		}
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class MultiWhisperer extends MultiSelectBox
{
	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$validationClass = $this->getValdiationClass();
		$validationFeedBack = $this->getValidationFeedback();

		$chosenClass = $this->isRequired() ? ' form-control-chosen-required' : ' form-control-chosen';

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . ($validationClass ? ' ' . $validationClass : null) . $chosenClass]);

		$prepend = null;
		$append = null;

		if(!empty($this->prepend))
		{
			$prepend = Html::el('span')
				->class('input-group-text')
				->addHtml($this->prepend);
		}

		if(!empty($this->append))
		{
			$append = Html::el('span')
				->class('input-group-text')
				->addHtml($this->append);
		}

		return Html::el('div')->class('input-group')
			->addHtml($prepend . $input . $append . $validationFeedBack);
	}


	public function validate(): void
	{
		parent::validate();

		foreach($this->getRules() as $rule)
		{
			if($rule->validator == \ModulIS\Form\Form::FILLED && !$this->getValue())
			{
				$this->addError(\Nette\Forms\Validator::formatMessage($rule, true), false);
			}
		}
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class MultiWhisperer extends MultiSelectBox
{
	public function getCoreControl()
	{
		$input = $this->getControl();

		$errorClass = '';
		$errorMessage = null;

		if($this->hasErrors())
		{
			$errorClass = ' is-invalid';

			$errorMessage = Html::el('div')
				->class('invalid-feedback')
				->addHtml($this->getError());
		}

		$chosenClass = $this->isRequired() ? ' form-control-chosen-required' : ' form-control-chosen';

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . $errorClass . $chosenClass]);

		$prepend = null;
		$append = null;

		if(!empty($this->prepend))
		{
			$prependText = Html::el('span')
				->class('input-group-text')
				->addHtml($this->prepend);

			$prepend = Html::el('div')
				->class('input-group-prepend')
				->addHtml($prependText);
		}

		if(!empty($this->append))
		{
			$appendText = Html::el('span')
				->class('input-group-text')
				->addHtml($this->append);

			$append = Html::el('div')
				->class('input-group-append')
				->addHtml($appendText);
		}

		return Html::el('div')->class('input-group')
			->addHtml($prepend . $input . $append . $errorMessage);
	}


	public function validate(): void
	{
		parent::validate();

		foreach($this->getRules() as $rule)
		{
			if($rule->validator == Form::FILLED && !$this->getValue())
			{
				$this->addError(\Nette\Forms\Validator::formatMessage($rule, true), false);
			}
		}
	}
}

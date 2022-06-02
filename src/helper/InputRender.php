<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait InputRender
{
	public function getCoreControl()
	{
		$input = $this->getControl();

		$validationClass = null;
		$validationFeedBack = null;

		if($this->hasErrors())
		{
			$validationClass = 'is-invalid';

			$validationFeedBack = Html::el('div')
				->class('invalid-feedback')
				->addHtml($this->getError());
		}
		else
		{
			$validationClass = 'is-valid';
			
			if($this->getValidationSuccessMessage())
			{
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . ' ' . $validationClass]);

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
			->addHtml($prepend . $input . $append . $validationFeedBack);
	}
}

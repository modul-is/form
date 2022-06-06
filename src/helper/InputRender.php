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

		if($this->getForm()->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationClass = 'is-invalid';

				$validationFeedBack = Html::el('div')
					->class('invalid-feedback')
					->addHtml($this->getError());
			}
			elseif($this->isRequired())
			{
				$validationClass = 'is-valid';

				if($this->getValidationSuccessMessage())
				{
					$validationFeedBack = Html::el('div')
						->class('valid-feedback')
						->addHtml($this->getValidationSuccessMessage());
				}
			}
		}

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . ' ' . $validationClass]);

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
}

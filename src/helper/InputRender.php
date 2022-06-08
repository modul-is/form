<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;
use Kravcik\Macros\FontAwesomeMacro;

trait InputRender
{
	public string $controlClass = 'form-control';
	
	
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

		$input->addAttributes(['class' => $this->controlClass. ' ' . $input->getAttribute('class') . ' ' . $validationClass]);
		
		if(!empty($this->onFocusOut))
		{
			/** @var \Nette\Application\UI\Presenter $presenter */
			$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);
			
			$input->setAttribute('data-on-focusout', $presenter->link($this->lookupPath('Nette\Application\UI\Presenter') . '-focusout!'));
		}

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
		
		$focusOutTooltip = null;
		
		if(!empty($this->onFocusOut))
		{
			$waiting = Html::el('span')
				->class('input-group-text focusout-waiting')
				->addHtml(FontAwesomeMacro::renderIcon('arrow-right-to-bracket'));
			
			$loading = Html::el('span')
				->class('input-group-text focusout-loading')
				->style('display', 'none')
				->addHtml(FontAwesomeMacro::renderIcon('spinner fa-spin'));
			
			$success = Html::el('span')
				->class('input-group-text focusout-success')
				->title('')
				->style('display', 'none')
				->addHtml(FontAwesomeMacro::renderIcon('check', ['color' => 'green']));
			
			$error = Html::el('span')
				->class('input-group-text focusout-error')
				->title('')
				->style('display', 'none')
				->addHtml(FontAwesomeMacro::renderIcon('times', ['color' => 'red']));
			
			$focusOutTooltip = $waiting . $loading . $success . $error;
		}

		return Html::el('div')->class('input-group')
			->addHtml($prepend . $input . $append . $focusOutTooltip . $validationFeedBack);
	}
}

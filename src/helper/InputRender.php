<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;
use Kravcik\LatteFontAwesomeIcon\Extension;

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

		if(!empty($this->onFocusOut))
		{
			/** @var \Nette\Application\UI\Presenter $presenter */
			$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);

			$input->setAttribute('data-on-focusout', $presenter->link($this->lookupPath('Nette\Application\UI\Presenter') . '-focusout!'));
		}

		$focusOutTooltip = null;

		if(!empty($this->onFocusOut))
		{
			$waiting = Html::el('span')
				->class('input-group-text focusout-waiting')
				->addHtml(Extension::render('arrow-right-to-bracket'));

			$loading = Html::el('span')
				->class('input-group-text focusout-loading')
				->style('display', 'none')
				->addHtml(Extension::render('spinner fa-spin'));

			$success = Html::el('span')
				->class('input-group-text focusout-success')
				->title('')
				->style('display', 'none')
				->addHtml(Extension::render('check', color: 'green'));

			$error = Html::el('span')
				->class('input-group-text focusout-error')
				->title('')
				->style('display', 'none')
				->addHtml(Extension::render('times', color: 'red'));

			$focusOutTooltip = $waiting . $loading . $success . $error;
		}
		
		$hasValidationClass = $this->getValidationClass() && $this->hasErrors() ? ' has-validation' : null;

		return Html::el('div')
			->class('input-group' . $hasValidationClass)
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $focusOutTooltip . $validationFeedBack);
	}
}

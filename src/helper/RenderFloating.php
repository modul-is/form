<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Control\HasInputGroup;
use Nette\Utils\Html;

trait RenderFloating
{
	public function renderFloating(): Html
	{
		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$input = $this->getControl();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';
		$inputClass = $this->controlClass . $currentClass . $validationClass;

		$input->class($inputClass);
		/**
		 * Floating label potrebuje neprazdny placeholder, aby se aplikovalo :placeholder-shown -
		 * u <select> ale placeholder neni platny atribut a BS5 ho nepotrebuje.
		 */
		if($input->getName() !== 'select')
		{
			$input->placeholder($this->translate($this->getCaption()));
		}

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$label = $this->getCoreLabel();

		$floatingDiv = Html::el('div')
			->class('form-floating')
			->addHtml($input . $label . $validationFeedBack);

		$quickCopyHtml = $this instanceof QuickCopyable && $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		$inputGroup = Html::el('div')
			->class('input-group');

		if($this instanceof HasInputGroup && $this->getPrepend())
		{
			$inputGroup->addHtml($this->getPrepend());
		}

		$inputGroup->addHtml($floatingDiv);

		if($this instanceof HasInputGroup && $this->getAppend())
		{
			$inputGroup->addHtml($this->getAppend());
		}

		if($quickCopyHtml)
		{
			$inputGroup->addHtml($quickCopyHtml);
		}

		return $this->createWrap()
			->addHtml($inputGroup);
	}
}

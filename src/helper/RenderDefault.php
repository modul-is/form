<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Enum\RenderType;
use Nette\Utils\Html;

trait RenderDefault
{
	public function renderDefault(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'field';

		$validationFeedBack = $this->getValidationFeedback();

		$input = $this->getControl();

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

		$inputClass = $currentClass . $validationClass . ' new-design-input form-control ' . $this->getInputWrapClass();

		if($inputClass)
		{
			$input->appendAttribute('class', ltrim($inputClass));
		}

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$required = $this->isRequired()
			? ' ' . Html::el('span')->class('required')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->class($this->getLabelWrapClass())
			->for($this->getHtmlId())
			->addHtml($this->getCaption() . $required);

		$quickCopyHtml = $this instanceof QuickCopyable && $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		$group = Html::el('div')
			->class('input-group');

		if($this->getPrepend())
		{
			$group->addHtml($this->getPrepend());
		}

		$group->addHtml($input);

		if($this->getAppend())
		{
			$group->addHtml($this->getAppend());
		}

		$group->addHtml($validationFeedBack);

		$fieldDiv = Html::el('div')
			->class($wrapClass . ' new-design-label')
			->addHtml($labelEl . $group . $quickCopyHtml);

		return $fieldDiv;
	}
}

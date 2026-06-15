<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait RenderFloating
{
	use RenderFloatingState;


	public function renderFloating(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'field';

		$validationFeedBack = $this->getValidationFeedback();

		$input = $this->getControl();

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';
		$inputClass = $currentClass . $validationClass;

		if($inputClass)
		{
			$input->class(ltrim($inputClass));
		}

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$required = $this->isRequired()
			? ' ' . Html::el('span')->style('color:var(--red)')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->style('font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gray-600)')
			->addHtml($this->getCaption() . $required);

		$quickCopyHtml = $this instanceof QuickCopyable && $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		$fieldDiv = Html::el('div')
			->style('display:flex;flex-direction:column;gap:5px;min-width:0')
			->class($wrapClass)
			->addHtml($labelEl . $input . $validationFeedBack . $quickCopyHtml);

		return $fieldDiv;
	}
}

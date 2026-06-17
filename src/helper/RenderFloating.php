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

		$inputClass = $currentClass . $validationClass . ' new-design-input';

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
			->for($this->getHtmlId())
			->addHtml($this->getCaption() . $required);

		$quickCopyHtml = $this instanceof QuickCopyable && $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		$fieldDiv = Html::el('div')
			->class($wrapClass . ' new-design-label')
			->addHtml($labelEl . $input . $validationFeedBack . $quickCopyHtml);

		return $fieldDiv;
	}
}

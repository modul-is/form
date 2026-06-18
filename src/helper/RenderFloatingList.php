<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Control\Signalable;
use Nette\Utils\Html;

trait RenderFloatingList
{
	use RenderTypeState;


	public function renderFloating(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'field';

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$required = $this->isRequired()
			? ' ' . Html::el('span')->style('required')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->style('font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gray-600)')
			->addHtml($this->getCaption() . $required);

		$checkSvg = '<svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';

		$itemsWrap = Html::el('div')
			->class('new-design-checkbox-wrap');

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this instanceof Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

			$boxSpan = Html::el('span')
				->class('check')
				->addHtml($checkSvg);

			$spanLabel = Html::el('span')
				->class('new-design-checkbox-item-label')
				->addHtml($itemLabel);

			$itemLabelEl = Html::el('label')
				->for($inputEl->getAttribute('id'))
				->class('new-design-checkbox-item')
				->addHtml($inputEl . $boxSpan . $spanLabel);

			$itemsWrap->addHtml($itemLabelEl);
		}

		$validationFeedBack = $this->getValidationFeedback();

		return Html::el('div')
			->style('display:flex;flex-direction:column;gap:5px;min-width:0')
			->class($wrapClass)
			->addHtml($labelEl . $itemsWrap . $validationFeedBack);
	}
}

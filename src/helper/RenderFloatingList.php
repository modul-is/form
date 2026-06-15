<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Control\Signalable;
use Nette\Utils\Html;

trait RenderFloatingList
{
	use RenderFloatingState;


	public function renderFloating(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'field';

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$required = $this->isRequired()
			? ' ' . Html::el('span')->style('color:var(--red)')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->style('font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gray-600)')
			->addHtml($this->getCaption() . $required);

		$checkSvg = '<svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>';

		$itemsWrap = Html::el('div')
			->style('display:flex;gap:14px;margin-top:4px;flex-wrap:wrap');

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this instanceof Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

			$inputEl->style('position:absolute;opacity:0;pointer-events:none');

			$boxSpan = Html::el('span')
				->style('width:18px;height:18px;border:1.5px solid var(--gray-200);border-radius:4px;background:var(--white);display:flex;align-items:center;justify-content:center;transition:all .15s;flex-shrink:0')
				->addHtml($checkSvg);

			$itemLabelEl = Html::el('label')
				->for($inputEl->getAttribute('id'))
				->style('display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;color:var(--gray-600)')
				->addHtml($inputEl . $boxSpan . $itemLabel);

			$itemsWrap->addHtml($itemLabelEl);
		}

		$validationFeedBack = $this->getValidationFeedback();

		return Html::el('div')
			->style('display:flex;flex-direction:column;gap:5px;min-width:0')
			->class($wrapClass)
			->addHtml($labelEl . $itemsWrap . $validationFeedBack);
	}
}

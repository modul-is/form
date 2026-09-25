<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait RenderDefault
{
	public function renderDefault(): Html
	{
		$validationFeedBack = $this->getValidationFeedback();

		$input = $this->getControl();

		/**
		 * Trida se pripojuje po castech - getControl() uz muze nejakou nest (napr. form-control-chosen)
		 * a slozeni jednoho retezce z $input->getAttribute('class') by ji zduplikovalo.
		 *
		 * $controlClass je form-control / form-select / form-control-chosen podle typu prvku -
		 * u <select> je form-select nutny, aby mel sipku (.mis-input ma appearance: none).
		 */
		foreach(['mis-input', $this->controlClass, $this->getValidationClass(), $this->getInputWrapClass()] as $class)
		{
			if($class)
			{
				$input->appendAttribute('class', $class);
			}
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
			->addText($this->translate($this->getCaption()))
			->addHtml($required);

		/**
		 * Tento typ vykresleni si label sklada sam, ne pres getCoreLabel(), proto se tooltip
		 * pripoji tady - vsechny kontrolky pouzivajici RenderDefault maji trait Tooltip.
		 */
		if($tooltipHtml = $this->getTooltipHtml())
		{
			$labelEl->addHtml(' ' . $tooltipHtml);
		}

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

		if($quickCopyHtml)
		{
			$group->addHtml($quickCopyHtml);
		}

		return $this->createWrap('mis-field')
			->addHtml($labelEl . $group);
	}
}

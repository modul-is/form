<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Control\HasInputGroup;
use Nette\Utils\Html;

trait RenderInline
{
	use ClassList;

	public function renderInline(): Html
	{
		$label = $this->getCoreLabel();
		$input = $this->getControl();

		$input->appendAttribute('class', $this->controlClass);

		if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$quickCopyHtml = $this instanceof QuickCopyable && $this->getQuickCopy()
			? $this->getQuickCopyButton()
			: null;

		$validationClass = $this->getValidationClass();
		$validationFeedBack = $this->getValidationFeedback();

		if($validationClass)
		{
			$input->appendAttribute('class', $validationClass);
		}

		$inputControl = Html::el('div')
			->class($this->joinClass('input-group', $validationClass));

		if($this instanceof HasInputGroup && $this->getPrepend())
		{
			$inputControl->addHtml($this->getPrepend());
		}

		$inputControl->addHtml($input);

		if($this instanceof HasInputGroup && $this->getAppend())
		{
			$inputControl->addHtml($this->getAppend());
		}

		if($quickCopyHtml)
		{
			$inputControl->addHtml($quickCopyHtml);
		}

		$labelDiv = Html::el('div')
			->class('mis-field-inline-label')
			->addHtml($label);

		$inputDiv = Html::el('div')
			->class('mis-field-inline-control')
			->addHtml($inputControl)
			->addHtml($validationFeedBack);

		return $this->createWrap('mis-field-inline')
			->addHtml($labelDiv)
			->addHtml($inputDiv);
	}
}

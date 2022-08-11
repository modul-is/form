<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait WrapControl
{
	protected ?string $labelClass = null;

	protected ?string $inputClass = null;

	protected ?\Nette\Utils\Html $wrapControl = null;


	public function setLabelWrapClass(string $class): self
	{
		$this->labelClass = $class;

		return $this;
	}


	public function setInputWrapClass(string $class): self
	{
		$this->inputClass = $class;

		return $this;
	}


	public function setWrapClass(string $class): self
	{
		$this->getWrapControl()
			->setAttribute('class', $class);

		return $this;
	}


	public function setWrapId(string $id): self
	{
		$this->getWrapControl()
			->setAttribute('id', $id);

		return $this;
	}


	public function getWrapControl(): Html
	{
		if(!$this->wrapControl)
		{
			/** @var \ModulIS\Form\Form $form */
			$form = $this->getForm();

			$this->wrapControl = Html::el('div')
				->class($form->getDefaultInputWrapClass());
		}

		return $this->wrapControl;
	}


	public function renderWrap(): Html
	{
		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		$label = $this->getCoreLabel();
		$input = $this->getCoreControl();

		$inputClass = 'align-self-center';
		$labelClass = 'align-self-center';

		if($this->getRenderInline() ?? $form->getRenderInline())
		{
			$inputClass .= $this->inputClass ? ' ' . $this->inputClass : ' col-sm-12';
			$labelClass .= $this->labelClass ? ' ' . $this->labelClass : ' col-sm-12';
		}
		else
		{
			$inputClass .= $this->inputClass ? ' ' . $this->inputClass : ' col-sm-8';
			$labelClass .= $this->labelClass ? ' ' . $this->labelClass : ' col-sm-4';
		}

		$labelDiv = Html::el('div')
			->class($labelClass)
			->addHtml($label);

		$inputDiv = Html::el('div')
			->class($inputClass)
			->addHtml($input);

		$rowDiv = Html::el('div')
			->class('row')
			->addHtml($labelDiv . $inputDiv);

		return $this->getWrapControl()
			->addHtml($rowDiv);
	}
}
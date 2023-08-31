<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait WrapControl
{
	protected ?string $labelClass = null;

	protected ?string $inputClass = null;

	protected ?string $rowClass = null;

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


	public function setRowClass(string $class): self
	{
		$this->rowClass = $class;

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
			$form = $this->getForm();
			\assert($form instanceof \ModulIS\Form\Form);

			$this->wrapControl = Html::el('div')
				->class($form->getDefaultInputWrapClass());
		}

		return $this->wrapControl;
	}


	public function renderWrap(): Html
	{
		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

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

		$rowClass = $this->rowClass ?? 'row';

		$rowDiv = Html::el('div')
			->class($rowClass)
			->addHtml($labelDiv . $inputDiv);

		return $this->getWrapControl()
			->addHtml($rowDiv);
	}
}

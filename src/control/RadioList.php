<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class RadioList extends \Nette\Forms\Controls\RadioList implements Renderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\CoreList;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\RenderBasic
	{
		render as public baseRender;
	}
	use Helper\Signals;
	use Helper\ToggleButton;

	private bool $big = false;


	public function setBig(bool $big = true): self
	{
		$this->big = $big;

		return $this;
	}


	/**
	 * @param class-string<BackedEnum&\ModulIS\Form\Enum\RadioEnum> $enumClass
	 */
	public function setValuesFromEnum(string $enumClass): self
	{
		$this->setItems($enumClass::getList());

		$this->setTooltips($enumClass::getDescription());

		return $this;
	}


	public function render(): Html|string
	{
		if(!$this->big)
		{
			return $this->baseRender();
		}

		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		$wrap = Html::el('div')
			->class('btn-group row w-100');

		foreach($this->getItems() as $case => $caseString)
		{
			if(isset($this->tooltips[$case]))
			{
				$tooltip = Html::el('p')
					->class('mb-0 text-muted')
					->addText($this->tooltips[$case]);
			}
			else
			{
				$tooltip = null;
			}

			$input = $this->getControlPart($case);

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : null;

			$input->class('btn-check z-1 top-50 start-0 ms-4 round-16 position-relative' . $currentClass);

			$labelString = Html::el('h6')
				->class('fs-4 fw-semibold mb-0')
				->addText($caseString);

			$labelStringWrap = Html::el('div')
				->class('text-start ps-2')
				->addHtml($labelString)
				->addHtml($tooltip);

			$color = $this->color ?: 'primary';

			$label = Html::el('label')
				->class("btn btn-outline-$color mb-0 p-3 rounded ps-5 w-100")
				->for($input->id)
				->addHtml($labelStringWrap);

			$inputLabelWrap = Html::el('div')
				->class('position-relative col-lg-' . 12 / $this->itemsPerRow . ' ' . $this->itemClass)
				->addHtml($input)
				->addHtml($label);

			$wrap->addHtml($inputLabelWrap);
		}

		$validationFeedBack = '';
		$validationClass = '';
		$cardClass = '';

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		if($form->isAnchored() && $form->isSubmitted())
		{
			if($this->hasErrors())
			{
				$cardClass = ' border-danger';
				$validationClass = ' is-invalid';
				$validationFeedBack = Html::el('div')
					->class('invalid-feedback')
					->addHtml($this->getError());
			}
			elseif($this->getValidationSuccessMessage())
			{
				$cardClass = ' border-success';
				$validationClass = ' is-valid';
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		$mainLabel = Html::el('h6')
			->class('mb-3 fw-semibold fs-4' . $validationClass)
			->addHtml($this->getLabel());

		$cardBody = Html::el('div')
			->class('card-body p-4')
			->addHtml($mainLabel)
			->addHtml($wrap)
			->addHtml($validationFeedBack);

		return Html::el('div')
			->class('btn-group-active card shadow-none border' . $cardClass . $this->inputClass)
			->addHtml($cardBody);
	}
}
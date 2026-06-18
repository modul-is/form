<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Enum\RenderType;
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
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\ToggleButton;
	use Helper\RenderFloatingList;
	use Helper\RenderInline;
	use Helper\Render
	{
		render as public baseRender;
	}

	private array $iconArray = [];

	private bool $big = false;


	public function setIconArray(array $iconArray): self
	{
		$this->iconArray = $iconArray;

		return $this;
	}


	public function setBig(bool $big = true): self
	{
		$this->big = $big;

		return $this;
	}


	public function setItemsColor(string $color): self
	{
		$this->color = $color;

		return $this;
	}


	/**
	 * @param class-string<\BackedEnum&\ModulIS\Form\Enum\RadioEnum> $enumClass
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
			$form = $this->getForm();
			\assert($form instanceof \ModulIS\Form\Form);

			$effectiveRenderType = $this->getRenderType() ?: $form->getRenderType();

			return match($effectiveRenderType)
			{
				RenderType::Inline, RenderType::Floating => $this->renderInline(),
				RenderType::Default => $this->renderDefault()
			};
		}

 		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$validationFeedBack = '';
		$validationClass = '';

		if($form->isAnchored() && $form->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationClass = ' is-invalid';
				$validationFeedBack = Html::el('div')
					->class('invalid-feedback')
					->addHtml($this->getError());
			}
			elseif($this->getValidationSuccessMessage())
			{
				$validationClass = ' is-valid';
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		$checkHtml = \Kravcik\LatteFontAwesomeIcon\Extension::render('circle');

		$tilesWrap = Html::el('div')
			->class('new-design-checkbox-big-tiles');

		foreach($this->getItems() as $key => $itemLabel)
		{
			$input = $this->getControlPart($key);

			if($this instanceof Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($input);
			}

			if(isset($this->iconArray[$key]))
			{
				$icoHtml = Html::el('span')
					->class('new-design-checkbox-big-ico')
					->addText(\Kravcik\LatteFontAwesomeIcon\Extension::render($this->iconArray[$key]));
			}
			else
			{
				$icoHtml = '';
			}

			if(isset($this->tooltips[$key]))
			{
				$desc = Html::el('span')
					->class('new-design-checkbox-big-desc')
					->addText($this->tooltips[$key]);
			}
			else
			{
				$desc = '';
			}

			$lbl = Html::el('span')
				->class('new-design-checkbox-big-lbl')
				->addText($itemLabel);

			$chk = Html::el('span')
				->class('new-design-checkbox-big-chk')
				->addHtml($checkHtml);

			$tileLabel = Html::el('label')
				->class('new-design-checkbox-big-tile radio');

			$tileLabel->addHtml($input)
				->addHtml($icoHtml)
				->addHtml($lbl)
				->addHtml($desc)
				->addHtml($chk);

			$tilesWrap->addHtml($tileLabel);
		}

		$label = $this->getLabel()->addAttributes(['class' => $this->isRequired() ? 'required' : '']);

		$blockTitle = Html::el('div')
			->class('new-design-checkbox-big-block-title' . $validationClass)
			->addHtml($label);

		$tooltip = $this->getTooltip() === null ? '' : Html::el('div')
			->class('new-design-checkbox-big-block-sub')
			->addHtml($this->getTooltip());

		$blockHead = Html::el('div')
			->class('new-design-checkbox-big-block-head')
			->addHtml($blockTitle)
			->addHtml($tooltip);

		$block = Html::el('div')
			->id($this->getOption('id') ?: null)
			->class('new-design-checkbox-big-block' . $this->inputClass)
			->addHtml($blockHead)
			->addHtml($tilesWrap)
			->addHtml($validationFeedBack);

		return $block;
	}


	public function renderDefault(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'field';

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$required = $this->isRequired()
			? ' ' . Html::el('span')->style('required')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->addHtml($this->getCaption() . $required);

		$itemsWrap = Html::el('div')
			->class('new-design-radio-input-wrap');

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this instanceof Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

			$inputEl->style('position:absolute;opacity:0;pointer-events:none');

			$itemLabelEl = Html::el('label')
				->for($inputEl->getAttribute('id'))
				->class('new-design-radio-label')
				->addHtml($inputEl . $itemLabel);

			$itemsWrap->addHtml($itemLabelEl);
		}

		$validationFeedBack = $this->getValidationFeedback();

		return Html::el('div')
			->id($this->getOption('id') ?: null)
			->class('new-design-radio-wrap ' . $wrapClass)
			->addHtml($labelEl . $itemsWrap . $validationFeedBack);
	}
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Enum\RenderType;
use ModulIS\Form\Helper;
use Nette\Utils\Html;

class CheckboxList extends \Nette\Forms\Controls\CheckboxList implements Renderable, Signalable, \Nette\Application\UI\SignalReceiver
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
	use Helper\Render
	{
		render as public baseRender;
	}
	use Helper\RenderInline;

	private array $iconArray = [];

	private bool $big = false;


	public function renderDefault(): Html
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return Html::el();
		}

		$required = $this->isRequired()
			? ' ' . Html::el('span')->class('required')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->addHtml($this->getCaption() . $required);

		$itemsWrap = Html::el('div')
			->class('checkbox-new-row-item-wrap');

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this instanceof Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

			$boxSpan = Html::el('span')
				->class('box')
				->addHtml(Extension::render('check', 'white'));

			$itemLabelEl = Html::el('label')
				->class('checkbox')
				->addHtml($inputEl)
				->addHtml($boxSpan)
				->addHtml($itemLabel);

			$itemsWrap->addHtml($itemLabelEl);
		}

		$validationFeedBack = $this->getValidationFeedback();

		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'col-12 mb-2';

		return Html::el('div')
			->id($this->getOption('id') ?: null)
			->class($wrapClass . ' checkbox-new-row')
			->addHtml($labelEl . $itemsWrap . $validationFeedBack);
	}


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

		$checkSvg = Extension::render('check-circle');

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
					->addText(Extension::render($this->iconArray[$key]));
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
				->addHtml($checkSvg);

			$tileLabel = Html::el('label')
				->class('new-design-checkbox-big-tile');

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
			->addHtml($blockTitle . $tooltip);

		$block = Html::el('div')
			->id($this->getOption('id') ?: null)
			->class('new-design-checkbox-big-block' . $this->inputClass)
			->addHtml($blockHead)
			->addHtml($tilesWrap)
			->addHtml($validationFeedBack);

		return $block;
	}
}

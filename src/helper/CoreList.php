<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Control\CheckboxList;
use ModulIS\Form\Control\RadioList;
use ModulIS\Form\Control\Signalable;
use ModulIS\Form\Enum\RenderListType;
use ModulIS\Form\Enum\RenderType;
use ModulIS\Form\Form;
use Nette\Utils\Html;
use function assert;

trait CoreList
{
	protected array $tooltips = [];

	protected int $itemsPerRow = 1;

	protected ?string $itemClass = null;

	protected array $wrapRowAttributes = [];

	protected ?RenderListType $renderType = null;


	public function setRenderType(RenderListType $renderType): static
	{
		$this->renderType = $renderType;

		return $this;
	}


	public function getRenderType(): ?RenderListType
	{
		return $this->renderType;
	}


	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->templatePath)
		{
			$path = $this->templatePath;

			$this->setTemplate(null, $this->templateParams, $this->templateEngine);

			$template = $this->templateEngine ?: new \Latte\Engine;

			return $template->renderToString($path, array_merge(['input' => $this], $this->templateParams));
		}

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$inputRenderType = $this->getRenderType();

		if(!$inputRenderType)
		{
			$inputRenderType = match($form->getRenderType())
			{
				RenderType::Inline, RenderType::Floating => RenderListType::Inline,
				RenderType::Default => RenderListType::Default
			};
		}

		$outerDiv = match($inputRenderType)
		{
			RenderListType::Inline, RenderListType::Floating => $this->renderInlineList(),
			RenderListType::Big => $this->renderBig(),
			RenderListType::Compact => $this->renderCompact(),
			RenderListType::Default => $this->renderDefault()
		};

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}


	public function renderCompact(): Html|string
	{
		$validationFeedBack = $this->getValidationFeedback();
		$validationClass = $this->getvalidationClass();

		$required = $this->isRequired()
			? ' ' . Html::el('span')->class('required')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->class('new-design-compact-label ' . $this->getLabelWrapClass())
			->addHtml($this->getCaption() . $required);

		$itemsWrapField = Html::el('div')
			->class('new-design-compact-input-field ' . $this->getInputWrapClass() . ' ' . $validationClass);

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

			$itemLabelEl = Html::el('label')
				->for($inputEl->getAttribute('id'))
				->addHtml($inputEl . $itemLabel);

			$itemsWrapField->addHtml($itemLabelEl);
		}

		$itemsWrap = Html::el('div')
			->class('new-design-compact-input-wrap')
			->addHtml($itemsWrapField);

		if($validationFeedBack)
		{
			$itemsWrap->addHtml($validationFeedBack);
		}

		return Html::el('div')
			->id($this->getOption('id') ?: null)
			->class('new-design-compact ' . ($this->getWrapControl()->getAttribute('class') ?: ''))
			->addHtml($labelEl . $itemsWrap);
	}


	public function renderBig(): Html|string
	{
		$validationFeedBack = $this->getValidationFeedback();
		$validationClass = $this->getvalidationClass();

		if($this instanceof CheckboxList)
		{
			$polyline = Html::el('polyline')
				->setAttribute('points', '20 6 9 17 4 12');

			$checkHtml = Html::el('svg')
				->setAttribute('viewBox', '0 0 24 24')
				->setAttribute('fill', 'none')
				->setAttribute('stroke-linecap', 'round')
				->addHtml($polyline);
		}
		else
		{
			$checkHtml = null;
		}

		$tilesWrap = Html::el('div')
			->class('new-design-checkbox-big-tiles');

		foreach($this->getItems() as $key => $itemLabel)
		{
			$input = $this->getControlPart($key);

			if($this->hasSignal())
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
				->class('new-design-checkbox-big-chk');

			if($checkHtml)
			{
				$chk->addHtml($checkHtml);
			}

			$tileLabel = Html::el('label')
				->class('new-design-checkbox-big-tile' . ($this instanceof RadioList ? ' radio' : ''));

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


	public function getCoreControl(): Html|string
	{
		$inputs = null;

		$form = $this->getForm();
		assert($form instanceof Form);

		foreach($this->getItems() as $key => $input)
		{
			$htmlInput = $this->renderItem($key);

			$inputs .= $htmlInput;
		}

		$validationFeedBack = null;
		$validationClass = null;

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

		$wrapRow = Html::el('div')
			->addAttributes($this->wrapRowAttributes)
			->class('row');

		if($inputs)
		{
			$wrapRow->addHtml($inputs);
		}

		$wrapContainer = Html::el('div')
			->class('container' . $validationClass)
			->addHtml($wrapRow);

		return $wrapContainer . $validationFeedBack;
	}


	public function renderItem(string|int $itemName)
	{
		$input = $this->getControlPart($itemName);

		$inputColorClass = $this->color ? ' checkbox-' . $this->color : null;

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : null;

		if($this->toggleButton)
		{
			if(is_array($this->buttonColor))
			{
				$buttonColor = $this->buttonColor[$itemName] ?? 'primary';
			}
			else
			{
				$buttonColor = $this->buttonColor;
			}

			$inputClass = 'btn-check';
			$labelClass = 'me-2 btn btn-' . $buttonColor . ' width-toggle';
		}
		else
		{
			$inputClass = 'form-check-input';
			$labelClass = 'form-check-label width-auto';
		}

		$input->class($inputClass . $currentClass . $inputColorClass);

		$label = $this->getLabelPart($itemName);

		$label->class($labelClass);

		$tooltip = null;

		if(isset($this->tooltips[$itemName]))
		{
			$tooltip = Html::el('span')
				->title($this->tooltips[$itemName])
				->addAttributes(['data-bs-placement' => 'top', 'data-bs-toggle' => 'tooltip', 'data-bs-html' => 'true'])
				->addHtml(Extension::render('question-circle', color: 'blue'));
		}

		$class = 'form-check col-' . (12 / $this->itemsPerRow);

		if($this->itemClass)
		{
			$class .= ' ' . $this->itemClass;
		}

		if($this instanceof Signalable && $this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		return Html::el('div')
			->class(($this->toggleButton ? 'p-0 ' : '') . $class)
			->addHtml($input . $label . $tooltip);
	}


	public function renderInlineList(): Html|string
	{
		$form = $this->getForm();
		assert($form instanceof Form);

		$label = $this->getCoreLabel();
		$input = $this->getCoreControl();

		$labelClass = 'align-self-center' . ($this->labelClass ? ' ' . $this->labelClass : ' col-sm-4');
		$inputClass = 'align-self-center' . ($this->inputClass ? ' ' . $this->inputClass : ' col-sm-8');

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


	public function setTooltips(array $tooltips): static
	{
		$this->tooltips = $tooltips;
		return $this;
	}


	public function setItemsPerRow(int $number): static
	{
		$this->itemsPerRow = $number;
		return $this;
	}


	public function setItemClass(string $itemClass): static
	{
		$this->itemClass = $itemClass;
		return $this;
	}


	public function setWrapAttributes(array $attributes): static
	{
		$this->wrapRowAttributes = $attributes;

		return $this;
	}
}

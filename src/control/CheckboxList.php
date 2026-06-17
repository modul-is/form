<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Helper;
use Nette\Utils\Html;

class CheckboxList extends \Nette\Forms\Controls\CheckboxList implements Renderable, FloatingRenderable, Signalable, \Nette\Application\UI\SignalReceiver
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
	use Helper\RenderFloatingList;

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


	public function render(): Html|string
	{
		if(!$this->big)
		{
			$form = $this->getForm();
			\assert($form instanceof \ModulIS\Form\Form);

			if($this instanceof FloatingRenderable && ($this->getRenderFloating() ?? $form->getRenderFloating()))
			{
				if($this->getOption('hide') || $this->autoRenderSkip)
				{
					return '';
				}

				return $this->renderFloating();
			}

			return $this->baseRender();
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
		$tooltip = $this->getTooltip() === null ? '' : Html::el('span')
			->title($this->getTooltip())
			->addAttributes(['data-bs-placement' => 'top', 'data-bs-toggle' => 'tooltip', 'data-bs-html' => 'true'])
			->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('question-circle', color: 'blue'));

		$blockTitle = Html::el('div')
			->class('new-design-checkbox-big-block-title' . $validationClass)
			->addHtml($label . $tooltip);

		$blockHead = Html::el('div')
			->class('new-design-checkbox-big-block-head')
			->addHtml($blockTitle);

		$block = Html::el('div')
			->id($this->getOption('id') ?: null)
			->class('new-design-checkbox-big-block' . $this->inputClass)
			->addHtml($blockHead)
			->addHtml($tilesWrap)
			->addHtml($validationFeedBack);

		return $block;
	}
}

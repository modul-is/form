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
	use Helper\RenderInline;

	private array $iconArray = [];


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
			->class('checkbox-new-row-item-wrap ' . $this->getValidationClass());

		$polyline = Html::el('polyline')
			->points('20 6 9 17 4 12');

		$svg = Html::el('svg')
			->viewBox('0 0 24 24')
			->fill('none')
			->setAttribute('stroke-linecap', 'round')
			->addHtml($polyline);

		$boxSpan = Html::el('span')
			->class('box')
			->addHtml($svg);

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this instanceof Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

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
}

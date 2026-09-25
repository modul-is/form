<?php

declare(strict_types = 1);

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
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\ToggleButton;
	use Helper\RenderInline;
	use Helper\WrapControl;

	/** @var array<int|string, string> */
	private array $iconArray = [];

	private bool $rounded = false;


	public function setRounded(bool $rounded): static
	{
		$this->rounded = $rounded;

		return $this;
	}


	/**
	 * @param array<int|string, string> $iconArray
	 */
	public function setIconArray(array $iconArray): static
	{
		$this->iconArray = $iconArray;

		return $this;
	}


	public function setItemsColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}


	/**
	 * @param class-string<\BackedEnum&\ModulIS\Form\Enum\RadioEnum> $enumClass
	 */
	public function setValuesFromEnum(string $enumClass): static
	{
		$this->setItems($enumClass::getList());

		$this->setTooltips($enumClass::getDescription());

		return $this;
	}


	public function renderDefault(): Html
	{
		$required = $this->isRequired()
			? ' ' . Html::el('span')->class('required')->setText('*')
			: '';

		$labelEl = Html::el('label')
			->addText($this->translate($this->getCaption()))
			->addHtml($required);

		if($tooltipHtml = $this->getTooltipHtml())
		{
			$labelEl->addHtml(' ' . $tooltipHtml);
		}

		$itemsWrap = Html::el('div')
			->class($this->joinClass('mis-radio-items', $this->getValidationClass()));

		foreach($this->getItems() as $key => $itemLabel)
		{
			$inputEl = $this->getControlPart($key);

			if($this->hasSignal())
			{
				$this->addSignalsToInput($inputEl);
			}

			$inputEl->appendAttribute('class', 'mis-radio-input');

			$itemLabelEl = Html::el('label')
				->for($inputEl->getAttribute('id'))
				->class('mis-radio-label' . ($this->rounded ? ' radio-rounded' : ''))
				->addHtml($inputEl)
				->addText($this->translate($itemLabel));

			$itemsWrap->addHtml($itemLabelEl);
		}

		$validationFeedBack = $this->getValidationFeedback();

		return $this->createWrap('mis-radio')
			->addHtml($labelEl . $itemsWrap . $validationFeedBack);
	}
}

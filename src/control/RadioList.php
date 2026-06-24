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
	use Helper\RenderFloatingList;
	use Helper\RenderInline;

	private array $iconArray = [];

	private bool $rounded = false;


	public function setRounded(bool $rounded): self
	{
		$this->rounded = $rounded;

		return $this;
	}


	public function setIconArray(array $iconArray): self
	{
		$this->iconArray = $iconArray;

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


	public function renderDefault(): Html
	{
		$wrapClass = $this->getWrapControl()->getAttribute('class') ?: 'field';

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		$required = $this->isRequired()
			? ' ' . Html::el('span')->class('required')->setText('*')
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
				->class('new-design-radio-label' . ($this->rounded ? ' radio-rounded' : ''))
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

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class SelectBox extends \Nette\Forms\Controls\SelectBox implements Renderable, FloatingRenderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\RenderFloating;
	use Helper\Validation;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\RenderBasic;

	private array $imageArray = [];


	public function __construct($label = null, ?array $items = null)
	{
		parent::__construct($label, $items);

		$this->controlClass = 'form-select';
	}


	public function setImageArray(array $imageArray): self
	{
		$this->imageArray = $imageArray;

		return $this;
	}


	public function getCoreControl()
	{
		$input = $this->getControl();

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		if($this->imageArray)
		{
			$input->addAttributes(['style' => 'display: none;']);

			if($this->getPrompt())
			{
				$labelPrompt = Html::el('div')
					->class('label-text mt-1')
					->addHtml($this->getPrompt());

				$optionPrompt = Html::el('a')
					->class('dropdown-item')
					->addHtml($labelPrompt);

				$li = Html::el('li')
					->value('')
					->addHtml($optionPrompt);
			}
			else
			{
				$li = null;
			}

			$items = $this->getItems();

			foreach($items as $key => $label)
			{
				$img = Html::el('img')
					->src($this->imageArray[$key]);

				$labelWrap = Html::el('div')
					->class('label-text mt-1 ms-2')
					->addHtml($label);

				$imgWrap = Html::el('div')
					->class('image-wrap')
					->addHtml($img);

				$optionLink = Html::el('a')
					->class('row dropdown-item')
					->addHtml($imgWrap . $labelWrap);

				$li .= Html::el('li')
					->value($key)
					->addHtml($optionLink);
			}

			if($this->getValue())
			{
				$promptValue = $items[$this->getValue()];
			}
			else
			{
				$promptValue = $this->getPrompt() ?: reset($items);
			}

			$span = Html::el('span')
				->class('prompt-text float-start')
				->addHtml($promptValue);

			$button = Html::el('a')
				->class('btn dropdown-toggle d-block' . $validationClass)
				->id($this->getHtmlId() . '-dropdown')
				->addAttributes(['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false'])
				->type('button')
				->addHtml($span);

			$ul = Html::el('ul')
				->addAttributes(['aria-labelledby' => $this->getHtmlId() . '-dropdown'])
				->class('dropdown-menu ps-2 pe-2 w-100')
				->addHtml($li);

			$div = Html::el('div')
				->id($this->getHtmlId() . '-wrapper')
				->addAttributes(['data-parent-id' => $this->getHtmlId()])
				->class('dropdown select-image d-block' . $validationClass)
				->addHtml($button . $ul);

			return $input . $div . $validationFeedBack;
		}
		else
		{
			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->addAttributes(['class' => 'form-select' . $currentClass . $validationClass]);

			$signalTooltip = null;

			if($this->hasSignal())
			{
				$this->addSignalsToInput($input);
				$signalTooltip = $this->getSignalTooltip();
			}

			$hasValidationClass = $this->getValidationClass() && $this->hasErrors() ? ' has-validation' : null;

			return Html::el('div')
				->class('input-group' . $hasValidationClass)
				->addHtml($this->getPrepend() . $input . $this->getAppend() . $signalTooltip . $validationFeedBack);
		}
	}
}

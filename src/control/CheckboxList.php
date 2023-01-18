<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

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
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\RenderBasic;
	use Helper\Signals;

	private const OUTLINE_COLOR_ARRAY = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

	private bool $toggleButton = false;

	private string $outlineColor = 'primary';


	public function setToggleButton(bool $toggleButton = true): self
	{
		$this->toggleButton = $toggleButton;

		return $this;
	}


	public function setOutlineColor(string $color = 'primary'): self
	{
		if(in_array($color, self::OUTLINE_COLOR_ARRAY, true))
		{
			$this->outlineColor = $color;
		}

		return $this;
	}


	public function getCoreControl(): string|Html
	{

		$validationClass = $this->getValidationClass();
		$validationMessage = $this->getValidationFeedback();

		$listValue = $this->value;

		$wrapDiv = Html::el('div')
			->class('container');

		$row = Html::el('div')
			->class('row');

		$wrapDiv->addHtml($row);

		if($this->tooltip)
		{
			$tooltip = Html::el('span')
				->title($this->tooltip)
				->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
				->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('question-circle', color: 'blue'));

			$label = $this->getLabel();
			$label->addHtml($tooltip);
		}

		foreach($this->getItems() as $key => $labelText)
		{
			$id = $this->getHtmlId() . '-' . $key;

			$input = $this->getControlPart();

			$inputClass = ($this->toggleButton) ? 'btn-check' : 'form-check-input checkbox-blue';

			if($this instanceof \ModulIS\Form\Control\Signalable && $this->hasSignal())
			{
				$this->addSignalsToInput($input);
			}

			$input->class($inputClass . ($validationClass ? ' ' . $validationClass : null))
				->setAttribute('id', $id)
				->setAttribute('value', $key);

			if(in_array($key, $listValue, true))
			{
				$input->setChecked('true');
			}

			$labelClass = ($this->toggleButton) ? 'me-2 btn btn-outline-' . $this->outlineColor : null;
			$labelAttribute = ($this->toggleButton) ? 'width: calc(100% - 7.5px)' : 'width: auto';

			$label = Html::el('label')
				->setAttribute('for', $id)
				->setAttribute('style', $labelAttribute)
				->class($labelClass)
				->addHtml($labelText);

			$divClass = ($this->toggleButton) ? 'p-0' : 'form-check';

			$div = Html::el('div')
				->class('form-check-inline mr-0 mb-2 col-' . 12 / $this->itemsPerRow . ' ' . $divClass)
				->addHtml($input . $label);

			$row->addHtml($div);
		}

		return $wrapDiv->addHtml($validationMessage);
	}
}

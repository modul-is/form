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
	use Helper\RenderBasic;
	use Helper\Signals;
	use Helper\ToggleButton;

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

			if($listValue === $input->getValue())
			{
				$input->setChecked('true');
			}

			if(is_array($this->outlineColor))
			{
				$outlineColor = $this->outlineColor[$key] ?? 'primary';
			}
			else
			{
				$outlineColor = $this->outlineColor;
			}

			$outlineColor = in_array($outlineColor, $this->outlineColorArray, true) ? $outlineColor : 'primary';

			$labelClass = ($this->toggleButton) ? 'me-2 btn btn-outline-' . $outlineColor : null;
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

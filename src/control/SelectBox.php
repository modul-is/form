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
		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';
		$imageDiv = null;

		if($this->imageArray)
		{
			$currentClass .= ' select2-image';

			$imageDiv = Html::el('div')
				->id($this->getHtmlId() . '-select2')
				->style('display:none;');

			foreach($this->getItems() as $key => $value)
			{
				if(!array_key_exists($key, $this->imageArray))
				{
					continue;
				}
				$div = Html::el('div')
					->addAttributes(['data-key' => $key, 'data-src' => $this->imageArray[$key]]);

				$imageDiv->addHtml($div);
			}
		}

		$input->addAttributes(['class' => 'form-select' . $currentClass . $validationClass]);

		if($this->hasSignal())
		{
			$this->addSignalsToInput($input);
		}

		$hasValidationClass = $this->getValidationClass() && $this->hasErrors() ? ' has-validation' : null;

		return Html::el('div')
			->class('input-group' . $hasValidationClass)
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $validationFeedBack . $imageDiv);
	}
}

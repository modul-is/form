<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class TextArea extends \Nette\Forms\Controls\TextArea implements Renderable, FloatingRenderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\RenderFloating;
	use Helper\Validation;
	use Helper\Signals;
	use Helper\WrapClass;
	use Helper\RenderInline;
	use Helper\ControlClass;

	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->getOption('template'))
		{
			return (new \Latte\Engine)->renderToString($this->getOption('template'), $this);
		}

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		$wrapClass = 'mb-3 ' . ($this->wrapClass ?? 'col-12');

		if($this->getRenderFloating() ?? $form->getRenderFloating())
		{
			$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
			$validationFeedBack = $this->getValidationFeedback();

			$input = $this->getControl();

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->class('form-control' . $currentClass . $validationClass);
			$input->placeholder($this->getCaption());

			$label = $this->getLabel();

			$floatingDiv = Html::el('div')
				->class('form-floating')
				->addHtml($input . $label . $validationFeedBack);

			$outerDiv = Html::el('div')
				->class($wrapClass)
				->addHtml($floatingDiv);
		}
		else
		{
			$label = $this->getCoreLabel();
			$input = $this->getCoreControl();

			$inputClass = 'align-self-center';
			$labelClass = 'align-self-center';

			if($this->getRenderInline() ?? $form->getRenderInline())
			{
				$inputClass .= $this->inputClass ? ' ' . $this->inputClass : null;
				$labelClass .= $this->labelClass ? ' ' . $this->labelClass : null;
			}
			else
			{
				$inputClass .= $this->inputClass ? ' ' . $this->inputClass : ' col-sm-8';
				$labelClass .= $this->labelClass ? ' ' . $this->labelClass : ' col-sm-4';
			}

			$labelDiv = Html::el('div')
				->class($labelClass)
				->addHtml($label);

			$inputDiv = Html::el('div')
				->class($inputClass)
				->addHtml($input);

			$rowDiv = Html::el('div')
				->class('row')
				->addHtml($labelDiv . $inputDiv);

			$outerDiv = Html::el('div')
				->class($wrapClass)
				->addHtml($rowDiv);
		}

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

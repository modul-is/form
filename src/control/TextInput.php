<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class TextInput extends \Nette\Forms\Controls\TextInput implements Renderable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\FloatingLabel;
	use Helper\Validation;
	use Helper\FocusOutHelper;
	use Helper\WrapClassHelper;
	use Helper\RenderInlineHelper;

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

		$floatingLabel = $this->getFloatingLabel();

		/**
		 * If floating label not set - take it from form
		 */
		if($floatingLabel === null)
		{
			$floatingLabel = $form->getFloatingLabel();
		}

		$wrapClass = 'mb-3' . ($this->wrapClass ? ' ' . $this->wrapClass : null);
		
		if($floatingLabel)
		{
			$validationClass = $this->getValdiationClass() ? ' ' . $this->getValdiationClass() : null;
			$validationFeedBack = $this->getValidationFeedback();

			$input = $this->getControl();

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->class('form-control' . $currentClass . $validationClass);
			$input->placeholder($this->getCaption());

			$label = $this->getLabel();

			$floatingDiv = Html::el('div')
				->class('form-floating')
				->addHtml($input . $label . $validationFeedBack);
			
			if(!$this->wrapClass)
			{
				$wrapClass .= ' col-12';
			}
			
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
				
				if(!$this->wrapClass)
				{
					$wrapClass .= ' row';
				}
			}

			$labelDiv = Html::el('div')
				->class($labelClass)
				->addHtml($label);

			$inputDiv = Html::el('div')
				->addHtml($input);
			
			if($inputClass)
			{
				$inputDiv->class($inputClass);
			}

			$outerDiv = Html::el('div')
				->class($wrapClass)
				->addHtml($labelDiv . $inputDiv);
		}

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

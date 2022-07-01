<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

use Nette\Utils\Html;

class DependentSelect extends \NasExt\Forms\Controls\DependentSelectBox implements Renderable
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
	use Helper\WrapClassHelper;
	use Helper\RenderInlineHelper;

	public function __construct($label = null, array $parents = [], callable $dependentCallback = null)
	{
		parent::__construct($label, $parents);

		$this->setDependentCallback($dependentCallback);

		$this->controlClass = 'form-select';
	}


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
		
		$wrapClass = 'mb-3' . ($this->wrapClass ? ' ' . $this->wrapClass : null);

		if($this->getFloatingLabel() ?? $form->getFloatingLabel())
		{
			$validationClass = $this->getValdiationClass() ? ' ' . $this->getValdiationClass() : null;
			$validationFeedBack = $this->getValidationFeedback();

			$input = $this->getControl();

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->class('form-select' . $currentClass . $validationClass);
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
				->class($inputClass)
				->addHtml($input);

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

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class UploadControl extends \Nette\Forms\Controls\UploadControl implements Renderable
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\InputGroup;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\WrapClass;
	use Helper\RenderInline;
	use Helper\ControlClass;

	public function getCoreControl(): string
	{
		$input = $this->getControl();

		$validationClass = $this->getValidationClass() ? ' ' . $this->getValidationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

		$input->addAttributes(['class' => 'form-control' . $currentClass . $validationClass]);
		
		$prepend = $this->getPrepend();
		$append = $this->getAppend();
		
		if($prepend || $append)
		{
			$inputGroup =  Html::el('div')
				->class('input-group')
				->addHtml($prepend . $input . $append);
			
			return $inputGroup . $validationFeedBack;
		}

		return  $input . $validationFeedBack;
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

		$label = $this->getCoreLabel();
		$input = $this->getCoreControl();

		$inputClass = 'align-self-center';
		$labelClass = 'align-self-center';
		$wrapClass = 'mb-3 ' . ($this->wrapClass ?? 'col-12');

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

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

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

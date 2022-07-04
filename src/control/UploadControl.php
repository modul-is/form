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
	use Helper\WrapClassHelper;
	use Helper\RenderInlineHelper;

	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$validationClass = $this->getValdiationClass() ? ' ' . $this->getValdiationClass() : null;
		$validationFeedBack = $this->getValidationFeedback();

		$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

		$input->addAttributes(['class' => 'form-control' . $currentClass . $validationClass]);

		$label = Html::el('label')
			->class('form-label')
			->for($this->getHtmlId());

		$prepend = null;
		$append = null;

		if(!empty($this->prepend))
		{
			$prepend = Html::el('span')
				->class('input-group-text')
				->addHtml($this->prepend);
		}

		if(!empty($this->append))
		{
			$append = Html::el('span')
				->class('input-group-text')
				->addHtml($this->append);
		}

		return Html::el('div')->class('input-group')
			->addHtml($prepend . $input . $label . $append . $validationFeedBack);
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

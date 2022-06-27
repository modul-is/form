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

		$labelDiv = Html::el('div')
			->class('col-sm-4 control-label align-self-center')
			->addHtml($label);

		$input = $this->getCoreControl();

		$inputDiv = Html::el('div')
			->class('col-sm-8')
			->addHtml($input);

		$outerDiv = Html::el('div')
			->class('mb-3 row')
			->addHtml($labelDiv . $inputDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

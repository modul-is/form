<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class UploadControl extends \Nette\Forms\Controls\UploadControl implements Renderable
{
	use Helper\Color;
	use Helper\Input;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\InputGroup;
	use Helper\AutoRenderSkip;

	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$errorClass = '';
		$errorMessage = null;

		if($this->hasErrors())
		{
			$errorClass = 'is-invalid';

			$errorMessage = Html::el('div')
				->class('invalid-feedback')
				->addHtml($this->getError());
		}

		$input->addAttributes(['class' => 'upload custom-file-input ' . $input->getAttribute('class') . ' ' . $errorClass]);
		$label = Html::el('label')
				->class('custom-file-label')
				->for($this->getHtmlId())
				->setText('Není vybrán soubor');

		$wrapDiv = Html::el('div')
				->class('custom-file')
				->addHtml($input)
				->addHtml($label);

		$prepend = null;
		$append = null;

		if(!empty($this->prepend))
		{
			$prependText = Html::el('span')
				->class('input-group-text')
				->addHtml($this->prepend);

			$prepend = Html::el('div')
				->class('input-group-prepend')
				->addHtml($prependText);
		}

		if(!empty($this->append))
		{
			$appendText = Html::el('span')
				->class('input-group-text')
				->addHtml($this->append);

			$append = Html::el('div')
				->class('input-group-append')
				->addHtml($appendText);
		}

		return Html::el('div')->class('input-group')
			->addHtml($prepend . $wrapDiv . $append . $errorMessage);
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
			->class('form-group row')
			->addHtml($labelDiv . $inputDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class RadioList extends \Nette\Forms\Controls\RadioList implements Renderable
{
	use Helper\Color;
	use Helper\Input;
	use Helper\CoreList;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;

	public function getCoreControl(): string
	{
		$inputArray = [];

		foreach($this->getItems() as $key => $input)
		{
			$input = $this->getControlPart($key);
			$label = $this->getLabelPart($key);

			$labelSpan = Html::el('span')
				->class('label-text ' . $input->getAttribute('class'))
				->addHtml($label);

			if(isset($this->tooltips[$key]))
			{
				$tooltip = Html::el('span')
					->title($this->tooltips[$key])
					->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
					->addHtml(\Kravcik\Macros\FontAwesomeMacro::renderIcon('question-circle', ['color' => 'blue']));

				$labelSpan->addHtml($tooltip);
			}

			$labelWrap = Html::el('label')
				->addHtml($input)
				->addHtml($labelSpan);

			$class = 'form-check-inline mr-0 col';

			if($this->itemsPerRow)
			{
				$class .= '-' . 12 / $this->itemsPerRow;
			}

			if($this->itemClass)
			{
				$class .= ' ' . $this->itemClass;
			}

			if($this->color)
			{
				$class .= ' radiobox-' . $this->color;
				$input->setAttribute('class', $input->getAttribute('class') . ' radiobox-' . $this->color);
			}

			$inputArray[] = Html::el('div')
				->class('form-check ' . $class)
				->addHtml($labelWrap);
		}

		$errorMessage = null;

		if($this->hasErrors())
		{
			$errorMessage = Html::el('div')
				->class('check-invalid')
				->addHtml($this->getError());
		}

		$wrapRow = Html::el('div')
			->class('row')
			->addHtml(implode('', $inputArray));

		$wrapContainer = Html::el('div')
			->class('container')
			->addHtml($wrapRow);

		return $wrapContainer . $errorMessage;
	}


	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
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

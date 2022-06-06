<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class CheckboxList extends \Nette\Forms\Controls\CheckboxList implements Renderable
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\CoreList;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\ValidationSuccessMessage;


	public function getCoreControl(): Html|string
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
				$class .= ' checkbox-' . $this->color;
				$input->setAttribute('class', $input->getAttribute('class') . ' checkbox-' . $this->color);
			}

			$inputArray[] = Html::el('div')
				->class('form-check ' . $class)
				->addHtml($labelWrap);
		}

		$validationFeedBack = null;

		if($this->getForm()->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationFeedBack = Html::el('div')
					->class('check-invalid')
					->addHtml($this->getError());
			}
			elseif($this->getValidationSuccessMessage())
			{
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		$wrapRow = Html::el('div')
			->class('row')
			->addHtml(implode('', $inputArray));

		$wrapContainer = Html::el('div')
			->class('container')
			->addHtml($wrapRow);

		return $wrapContainer . $validationFeedBack;
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

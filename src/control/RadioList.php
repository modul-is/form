<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class RadioList extends \Nette\Forms\Controls\RadioList implements Renderable
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\CoreList;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\ValidationSuccessMessage;

	public function getCoreControl(): string
	{
		$inputs = null;

		foreach($this->getItems() as $key => $input)
		{
			$input = $this->getControlPart($key);
			
			$inputColorClass = $this->color ? ' checkbox-' . $this->color : null;
		
			$input->class('form-check-input ' . $input->getAttribute('class') . $inputColorClass);
			
			$label = $this->getLabelPart($key);
			
			$label->class('form-check-label');

			$tooltip = null;
			
			if(isset($this->tooltips[$key]))
			{
				$tooltip = Html::el('span')
					->title($this->tooltips[$key])
					->addAttributes(['data-placement' => 'top', 'data-toggle' => 'tooltip'])
					->addHtml(\Kravcik\Macros\FontAwesomeMacro::renderIcon('question-circle', ['color' => 'blue']));
			}

			$class = 'form-check-inline mr-0 col-' . 12 / $this->itemsPerRow;

			if($this->itemClass)
			{
				$class .= ' ' . $this->itemClass;
			}

			$inputs .= Html::el('div')
				->class('form-check ' . $class)
				->addHtml($input . $label . $tooltip);
		}

		$validationFeedBack = null;
		$validationClass = null;

		if($this->getForm()->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationClass = 'is-invalid';
				$validationFeedBack = Html::el('div')
					->class('check-invalid')
					->addHtml($this->getError());
			}
			elseif($this->getValidationSuccessMessage())
			{
				$validationClass = 'is-valid';
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		$wrapRow = Html::el('div')
			->class('row')
			->addHtml($inputs);

		$wrapContainer = Html::el('div')
			->class('container ' . $validationClass)
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

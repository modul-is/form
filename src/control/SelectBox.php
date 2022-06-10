<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class SelectBox extends \Nette\Forms\Controls\SelectBox implements Renderable
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
	use Helper\ValidationSuccessMessage;

	public function __construct($label = null, ?array $items = null)
	{
		parent::__construct($label, $items);

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

		$floatingLabel = $this->getFloatingLabel();

		/**
		 * If floating label not set - take it from form
		 */
		if($floatingLabel === null)
		{
			/** @var \ModulIS\Form\Form $form */
			$form = $this->getForm();

			$floatingLabel = $form->getFloatingLabel();
		}

		if($floatingLabel)
		{
			$input = $this->getControl();

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->class('form-select' . $currentClass);
			$input->placeholder($this->getCaption());

			$label = $this->getLabel();

			$outerDiv = Html::el('div')
				->class('form-floating mb-3')
				->addHtml($input . $label);
		}
		else
		{
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
		}

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}

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
			$validationClass = $this->getValdiationClass() ? ' ' . $this->getValdiationClass() : null;
			$validationFeedBack = $this->getValidationFeedback();

			$input = $this->getControl();

			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->class('form-control' . $currentClass . $validationClass);
			$input->placeholder($this->getCaption());

			$label = $this->getLabel();

			$outerDiv = Html::el('div')
				->class('form-floating mb-3')
				->addHtml($input . $label . $validationFeedBack);
		}
		else
		{
			$label = $this->getCoreLabel();
			$input = $this->getCoreControl();

			$labelDiv = Html::el('div')
				->class('col-sm-4 control-label align-self-center')
				->addHtml($label);

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

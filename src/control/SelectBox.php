<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;
use \Kravcik\LatteFontAwesomeIcon\Extension;

class SelectBox extends \Nette\Forms\Controls\SelectBox implements Renderable
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\FloatingLabel;
	use Helper\ValidationSuccessMessage;

	private array $imageArray = [];


	public function setImageArray(array $imageArray): self
	{
		$this->imageArray = $imageArray;

		return $this;
	}


	public function getCoreControl()
	{
		$input = $this->getControl();

		$validationClass = null;
		$validationFeedBack = null;

		if($this->getForm()->isSubmitted())
		{
			if($this->hasErrors())
			{
				$validationClass = ' is-invalid';

				$validationFeedBack = Html::el('div')
					->class('invalid-feedback')
					->addHtml($this->getError());
			}
			elseif($this->isRequired())
			{
				$validationClass = ' is-valid';

				if($this->getValidationSuccessMessage())
				{
					$validationFeedBack = Html::el('div')
						->class('valid-feedback')
						->addHtml($this->getValidationSuccessMessage());
				}
			}
		}

		if($this->imageArray)
		{
			$input->addAttributes(['style' => 'display: none;']);

			if($this->getPrompt())
			{
				$labelPrompt = Html::el('div')
					->class('label-text mt-1')
					->addHtml($this->getPrompt());

				$optionPrompt = Html::el('a')
					->class('dropdown-item')
					->addHtml($labelPrompt);

				$li = Html::el('li')
					->value('')
					->addHtml($optionPrompt);
			}
			else
			{
				$li = null;
			}

			$items = $this->getItems();

			foreach($items as $key => $label)
			{
				$img = Html::el('img')
					->src($this->imageArray[$key]);

				$labelWrap = Html::el('div')
					->class('label-text mt-1 ms-2')
					->addHtml($label);

				$imgWrap = Html::el('div')
					->class('image-wrap')
					->addHtml($img);

				$optionLink = Html::el('a')
					->class('row dropdown-item')
					->addHtml($imgWrap . $labelWrap);

				$li .= Html::el('li')
					->value($key)
					->addHtml($optionLink);
			}

			if($this->getValue())
			{
				$promptValue = $items[$this->getValue()];
			}
			else
			{
				$promptValue = $this->getPrompt() ?: reset($items);
			}

			$span = Html::el('span')
				->class('prompt-text float-start')
				->addHtml($promptValue);

			$button = Html::el('a')
				->class('btn dropdown-toggle d-block' . $validationClass)
				->id($this->getHtmlId() . '-dropdown')
				->addAttributes(['data-bs-toggle' => 'dropdown', 'aria-expanded' => 'false'])
				->type('button')
				->addHtml($span);

			$ul = Html::el('ul')
				->addAttributes(['aria-labelledby' => $this->getHtmlId() . '-dropdown'])
				->class('dropdown-menu ps-2 pe-2 w-100')
				->addHtml($li);

			$div = Html::el('div')
				->id($this->getHtmlId() . '-wrapper')
				->addAttributes(['data-parent-id' => $this->getHtmlId()])
				->class('dropdown select-image d-block' . $validationClass)
				->addHtml($button . $ul);

			return $input . $div . $validationFeedBack;
		}
		else
		{
			$currentClass = $input->getAttribute('class') ? ' ' . $input->getAttribute('class') : '';

			$input->addAttributes(['class' => 'form-select' . $currentClass . $validationClass]);

			if(!empty($this->onFocusOut))
			{
				/** @var \Nette\Application\UI\Presenter $presenter */
				$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);

				$input->setAttribute('data-on-focusout', $presenter->link($this->lookupPath('Nette\Application\UI\Presenter') . '-focusout!'));
			}

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

			$focusOutTooltip = null;

			if(!empty($this->onFocusOut))
			{
				$waiting = Html::el('span')
					->class('input-group-text focusout-waiting')
					->addHtml(Extension::render('arrow-right-to-bracket'));

				$loading = Html::el('span')
					->class('input-group-text focusout-loading')
					->style('display', 'none')
					->addHtml(Extension::render('spinner fa-spin'));

				$success = Html::el('span')
					->class('input-group-text focusout-success')
					->title('')
					->style('display', 'none')
					->addHtml(Extension::render('check', color: 'green'));

				$error = Html::el('span')
					->class('input-group-text focusout-error')
					->title('')
					->style('display', 'none')
					->addHtml(Extension::render('times', color: 'red'));

				$focusOutTooltip = $waiting . $loading . $success . $error;
			}

			return Html::el('div')->class('input-group')
				->addHtml($prepend . $input . $append . $focusOutTooltip . $validationFeedBack);
		}
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

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Utils\Html;
use function assert;

class DuplicatorCreateSubmit extends SubmitButton
{
	public function addCreateOnClick(bool $allowEmpty = true, ?callable $callback = null): void
	{
		$this->onClick[] = function(\Nette\Forms\Controls\SubmitButton $button) use ($allowEmpty, $callback): void
		{
			$form = $button->getForm();
			assert($form instanceof Form);

			$duplicator = $button->lookup(Duplicator::class);

			if($allowEmpty === true || $duplicator->isAllFilled() === true)
			{
				$newContainer = $duplicator->createOne();

				if($form->getPresenter()->isAjax())
				{
					$component = $button->lookup(FormComponent::class);

					$component->redrawControl('form');
				}

				if(is_callable($callback))
				{
					$callback($duplicator, $newContainer);
				}
			}

			$form->onSuccess = [];
		};
	}


	public function getCoreControl(): Html
	{
		$control = $this->getControl();

		/**
		 * Adding a row never runs client-side validation - scope is taken from Nette, empty when not set
		 */
		$attributes = [
			'name' => $this->getHtmlName(),
			'value' => $this->translate($this->getCaption()),
			'formnovalidate' => '',
			'data-nette-validation-scope' => $control->getAttribute('data-nette-validation-scope') ?? '[]',
			'type' => 'submit'
		];

		$currentClass = $control->getAttribute('class');

		$icon = Extension::render($this->isDisabled() ? 'info' : 'plus');

		$form = $this->getForm();
		assert($form instanceof Form);

		$class = 'btn-duplicator btn-sm float-start'
			. ($form->ajax ? ' ajax' : '')
			. ($currentClass ? ' ' . $currentClass : '');

		return Html::el('button')
			->class($class)
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml($icon)
			->addText($this->translate($this->getCaption()));
	}
}

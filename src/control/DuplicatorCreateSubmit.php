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
			assert($duplicator instanceof Duplicator);

			if($allowEmpty === true || $duplicator->isAllFilled() === true)
			{
				$newContainer = $duplicator->createOne();

				if($form->getPresenter()->isAjax())
				{
					$component = $button->lookup(FormComponent::class);
					assert($component instanceof FormComponent);

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
		$attributes = [
			'name' => $this->getHtmlName(),
			'value' => 'Přidat',
			'formnovalidate' => '',
			'data-nette-validation-scope' => '["multiplier"]',
			'label' => 'Přidat',
			'type' => 'submit'
		];

		$currentClass = $this->getControl()->getAttribute('class');

		$icon = Extension::render($this->isDisabled() ? 'info' : 'plus');

		$form = $this->getForm();
		assert($form instanceof Form);

		$class = 'btn' . $this->getFormButtonClass()
			. ' btn-outline-primary btn-sm float-start'
			. ($form->ajax ? ' ajax' : '')
			. ($currentClass ? ' ' . $currentClass : '');

		return Html::el('button')
			->class($class)
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml($icon . $this->translate($this->getCaption()));
	}
}

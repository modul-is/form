<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class DuplicatorCreateSubmit extends SubmitButton
{
	public function addCreateOnClick(bool $allowEmpty = true, ?callable $callback = null)
	{
		$this->onClick[] = function(\Nette\Forms\Controls\SubmitButton $button) use ($allowEmpty, $callback): void
		{
			/** @var \ModulIS\Form\Form $form */
			$form = $button->getForm();

			/** @var Duplicator $duplicator */
			$duplicator = $button->lookup(Duplicator::class);

			if($allowEmpty === true || $duplicator->isAllFilled() === true)
			{
				$newContainer = $duplicator->createOne();

				if($form->getPresenter()->isAjax())
				{
					/** @var \ModulIS\Form\FormComponent $component */
					$component = $button->lookup(\ModulIS\Form\FormComponent::class);

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


	public function render(): Html
	{
		/** @var Duplicator $duplicator */
		$duplicator = $this->lookup(Duplicator::class);

		$attributes = [
			'name' => $duplicator->getName() . '[add]',
			'value' => 'Přidat',
			'formnovalidate' => '',
			'data-nette-validation-scope' => '["multiplier"]',
			'label' => 'Přidat',
			'type' => 'submit'
		];

		$currentClass = $this->getControl()->getAttribute('class');

		$icon = \Kravcik\LatteFontAwesomeIcon\Extension::render($this->isDisabled() ? 'info' : 'plus');

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		return Html::el('button')
			->class('btn btn-primary float-left btn-xs ' . ($form->ajax ? 'ajax' : '') . ($currentClass ? ' ' . $currentClass : ''))
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml($icon . $this->getCaption());
	}
}

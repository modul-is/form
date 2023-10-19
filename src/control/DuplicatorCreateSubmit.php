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
			$form = $button->getForm();
			\assert($form instanceof \ModulIS\Form\Form);

			$duplicator = $button->lookup(Duplicator::class);
			\assert($duplicator instanceof Duplicator);

			if($allowEmpty === true || $duplicator->isAllFilled() === true)
			{
				$newContainer = $duplicator->createOne();

				if($form->getPresenter()->isAjax())
				{
					$component = $button->lookup(\ModulIS\Form\FormComponent::class);
					\assert($component instanceof \ModulIS\Form\FormComponent);

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
		$duplicator = $this->lookup(Duplicator::class);
		\assert($duplicator instanceof Duplicator);

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

		$form = $this->getForm();
		\assert($form instanceof \ModulIS\Form\Form);

		return Html::el('button')
			->class('btn btn-outline-primary float-left btn-xs ' . ($form->ajax ? 'ajax' : '') . ($currentClass ? ' ' . $currentClass : ''))
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml($icon . $this->getCaption());
	}
}

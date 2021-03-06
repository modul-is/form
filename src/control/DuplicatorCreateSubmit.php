<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class DuplicatorCreateSubmit extends SubmitButton
{
	public function addCreateOnClick(bool $allowEmpty = true, ?callable $callback = null)
	{
		$this->setValidationScope([]);

		$this->onClick[] = function(\Nette\Forms\Controls\SubmitButton $button) use ($allowEmpty, $callback): void
		{
			/** @var Duplicator $duplicator */
			$duplicator = $button->lookup(Duplicator::class);

			if($allowEmpty === true || $duplicator->isAllFilled() === true)
			{
				$newContainer = $duplicator->createOne();

				if(is_callable($callback))
				{
					$callback($duplicator, $newContainer);
				}
			}

			$button->getForm()->onSuccess = [];
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

		$icon = \Kravcik\Macros\FontAwesomeMacro::renderIcon($this->isDisabled() ? 'info' : 'plus');

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		return Html::el('button')
			->class('btn btn-primary float-left btn-xs ' . ($form->ajax ? 'ajax' : ''))
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml($icon . $this->getCaption());
	}
}

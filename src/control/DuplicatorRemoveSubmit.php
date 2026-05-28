<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Utils\Html;
use function assert;

class DuplicatorRemoveSubmit extends SubmitButton
{
	public function addRemoveOnClick(?callable $callback = null): void
	{
		$this->onClick[] = function(\Nette\Forms\Controls\SubmitButton $button) use ($callback): void
		{
			$duplicator = $button->lookup(Duplicator::class);

			if(is_callable($callback))
			{
				$callback($duplicator, $button->getParent());
			}

			$form = $button->getForm(false);
			assert($form instanceof Form);

			if($form->getPresenter()->isAjax())
			{
				$component = $button->lookup(FormComponent::class);

				$component->redrawControl('form');
			}

			$form->onSuccess = [];

			$duplicator->removeComponent($button->getParent());
		};
	}


	public function getCoreControl(): Html
	{
		$attributes = [
			'name' => $this->getHtmlName(),
			'formnovalidate' => '',
			'type' => 'submit'
		];

		$form = $this->getForm();
		assert($form instanceof Form);

		$currentClass = $this->getControl()->getAttribute('class');
		$class = 'btn' . $this->getFormButtonClass()
			. ' btn-sm btn-outline-danger float-end'
			. ($form->ajax ? ' ajax' : '')
			. ($currentClass ? ' ' . $currentClass : '');

		return Html::el('button')
			->class($class)
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml(Extension::render('times') . $this->translate($this->getCaption()));
	}
}

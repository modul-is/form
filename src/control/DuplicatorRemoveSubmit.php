<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class DuplicatorRemoveSubmit extends SubmitButton
{
	public function addRemoveOnClick(?callable $callback = null)
	{
		$this->onClick[] = function(\Nette\Forms\Controls\SubmitButton $button) use ($callback): void
		{
			$duplicator = $button->lookup(Duplicator::class);
			\assert($duplicator instanceof Duplicator);

			if(is_callable($callback))
			{
				$callback($duplicator, $button->parent);
			}

			$form = $button->getForm(false);
			\assert($form instanceof \ModulIS\Form\Form);

			if($form->getPresenter()->isAjax())
			{
				$component = $button->lookup(\ModulIS\Form\FormComponent::class);
				\assert($component instanceof \ModulIS\Form\FormComponent);

				$component->redrawControl('form');
			}

			$form->onSuccess = [];

			$duplicator->removeComponent($button->parent);
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
		\assert($form instanceof \ModulIS\Form\Form);

		$currentClass = $this->getControl()->getAttribute('class');

		$button = Html::el('button')
			->class('btn btn-xs btn-outline-danger float-end' . ($form->ajax ? ' ajax' : '') . ($currentClass ? ' ' . $currentClass : ''))
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('times') . $this->getCaption());

		return $button;
	}
}

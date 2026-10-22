<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use ModulIS\Form\Renderer\Bootstrap6Renderer;
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

			$container = $button->getParent();
			assert($container !== null);

			$duplicator->removeComponent($container);
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
		$class = 'btn-subtle theme-danger btn-sm ms-auto'
			. ($form->ajax ? ' ajax' : '')
			. ($currentClass ? ' ' . $currentClass : '');

		return Html::el('button')
			->class($class)
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml(Bootstrap6Renderer::of($this)->icon('remove'))
			->addText($this->translate($this->getCaption()));
	}


	/**
	 * Duplicator buttons keep their own markup (no validation, AJAX class of the form)
	 */
	public function render(): Html
	{
		return $this->getCoreControl();
	}
}

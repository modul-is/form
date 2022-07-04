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
			/** @var Duplicator $duplicator */
			$duplicator = $button->lookup(Duplicator::class);

			if(is_callable($callback))
			{
				$callback($duplicator, $button->parent);
			}

			$form = $button->getForm(false);

			if($form)
			{
				$form->onSuccess = [];
			}

			$duplicator->removeComponent($button->parent);
		};
	}


	public function render(): Html|string
	{
		/** @var Duplicator $duplicator */
		$duplicator = $this->lookup(Duplicator::class);
		$duplicatorContainer = $this->lookup(\ModulIS\Form\DuplicatorContainer::class);

		$attributes = [
			'name' => $duplicator->getName() . '[' . $duplicatorContainer->getName() . ']' . '[' . $this->getName() . ']',
			'formnovalidate' => '',
			'type' => 'submit'
		];

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		$button = Html::el('button')
			->class('btn btn-xs btn-danger float-end ' . ($form->ajax ? 'ajax' : ''))
			->addAttributes($attributes)
			->disabled($this->isDisabled())
			->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('times') . $this->getCaption());

		$clearfix = Html::el('div')
			->class('clearfix');

		return $button . $clearfix;
	}
}

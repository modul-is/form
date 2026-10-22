<?php

declare(strict_types = 1);

namespace ModulIS\Form\Demo\Form;

use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use Nette\Utils\ArrayHash;

/**
 * Form rendered by its own template (manualForm.latte) with tags {inputRender}, {inputCore} and {labelCore}
 */
class ManualForm extends FormComponent
{
	public function __construct()
	{
		$this->setRenderManually(true);
	}


	public function prepare(): void
	{
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();
		$form->setAjax();

		$form->addText('name', 'Jméno')
			->setRequired('Vyplňte jméno');

		$form->addEmail('email', 'E-mail')
			->setIcon('envelope');

		$form->addTextArea('note', 'Poznámka', null, 2)
			->setRequired('Vyplňte poznámku');

		$form->addRadioList('contact', 'Kontaktovat', ['email' => 'E-mailem', 'phone' => 'Telefonem'])
			->setDefaultValue('email');

		$form->addSubmit('send', 'Odeslat');

		$form->onSuccess[] = function(Form $form, ArrayHash $values): void
		{
			$this->getPresenter()->flashMessage('Ručně vykreslený formulář odeslán: ' . $values->name, 'success');
			$this->getPresenter()->redrawControl('flashMessages');
		};

		return $form;
	}
}

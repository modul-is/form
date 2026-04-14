<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Form;

trait ButtonRounded
{
	/**
	 * Returns default button class from form (setButtonClass), with leading space when set.
	 * Priority: button over form – when the button has its own class (setClass), form class is not added.
	 */
	protected function getFormButtonClass(): string
	{
		$form = $this->getForm(false);

		if(!$form instanceof Form)
		{
			return '';
		}

		$userClass = trim((string) $this->getControl()->getAttribute('class'));

		if($userClass !== '')
		{
			return '';
		}

		$class = $form->getButtonClass();

		return $class !== null && $class !== '' ? ' ' . $class : '';
	}
}

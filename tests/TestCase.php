<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

abstract class TestCase extends \Tester\TestCase
{
	public function getForm()
	{
		$form = new \ModulIS\Form\Form;

		$submit = $form->addSubmit('save');

		$form->setSubmittedBy($submit);

		(new TestPresenter)->addComponent($form, 'form');

		return $form;
	}
}

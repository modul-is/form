<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class CheckboxlistTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second']);

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderHidden()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}
}

$testcase = new CheckboxlistTest;
$testcase->run();

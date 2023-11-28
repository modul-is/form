<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class CheckboxlistTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second']);

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}
}

$testcase = new CheckboxlistTest;
$testcase->run();

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

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderItemsPerRow()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setItemsPerRow(2);

		$this->assertRender(__DIR__ . '/itemsPerRow.latte', $form->getComponent('checkboxlist')->render()->__toString());
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

<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class CheckboxTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check');

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('check')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('check')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setTemplate(__DIR__ . '/custom.latte');

		$checkboxString = 'custom-template';

		Assert::same($checkboxString, $form->getComponent('check')->render());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setOption('hide', true);

		$checkboxString = '';

		Assert::same($checkboxString, $form->getComponent('check')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setAutoRenderSkip();

		$checkboxString = '';

		Assert::same($checkboxString, $form->getComponent('check')->render());
	}


	public function testRenderTooltip()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setTooltip('MyTooltip');

		$this->assertRender(__DIR__ . '/tooltip.latte', $form->getComponent('check')->render()->__toString());
	}
}

$testcase = new CheckboxTest;
$testcase->run();

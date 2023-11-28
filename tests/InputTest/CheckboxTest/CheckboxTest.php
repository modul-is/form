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

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('check')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('check')->render()->__toString());
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

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/tooltip.latte'));

		Assert::same($html, $form->getComponent('check')->render()->__toString());
	}
}

$testcase = new CheckboxTest;
$testcase->run();

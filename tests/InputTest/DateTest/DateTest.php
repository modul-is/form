<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class DateTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setPrepend('prepend');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/prepend.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setAppend('append');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/append.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setIcon('user');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/icon.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setRenderFloating();

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderFormFloatingLabel()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addDate('date', 'Date');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addDate('date', 'Date')
			->setRenderFloating(false);

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('date')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('date')->render());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('date')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('date')->render());
	}
}

$testcase = new DateTest;
$testcase->run();

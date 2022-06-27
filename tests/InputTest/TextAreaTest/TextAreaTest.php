<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class TextAreaTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setPrepend('prepend');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/prepend.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setAppend('append');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/append.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setIcon('user');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/icon.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setFloatingLabel(true);

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderFormFloatingLabel()
	{
		$form = $this->getForm();

		$form->setFloatingLabel(true);

		$form->addTextArea('text', 'Area');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = $this->getForm();

		$form->setFloatingLabel(true);

		$form->addTextArea('text', 'Area')
			->setFloatingLabel(false);

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Text')
			->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('text')->render());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Text')
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('text')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Text')
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('text')->render());
	}
}

$testcase = new TextAreaTest;
$testcase->run();

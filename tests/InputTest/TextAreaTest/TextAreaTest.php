<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class TextAreaTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area');

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setPrepend('prepend');

		$this->assertRender(__DIR__ . '/prepend.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setAppend('append');

		$this->assertRender(__DIR__ . '/append.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setIcon('user');

		$this->assertRender(__DIR__ . '/icon.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setRenderFloating();

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderFormFloatingLabel()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addTextArea('text', 'Area');

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addTextArea('text', 'Area')
			->setRenderDefault();

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('text')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addTextArea('text', 'Area')
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('text')->render()->__toString());
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

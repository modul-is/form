<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class SelectTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second']);

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('select')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('select')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setRenderFloating();

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('select')->render()->__toString());
	}


	public function testRenderFormFloatingLabel()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second']);

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('select')->render()->__toString());
	}


	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setRenderDefault();

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('select')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('select')->render());
	}


	public function testRenderWithImage()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setPrompt('~ Vyberte ~')
			->setImageArray(['first' => '/images/first.png', 'second' => '/images/second.png']);

		$this->assertRender(__DIR__ . '/image.latte', $form->getComponent('select')->render()->__toString());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('select')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('select')->render());
	}
}

$testcase = new SelectTest;
$testcase->run();

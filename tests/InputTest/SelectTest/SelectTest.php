<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class SelectTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second']);

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('select')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('select')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setFloatingLabel(true);

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('select')->render()->__toString());
	}


	public function testRenderFormFloatingLabel()
	{
		$form = $this->getForm();

		$form->setFloatingLabel(true);

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second']);

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('select')->render()->__toString());
	}


	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = $this->getForm();

		$form->setFloatingLabel(true);

		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setFloatingLabel(false);

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('select')->render()->__toString());
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

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/image.latte'));

		Assert::same($html, $form->getComponent('select')->render()->__toString());
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

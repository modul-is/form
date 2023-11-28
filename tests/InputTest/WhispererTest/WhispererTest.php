<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class WhispererTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B']);

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setIcon('user');

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/icon.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}


	public function testFloatingLabel()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setRenderFloating();

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}


	public function testChangeCallback()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setOnChangeCallback(function(){});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/changeCallback.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}


	public function testDependent()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setParents([$form['select']])
			->setDependentCallback(function($parentArray)
			{
				return new \ModulIS\Form\Helper\DependentData([]);
			});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/dependent.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}


	public function testIncompatibleCallbacks()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		Assert::exception(function() use ($form)
		{
			$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
				->setOnChangeCallback(function(){})
				->setOnSelectCallback(function(){});
		}, \Nette\InvalidStateException::class, 'Cannot use onSelectCallback and onChangeCallback together for input "whisperer"');

		Assert::exception(function() use ($form)
		{
			$form->addWhisperer('whisperer1', 'Whisperer', ['a' => 'A', 'b' => 'B'])
				->setOnSelectCallback(function(){})
				->setOnChangeCallback(function(){});
		}, \Nette\InvalidStateException::class, 'Cannot use onChangeCallback and onSelectCallback together for input "whisperer1"');
	}


	public function testAllCallbacks()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setOnFocusOutCallback(function(){})
			->setOnSelectCallback(function(){})
			->setParents([$form['select']])
			->setDependentCallback(function($parentArray)
			{
				return new \ModulIS\Form\Helper\DependentData([]);
			});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/callbacks.latte'));

		Assert::same($html, $form->getComponent('whisperer')->render()->__toString());
	}
}

$testcase = new WhispererTest;
$testcase->run();
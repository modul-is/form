<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class DependentTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addDependentSelect('dependent', 'Dependent', [$form['select']])
			->setDependentCallback(function($parentArray)
			{
				if($parentArray['select'] === 'a')
				{
					$data = ['a' => 'A', 'aa' => 'AA'];
				}
				else
				{
					$data = ['b' => 'B', 'bb' => 'BB'];
				}

				return new \ModulIS\Form\Helper\DependentData($data);
			});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('dependent')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addDependentSelect('dependent', 'Dependent', [$form['select']])
			->setOption('id', 'customId')
			->setDependentCallback(function($parentArray)
			{
				if($parentArray['select'] === 'a')
				{
					$data = ['a' => 'A', 'aa' => 'AA'];
				}
				else
				{
					$data = ['b' => 'B', 'bb' => 'BB'];
				}

				return new \ModulIS\Form\Helper\DependentData($data);
			});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('dependent')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addDependentSelect('dependent', 'Dependent', [$form['select']])
			->setIcon('user')
			->setDependentCallback(function($parentArray)
			{
				if($parentArray['select'] === 'a')
				{
					$data = ['a' => 'A', 'aa' => 'AA'];
				}
				else
				{
					$data = ['b' => 'B', 'bb' => 'BB'];
				}

				return new \ModulIS\Form\Helper\DependentData($data);
			});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/icon.latte'));

		Assert::same($html, $form->getComponent('dependent')->render()->__toString());
	}


	public function testFloatingLabel()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addDependentSelect('dependent', 'Dependent', [$form['select']])
			->setRenderFloating()
			->setDependentCallback(function($parentArray)
			{
				if($parentArray['select'] === 'a')
				{
					$data = ['a' => 'A', 'aa' => 'AA'];
				}
				else
				{
					$data = ['b' => 'B', 'bb' => 'BB'];
				}

				return new \ModulIS\Form\Helper\DependentData($data);
			});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/floatingLabel.latte'));

		Assert::same($html, $form->getComponent('dependent')->render()->__toString());
	}


	public function testChangeCallback()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B']);

		$form->addDependentSelect('dependent', 'Dependent', [$form['select']])
			->setDependentCallback(function($parentArray)
			{
				if($parentArray['select'] === 'a')
				{
					$data = ['a' => 'A', 'aa' => 'AA'];
				}
				else
				{
					$data = ['b' => 'B', 'bb' => 'BB'];
				}

				return new \ModulIS\Form\Helper\DependentData($data);
			})
			->setOnChangeCallback(function(){});

		$html = str_replace(["\t", "\n", "\r"], '', file_get_contents(__DIR__ . '/callback.latte'));

		Assert::same($html, $form->getComponent('dependent')->render()->__toString());
	}
}

$testcase = new DependentTest;
$testcase->run();
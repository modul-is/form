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

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('dependent')->render()->__toString());
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

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('dependent')->render()->__toString());
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

		$this->assertRender(__DIR__ . '/icon.latte', $form->getComponent('dependent')->render()->__toString());
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

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('dependent')->render()->__toString());
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

		$this->assertRender(__DIR__ . '/callback.latte', $form->getComponent('dependent')->render()->__toString());
	}
}

$testcase = new DependentTest;
$testcase->run();
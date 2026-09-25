<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class DateTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date');

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setPrepend('prepend');

		$this->assertRender(__DIR__ . '/prepend.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setAppend('append');

		$this->assertRender(__DIR__ . '/append.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setIcon('user');

		$this->assertRender(__DIR__ . '/icon.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setRenderFloating();

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderFormFloatingLabel()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addDate('date', 'Date');

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = $this->getForm();

		$form->setRenderFloating();

		$form->addDate('date', 'Date')
			->setRenderDefault();

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('date')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addDate('date', 'Date')
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('date')->render()->__toString());
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


	public function testMinMaxWithDateTimeLimit()
	{
		$form = $this->getForm();

		$date = $form->addDate('date', 'Date')
			->addRule($form::Min, 'min', new \DateTimeImmutable('2026-01-10'))
			->addRule($form::Max, 'max', new \DateTimeImmutable('2026-01-20'));

		Assert::same('2026-01-10', $date->getControl()->min);
		Assert::same('2026-01-20', $date->getControl()->max);

		foreach(['2026-01-10' => [], '2026-01-15' => [], '2026-01-09' => ['min'], '2026-01-21' => ['max']] as $value => $errors)
		{
			$date->setValue($value);
			$date->cleanErrors();
			$date->validate();

			Assert::same($errors, $date->getErrors(), $value);
			Assert::same($value, $date->getValue());
		}
	}


	public function testMinWithStringLimitOnDateTime()
	{
		$form = $this->getForm();

		$dateTime = $form->addDateTime('datetime', 'DateTime')
			->addRule($form::Min, 'min', '2026-01-10 12:00');

		$dateTime->setValue('2026-01-10 11:00');
		$dateTime->validate();
		Assert::same(['min'], $dateTime->getErrors());

		$dateTime->setValue('2026-01-10 13:00');
		$dateTime->cleanErrors();
		$dateTime->validate();
		Assert::same([], $dateTime->getErrors());
	}
}

$testcase = new DateTest;
$testcase->run();

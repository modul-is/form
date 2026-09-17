<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use ModulIS\Form\Control\CurrencyInput;
use Tester\Assert;

class CurrencyTest extends TestCase
{
	public function tearDown()
	{
		CurrencyInput::setDefaultCurrency('Kč');
	}


	public function testRender()
	{
		$form = $this->getForm();

		$form->addCurrency('price', 'Price');

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('price')->render()->__toString());
	}


	public function testRenderWithValue()
	{
		$form = $this->getForm();

		$form->addCurrency('price', 'Price')
			->setValue(1000000);

		$this->assertRender(__DIR__ . '/value.latte', $form->getComponent('price')->render()->__toString());
	}


	public function testRenderCustomCurrency()
	{
		$form = $this->getForm();

		$form->addCurrency('price', 'Price', 'EUR');

		$this->assertRender(__DIR__ . '/currency.latte', $form->getComponent('price')->render()->__toString());
	}


	public function testDefaultCurrency()
	{
		CurrencyInput::setDefaultCurrency('USD');

		$form = $this->getForm();

		Assert::same('USD', $form->addCurrency('price', 'Price')->getCurrency());
	}


	public function testPerInputCurrencyBeatsDefault()
	{
		CurrencyInput::setDefaultCurrency('USD');

		$form = $this->getForm();

		Assert::same('EUR', $form->addCurrency('price', 'Price')->setCurrency('EUR')->getCurrency());
	}


	/**
	 * Vizualne se hodnota formatuje s oddelovacem tisicu, ven ale leze cely integer.
	 */
	public function testValueIsFormattedButReturnedAsInteger()
	{
		$form = $this->getForm();

		$input = $form->addCurrency('price', 'Price')
			->setValue(1000000);

		Assert::contains("1\u{00A0}000\u{00A0}000", $input->getControl()->__toString());
		Assert::same(1000000, $input->getValue());
	}


	public function testValueWithSeparatorsIsParsed()
	{
		$form = $this->getForm();

		$input = $form->addCurrency('price', 'Price');

		foreach(["1\u{00A0}000\u{00A0}000", '1 000 000', '1000000'] as $raw)
		{
			$input->setValue($raw);

			Assert::same(1000000, $input->getValue(), 'raw value "' . $raw . '"');
		}
	}


	public function testEmptyValue()
	{
		$form = $this->getForm();

		$input = $form->addCurrency('price', 'Price');

		Assert::null($input->getValue());

		$input->setValue('');

		Assert::null($input->getValue());
	}
}

(new CurrencyTest)->run();

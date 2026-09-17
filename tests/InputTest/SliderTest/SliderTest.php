<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use ModulIS\Form\Enum\RenderType;
use Tester\Assert;

class SliderTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addSlider('slider', 'Slider', 0, 100, 5);

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('slider')->render()->__toString());
	}


	public function testRenderItems()
	{
		$form = $this->getForm();

		$form->addSlider('slider', 'Slider')
			->setItems([2020, 2021, 2022]);

		$this->assertRender(__DIR__ . '/items.latte', $form->getComponent('slider')->render()->__toString());
	}


	public function testRenderRange()
	{
		$form = $this->getForm();

		$form->addSlider('slider', 'Slider', 0, 100, 10)
			->setRange()
			->setValue([20, 60]);

		$this->assertRender(__DIR__ . '/range.latte', $form->getComponent('slider')->render()->__toString());
	}


	public function testRenderWithoutScale()
	{
		$form = $this->getForm();

		$form->addSlider('slider', 'Slider', 0, 100, 5)
			->showScale(false)
			->showLabels(false)
			->showTooltip(false);

		$this->assertRender(__DIR__ . '/noScale.latte', $form->getComponent('slider')->render()->__toString());
	}


	public function testRenderInputFloatingLabel()
	{
		$form = $this->getForm();

		$form->addSlider('slider', 'Slider', 0, 100, 5)
			->setRenderFloating();

		$this->assertRender(__DIR__ . '/floatingLabel.latte', $form->getComponent('slider')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addSlider('slider', 'Slider', 0, 100, 5)
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('slider')->render()->__toString());
	}


	public function testSingleValue()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('slider', 'Slider', 0, 100, 5);

		Assert::null($slider->getValue());

		$slider->setValue(35);

		Assert::same(35, $slider->getValue());
	}


	public function testRangeValue()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('slider', 'Slider', 0, 100, 10)
			->setRange();

		$slider->setValue([20, 60]);

		Assert::same([20, 60], $slider->getValue());
	}


	public function testFloatValue()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('slider', 'Slider', 0, 1, 0.1);

		$slider->setValue(0.5);

		Assert::same(0.5, $slider->getValue());
	}


	public function testMissingValuesThrows()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('slider', 'Slider');

		Assert::exception(
			fn() => $slider->getControl(),
			\Nette\InvalidStateException::class,
			'Call setMinMax() or setItems() before rendering input "slider"'
		);
	}


	public function testInvalidRangeThrows()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('slider', 'Slider');

		Assert::exception(
			fn() => $slider->setMinMax(10, 10),
			\Nette\InvalidArgumentException::class
		);

		Assert::exception(
			fn() => $slider->setMinMax(0, 10, 0),
			\Nette\InvalidArgumentException::class
		);

		Assert::exception(
			fn() => $slider->setItems([1]),
			\Nette\InvalidArgumentException::class
		);
	}
}

(new SliderTest)->run();

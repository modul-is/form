<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class CheckboxlistTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second']);

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderItemsPerRow()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setItemsPerRow(2);

		$this->assertRender(__DIR__ . '/itemsPerRow.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderFloating()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderFloating();

		$this->assertRender(__DIR__ . '/floating.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderInline()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderInline();

		$this->assertRender(__DIR__ . '/inline.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderCompact()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderCompact();

		$this->assertRender(__DIR__ . '/compact.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderCompactWrapClasses()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderCompact()
			->setLabelWrapClass('labelWrap')
			->setInputWrapClass('inputWrap');

		$this->assertRender(__DIR__ . '/compactWrap.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderBig()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderBig();

		$this->assertRender(__DIR__ . '/big.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderBigWithIconsAndTooltips()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderBig()
			->setIconArray(['first' => 'user'])
			->setTooltips(['first' => 'Description']);

		$this->assertRender(__DIR__ . '/bigIcons.latte', $form->getComponent('checkboxlist')->render()->__toString());
	}


	/**
	 * setInputWrapClass() se v Big rezimu lepi na blok dlazdic - drive chybela mezera.
	 */
	public function testRenderBigInputWrapClass()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setRenderBig()
			->setInputWrapClass('col-6');

		Assert::contains('mis-tiles col-6', $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderTypesAreIdempotent()
	{
		foreach(\ModulIS\Form\Enum\RenderListType::cases() as $renderType)
		{
			$form = $this->getForm();

			$input = $form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
				->setRenderType($renderType);

			Assert::same(
				$input->render()->__toString(),
				$input->render()->__toString(),
				'render type ' . $renderType->name
			);
		}
	}


	public function testWrapIdInAllRenderTypes()
	{
		foreach(\ModulIS\Form\Enum\RenderListType::cases() as $renderType)
		{
			$form = $this->getForm();

			$input = $form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
				->setRenderType($renderType)
				->setOption('id', 'optionId');

			Assert::contains('optionId', $input->render()->__toString(), 'render type ' . $renderType->name);
		}
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}
}

$testcase = new CheckboxlistTest;
$testcase->run();

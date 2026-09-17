<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use ModulIS\Form\Enum\RenderListType;
use ModulIS\Form\Enum\RenderType;
use Tester\Assert;

/**
 * Validacni trida se bez skutecnych HTTP dat neaktivuje - Form::isSubmitted() vola
 * getHttpData() a ten submittedBy zahodi. Testovaci potomci ji proto vnuti primo,
 * aby slo overit, kam se ve vykreslenem HTML dostane.
 */
class ValidatedTextInput extends \ModulIS\Form\Control\TextInput
{
	protected function getValidationClass(): ?string
	{
		return 'is-invalid';
	}
}

class ValidatedCheckboxList extends \ModulIS\Form\Control\CheckboxList
{
	protected function getValidationClass(): ?string
	{
		return 'is-invalid';
	}
}

class ValidatedRadioList extends \ModulIS\Form\Control\RadioList
{
	protected function getValidationClass(): ?string
	{
		return 'is-invalid';
	}
}

class ValidationClassTest extends TestCase
{
	/**
	 * Trida se nesmi slepit s okolni tridou dohromady (drive napr. "mis-tiles-titleis-invalid").
	 */
	private function assertNoGluedClass(string $html): void
	{
		preg_match_all('~class="([^"]*)"~', $html, $matches);

		foreach($matches[1] as $class)
		{
			Assert::false(
				(bool) preg_match('~[a-z]is-(in)?valid~', $class),
				'glued validation class in "' . $class . '"'
			);

			Assert::false(str_contains($class, '  '), 'double space in "' . $class . '"');
			Assert::same(trim($class), $class, 'untrimmed class "' . $class . '"');
		}
	}


	public function testTextInputRenderTypes()
	{
		foreach(RenderType::cases() as $renderType)
		{
			$form = $this->getForm();
			$form->setRenderType($renderType);

			$input = $form->addComponent(new ValidatedTextInput('Text'), 'text')
				->getComponent('text');

			$html = $input->render()->__toString();

			Assert::contains('is-invalid', $html, 'render type ' . $renderType->name);
			$this->assertNoGluedClass($html);
		}
	}


	public function testCheckboxListRenderTypes()
	{
		foreach(RenderListType::cases() as $renderType)
		{
			$form = $this->getForm();

			$input = $form->addComponent(new ValidatedCheckboxList('Checklist', ['first' => 'First']), 'list')
				->getComponent('list');

			$input->setRenderType($renderType);

			$this->assertNoGluedClass($input->render()->__toString());
		}
	}


	public function testRadioListRenderTypes()
	{
		foreach(RenderListType::cases() as $renderType)
		{
			$form = $this->getForm();

			$input = $form->addComponent(new ValidatedRadioList('Radio', ['first' => 'First']), 'list')
				->getComponent('list');

			$input->setRenderType($renderType);

			$this->assertNoGluedClass($input->render()->__toString());
		}
	}


	/**
	 * Regrese - setInputWrapClass() se v Big rezimu lepilo bez mezery.
	 */
	public function testBigTitleKeepsValidationClassSeparate()
	{
		$form = $this->getForm();

		$input = $form->addComponent(new ValidatedCheckboxList('Checklist', ['first' => 'First']), 'list')
			->getComponent('list');

		$input->setRenderBig();

		Assert::contains('class="mis-tiles-title is-invalid"', $input->render()->__toString());
	}
}

(new ValidationClassTest)->run();

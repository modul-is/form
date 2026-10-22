<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;

/**
 * Inputs of Bootstrap 6 added on top of the basic ones - color, month, datalist, plaintext, sizes,
 * password strength, OTP, chips, range
 */
class Bootstrap6InputsTest extends TestCase
{
	public function testColor()
	{
		$form = $this->getForm();

		$color = $form->addColor('color', 'Color')
			->setDefaultValue('#631AFF')
			->setRenderFloating();

		$html = (string) $color->render();

		Assert::same('#631aff', $color->getValue());
		Assert::contains('class="form-control form-control-color"', $html);
		Assert::contains('<label for="frm-form-color" class="form-label">Color</label>', $html, 'floating is not supported - default render');
	}


	public function testMonth()
	{
		$form = $this->getForm();

		$month = $form->addMonth('month', 'Month');

		Assert::contains('type="month"', (string) $month->render());
		Assert::null($month->getValue());
	}


	public function testSuggestions()
	{
		$form = $this->getForm();

		$form->addText('city', 'City')
			->setSuggestions([1 => 'Praha', 2 => 'Brno']);

		$form->addText('noCaret', 'City')
			->setSuggestions(['Praha'], false);

		$html = (string) $form->getComponent('city')->render();

		Assert::contains('class="form-control form-control-caret" list="frm-form-city-suggestions"', $html);
		Assert::contains('<datalist id="frm-form-city-suggestions"><option value="Praha"></option><option value="Brno"></option></datalist>', $html);
		Assert::notContains('form-control-caret', (string) $form->getComponent('noCaret')->render());
	}


	public function testPlaintext()
	{
		$form = $this->getForm();

		$form->addText('text', 'Text')
			->setDefaultValue('value')
			->setPlaintext();

		Assert::contains('class="form-control-plaintext" readonly', (string) $form->getComponent('text')->render());
	}


	public function testSize()
	{
		$form = $this->getForm();
		$form->setSize('sm');

		$form->addText('plain', 'Plain');
		$form->addText('group', 'Group')->setPrepend('@');
		$form->addText('icon', 'Icon')->setIcon('search');
		$form->addText('large', 'Large')->setSize('lg');
		$form->addCheckbox('check', 'Check');
		$form->addCheckbox('switch', 'Switch')->setSwitch();
		$form->addRadioList('radio', 'Radio', ['a' => 'A']);
		$form->addButton('button', 'Button');

		Assert::contains('class="form-control form-control-sm"', (string) $form->getComponent('plain')->render());
		Assert::contains('<div class="input-group input-group-sm">', (string) $form->getComponent('group')->render());
		Assert::contains('<label class="form-control form-adorn form-control-sm">', (string) $form->getComponent('icon')->render());
		Assert::contains('form-control-lg', (string) $form->getComponent('large')->render());
		Assert::contains('class="check check-sm"', (string) $form->getComponent('check')->render());
		Assert::contains('<div class="switch switch-sm">', (string) $form->getComponent('switch')->render());
		Assert::contains('class="radio radio-sm"', (string) $form->getComponent('radio')->render());
		Assert::contains('class="btn-solid theme-secondary btn-sm"', (string) $form->getComponent('button')->render());

		Assert::exception(fn() => $form->addText('wrong')->setSize('xl'), \Nette\InvalidArgumentException::class);
	}


	public function testInlineLabelSize()
	{
		$form = $this->getForm();
		$form->setRenderInline();

		$form->addText('text', 'Text')->setSize('lg');

		Assert::contains('class="col-form-label col-form-label-lg sm:col-4"', (string) $form->getComponent('text')->render());
	}


	public function testCheckboxThemeAndIndeterminate()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setColor('success')
			->setIndeterminate();

		$form->addCheckbox('switch', 'Switch')
			->setSwitch()
			->setColor('danger');

		Assert::contains('class="check theme-success" data-mis-indeterminate', (string) $form->getComponent('check')->render());
		Assert::contains('<div class="switch theme-danger">', (string) $form->getComponent('switch')->render());
	}


	public function testStrengthMeter()
	{
		$form = $this->getForm();

		$form->addPassword('password', 'Password')
			->setStrengthMeter(minLength: 10);

		$html = (string) $form->getComponent('password')->render();

		Assert::match('%A%<div><div class="strength" data-bs-strength data-bs-input="#frm-form-password" data-bs-min-length="10" data-bs-messages=\'{"weak":"Slabé heslo",%A%\'><div class="strength-segment"></div>%A%</div><small class="strength-text form-text"></small></div>%A%', $html);
	}


	public function testOtp()
	{
		$form = $this->getForm();

		$otp = $form->addOtp('otp', 'Code', 4)
			->setType('alphanumeric')
			->setMask()
			->setGroups([2, 2]);

		$html = (string) $otp->render();

		Assert::contains('<div class="otp" data-bs-otp data-bs-type="alphanumeric" data-bs-mask="true" data-bs-groups="[2,2]"><input type="text" name="otp" maxlength="4" pattern="[A-Za-z0-9]{4}" id="frm-form-otp"', $html);
		Assert::contains('class="otp-input"', $html);
		Assert::contains('"arg":"[A-Za-z0-9]{4}"', $html, 'pattern follows the type');

		$otp->setValue('ab1');
		$otp->validate();
		Assert::same(['Zadejte celý kód.'], $otp->getErrors());

		Assert::exception(fn() => $form->addOtp('wrong', null, 0), \Nette\InvalidArgumentException::class);
		Assert::exception(fn() => $otp->setType('hex'), \Nette\InvalidArgumentException::class);
	}


	public function testChips()
	{
		$form = $this->getForm();

		$chips = $form->addChips('tags', 'Tags')
			->setPlaceholder('Add')
			->setMaxChips(3)
			->setValue(['a', ' b ', 'a', '', 'c', 'd']);

		Assert::same(['a', 'b', 'c'], $chips->getValue(), 'trimmed, without duplicates and empty values, limited');

		$chips->setValue('x,y');
		Assert::same(['x', 'y'], $chips->getValue());

		$html = (string) $chips->render();

		Assert::contains('<label for="frm-form-tags" class="form-label">Tags</label>', $html);
		Assert::contains('<div class="chip-input" data-bs-chips data-bs-separator="," data-bs-max-chips="3" data-mis-chips="frm-form-tags-values"><span class="chip" data-bs-chip-value="x">x</span>', $html);
		Assert::contains('<input type="text" id="frm-form-tags" class="form-ghost" placeholder="Add" data-mis-proxy>', $html);
		Assert::contains('<select multiple hidden id="frm-form-tags-values" name="tags[]"><option value="x" selected>x</option><option value="y" selected>y</option></select>', $html);
	}


	public function testRangeItems()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('size', 'Size')
			->setItems(['S', 'M', 'L'])
			->setValue('L');

		$html = (string) $slider->render();

		Assert::contains('data-mis-range-items=\'["S","M","L"]\'', $html);
		Assert::contains('value="2" min="0" max="2" step="1"', $html);
		Assert::contains('<option value="1" label="M"></option>', $html);
	}


	public function testRangeManyStepsHasTenTicks()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('percent', 'Percent', 0, 100);

		$ticks = $slider->getTicks();

		Assert::count(11, $ticks);
		Assert::same(['value' => 50, 'label' => '50'], $ticks[5]);
	}
}

(new Bootstrap6InputsTest)->run();

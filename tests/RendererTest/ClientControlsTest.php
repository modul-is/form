<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;

/**
 * Markup for the client side of the form (assets/js) - combobox, autocomplete, datepicker, slider with two handles
 */
class ClientControlsTest extends TestCase
{
	public function testSelectWithSearchIsCombobox()
	{
		$form = $this->getForm();

		$form->addSelect('plain', 'Plain', ['a' => 'A']);

		$form->addSelect('search', 'Search', ['a' => 'A'])
			->setPrompt('Choose')
			->setSearch()
			->setNoResultMessage('Nothing');

		$form->addMultiSelect('multi', 'Multi', ['a' => 'A'])
			->setSearch();

		Assert::notContains('data-mis-combobox', (string) $form->getComponent('plain')->render());
		Assert::contains('data-mis-combobox=\'{"search":true,"placeholder":"Choose","noResults":"Nothing"}\'', (string) $form->getComponent('search')->render());
		Assert::contains('data-mis-combobox=\'{"search":true}\'', (string) $form->getComponent('multi')->render());
	}


	public function testSelectImages()
	{
		$form = $this->getForm();

		$form->addSelect('select', 'Select', ['a' => 'A', 'b' => 'B'])
			->setImageArray(['a' => '/a.png']);

		$html = (string) $form->getComponent('select')->render();

		Assert::contains('data-mis-combobox=\'{"search":false}\'', $html);
		Assert::contains('<option value="a" data-mis-image="/a.png">A</option><option value="b">B</option>', $html);
	}


	public function testComboboxIconGoesToInputGroup()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A'])
			->setIcon('search');

		$html = (string) $form->getComponent('whisperer')->render();

		Assert::contains('<div class="input-group"><span class="input-group-text"><i class="bi bi-search"', $html);
		Assert::notContains('form-adorn', $html);
	}


	public function testWhispererDivider()
	{
		$form = $this->getForm();

		$form->addWhisperer('whisperer', 'Whisperer', ['a' => 'A', 'b' => 'B'])
			->setDividerValue('a');

		Assert::contains('<option data-mis-divider value="a">A</option>', (string) $form->getComponent('whisperer')->render());
	}


	public function testAutocompleteStaticSuggestions()
	{
		$form = $this->getForm();

		$form->addAutocomplete('city', 'City', null, [1 => 'Praha', 2 => 'Brno'])
			->setPrompt('Type');

		$html = (string) $form->getComponent('city')->render();

		Assert::contains('autocomplete="off"', $html);
		Assert::contains('placeholder="Type"', $html);
		Assert::contains('list="frm-form-city-suggestions"', $html);
		Assert::notContains('form-control-caret', $html);
		Assert::contains('<datalist id="frm-form-city-suggestions"><option value="Praha" data-key="1"></option><option value="Brno" data-key="2"></option></datalist>', $html);
	}


	public function testAutocompleteRemoteHasEmptyDatalist()
	{
		$form = $this->getForm();

		$form->addAutocomplete('city', 'City')
			->setOnSearchChangeCallback(fn(mixed $query, array $parents): array => []);

		$html = (string) $form->getComponent('city')->render();

		Assert::contains('data-autocomplete=', $html);
		Assert::contains('<datalist id="frm-form-city-suggestions"></datalist>', $html);
	}


	public function testPrefixSuffix()
	{
		$form = $this->getForm();

		$form->addText('price', 'Price')
			->setIcon('cash')
			->setPrefix('$')
			->setSuffix('USD');

		$form->addText('floating', 'Floating')
			->setSuffix('km')
			->setRenderFloating();

		Assert::contains('<label class="form-control form-adorn"><span class="form-adorn-icon" aria-hidden="true"><i class="bi bi-cash" aria-hidden="true"></i></span><span class="form-adorn-text" aria-hidden="true">$</span><input type="text" name="price" id="frm-form-price" class="form-ghost"><span class="form-adorn-text" aria-hidden="true">USD</span></label>', (string) $form->getComponent('price')->render());
		Assert::contains('</div><span class="input-group-text">km</span></div>', (string) $form->getComponent('floating')->render(), 'floating has no adorn - suffix goes to the input group');
	}


	public function testCurrencyIsSuffix()
	{
		$form = $this->getForm();

		$form->addCurrency('price', 'Price', 'EUR');
		$form->addCurrency('withAppend', 'Price', 'EUR')->setAppend('/ month');

		Assert::contains('<span class="form-adorn-text" aria-hidden="true">EUR</span>', (string) $form->getComponent('price')->render());
		Assert::notContains('EUR', (string) $form->getComponent('withAppend')->render(), 'own append - no currency inside');
	}


	public function testDatepicker()
	{
		$form = $this->getForm();

		$date = $form->addDate('date', 'Date')
			->setPicker()
			->addRule($form::Min, 'Too early', new \DateTimeImmutable('2026-01-01'))
			->setDefaultValue(new \DateTimeImmutable('2026-05-10'));

		$html = (string) $date->render();

		Assert::contains('<label for="frm-form-date" class="form-label">Date</label>', $html);
		Assert::contains('<input type="text" id="frm-form-date" class="form-control" placeholder="Vyberte datum" readonly autocomplete="off" data-bs-toggle="datepicker" data-bs-date-min="2026-01-01" data-bs-selected-dates=\'["2026-05-10"]\' data-mis-datepicker="frm-form-date-value" data-mis-proxy>', $html);
		Assert::match('%A%<input type="date" name="date" id="frm-form-date-value"%A%value="2026-05-10"%A%hidden>%A%', $html);

		Assert::exception(fn() => $form->addTime('time', 'Time')->setPicker(), \Nette\InvalidStateException::class);
	}


	public function testSliderRange()
	{
		$form = $this->getForm();

		$slider = $form->addSlider('size', 'Size')
			->setItems(['S', 'M', 'L', 'XL'])
			->setRange()
			->setValue(['M', 'XL']);

		$html = (string) $slider->render();

		Assert::same([1, 3], $slider->getRangePositions());
		Assert::contains('<div class="form-range mis-range-dual" data-mis-range-items=\'["S","M","L","XL"]\' data-mis-range-dual data-mis-bubble>', $html);
		Assert::contains('value="1" list="frm-form-size-ticks" aria-label="Size – Od" data-mis-range-part="from"', $html);
		Assert::contains('value="3" aria-label="Size – Do" data-mis-range-part="to"', $html);
		Assert::contains('<input type="hidden" name="size" id="frm-form-size" value="M,XL">', $html);
	}
}

(new ClientControlsTest)->run();

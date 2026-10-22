<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\DateTimeControl;
use Nette\Utils\Html;
use Stringable;

/**
 * Factories of all controls - shared by Form and Container
 */
trait ControlFactories
{
	/** @var array<string, Html|string> */
	private array $dividerArray = [];


	public function addHidden(string $name, mixed $default = null): Control\Hidden
	{
		return $this[$name] = (new Control\Hidden)
			->setDefaultValue($default);
	}


	public function addText(string $name, null|string|Stringable $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label, $maxLength)
			->setHtmlAttribute('size', $cols);
	}


	public function addFloat(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label)
			->setNullable()
			->setHtmlType('number')
			->setHtmlAttribute('step', 'any')
			->addRule(Form::Float);
	}


	/**
	 * @param ?array<mixed> $itemArray
	 */
	public function addAutocomplete(string $name, null|string|Stringable $label = null, ?int $maxLength = null, ?array $itemArray = []): Control\AutocompleteInput
	{
		return $this[$name] = new Control\AutocompleteInput($label, $maxLength, items: $itemArray ?? [])
			->setHtmlAttribute('autocomplete', 'off');
	}


	public function addPassword(string $name, null|string|Stringable $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label, $maxLength)
			->setHtmlAttribute('size', $cols)
			->setHtmlType('password');
	}


	public function addTextArea(string $name, null|string|Stringable $label = null, ?int $cols = null, ?int $rows = null): Control\TextArea
	{
		return $this[$name] = new Control\TextArea($label)
			->setHtmlAttribute('cols', $cols)
			->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, string|Stringable|null $label = null, int $maxLength = 255): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label, $maxLength)
			->setRequired(false)
			->addRule(Form::Email);
	}


	public function addInteger(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label)
			->setNullable()
			->setRequired(false)
			->addRule(Form::Integer);
	}


	public function addCurrency(string $name, null|string|Stringable $label = null, ?string $currency = null): Control\CurrencyInput
	{
		$input = new Control\CurrencyInput($label);

		if($currency !== null)
		{
			$input->setCurrency($currency);
		}

		return $this[$name] = $input;
	}


	public function addDate(string $name, null|string|Stringable $label = null): Control\DateTimeInput
	{
		$dateInput = new Control\DateTimeInput($label, DateTimeControl::TypeDate);

		return $this[$name] = $dateInput->setRequired(false)
			->setFormat('Y-m-d');
	}


	public function addDateTime(string $name, null|string|Stringable $label = null, bool $withSeconds = false): Control\DateTimeInput
	{
		$dateInput = new Control\DateTimeInput($label, DateTimeControl::TypeDateTime, $withSeconds);

		return $this[$name] = $dateInput->setRequired(false)
			->setFormat('Y-m-d H:i:s');
	}


	public function addDateWeek(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label)
			->setHtmlAttribute('type', 'week');
	}


	/**
	 * Month as a string in Y-m format (e.g. 2026-10)
	 */
	public function addMonth(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label)
			->setHtmlType('month')
			->setNullable()
			->addRule(Form::Pattern, 'Zadejte měsíc ve formátu RRRR-MM.', '\d{4}-(0[1-9]|1[0-2])');
	}


	/**
	 * Color in #rrggbb format
	 */
	public function addColor(string $name, null|string|Stringable $label = null): Control\ColorPicker
	{
		return $this[$name] = new Control\ColorPicker($label);
	}


	/**
	 * One-time code of the given length, digits only by default (see OtpInput::setType())
	 */
	public function addOtp(string $name, null|string|Stringable $label = null, int $length = 6): Control\OtpInput
	{
		return $this[$name] = new Control\OtpInput($label, $length);
	}


	/**
	 * List of free text values entered as chips (tags) - value is a list of strings
	 */
	public function addChips(string $name, null|string|Stringable $label = null): Control\ChipsInput
	{
		return $this[$name] = new Control\ChipsInput($label);
	}


	public function addTime(string $name, null|string|Stringable $label = null, bool $withSeconds = false): Control\DateTimeInput
	{
		return $this[$name] = new Control\DateTimeInput($label, DateTimeControl::TypeTime, $withSeconds)
			->setFormat($withSeconds ? 'H:i:s' : 'H:i');
	}


	/**
	 * Slider - value range is defined by setMinMax() or setItems()
	 */
	public function addSlider(string $name, null|string|Stringable $label = null, int|float|null $min = null, int|float|null $max = null, int|float $step = 1): Control\SliderInput
	{
		$input = new Control\SliderInput($label);

		if($min !== null && $max !== null)
		{
			$input->setMinMax($min, $max, $step);
		}

		return $this[$name] = $input;
	}


	public function addUpload(string $name, null|string|Stringable $label = null, bool $multiple = false): Control\UploadControl
	{
		return $this[$name] = new Control\UploadControl($label, $multiple);
	}


	public function addMultiUpload(string $name, null|string|Stringable $label = null): Control\UploadControl
	{
		return $this[$name] = new Control\UploadControl($label, true);
	}


	public function addCheckbox(string $name, null|string|Stringable $caption = null): Control\Checkbox
	{
		return $this[$name] = new Control\Checkbox($caption);
	}


	/**
	 * @param ?array<mixed> $items
	 */
	public function addRadioList(string $name, null|string|Stringable $label = null, ?array $items = null): Control\RadioList
	{
		return $this[$name] = new Control\RadioList($label, $items);
	}


	/**
	 * @param ?array<mixed> $items
	 */
	public function addCheckboxList(string $name, null|string|Stringable $label = null, ?array $items = null): Control\CheckboxList
	{
		return $this[$name] = new Control\CheckboxList($label, $items);
	}


	/**
	 * @param ?array<mixed> $items
	 */
	public function addSelect(string $name, null|string|Stringable $label = null, ?array $items = null, ?int $size = null): Control\SelectBox
	{
		return $this[$name] = new Control\SelectBox($label, $items)
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	/**
	 * @param ?array<mixed> $items
	 */
	public function addMultiSelect(string $name, null|string|Stringable $label = null, ?array $items = null, ?int $size = null): Control\MultiSelectBox
	{
		return $this[$name] = new Control\MultiSelectBox($label, $items)
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	/**
	 * @param ?(\Closure(Control\SubmitButton, array<mixed>|object): void|\Closure(array<mixed>|object): void) $onSubmit
	 */
	public function addSubmit(string $name, Stringable|string|null $caption = null, ?\Closure $onSubmit = null): Control\SubmitButton
	{
		$control = new Control\SubmitButton($caption)
			->setIcon('save')
			->setColor('success');

		if($onSubmit !== null)
		{
			$control->onClick[] = $onSubmit;
		}

		return $this[$name] = $control;
	}


	public function addButton(string $name, null|string|Stringable $caption = ''): Control\Button
	{
		return $this[$name] = new Control\Button($caption);
	}


	public function addLink(string $name, null|string|Stringable $caption = ''): Control\Link
	{
		return $this[$name] = new Control\Link($caption);
	}


	/**
	 * @param array<BaseControl> $parents
	 */
	public function addDependentSelect(string $name, null|string|Stringable $label = null, array $parents = [], ?callable $dependentCallback = null): Control\DependentSelect
	{
		return $this[$name] = new Control\DependentSelect($label, $parents, $dependentCallback);
	}


	/**
	 * @param array<BaseControl> $parents
	 */
	public function addDependentMultiSelect(string $name, null|string|Stringable $label = null, array $parents = [], ?callable $dependentCallback = null): Control\DependentMultiSelect
	{
		return $this[$name] = new Control\DependentMultiSelect($label, $parents, $dependentCallback);
	}


	/**
	 * @param callable(DuplicatorContainer): void $factory
	 */
	public function addDuplicator(string $name, callable $factory, int $copyNumber = 1, bool $forceDefault = false): Control\Duplicator
	{
		$duplicator = new Control\Duplicator($factory, $copyNumber, $forceDefault);

		$duplicator->setCurrentGroup($this->getCurrentGroup());

		return $this[$name] = $duplicator;
	}


	/**
	 * @param array<mixed> $items
	 */
	public function addWhisperer(string $name, null|string|Stringable $label = null, array $items = []): Control\Whisperer
	{
		return $this[$name] = new Control\Whisperer($label, isset($items['']) ? $items : ['' => ''] + $items)
			->setHtmlAttribute('data-placeholder', 'Vyberte')
			->checkDefaultValue(false);
	}


	/**
	 * @param array<mixed> $items
	 */
	public function addMultiWhisperer(string $name, null|string|Stringable $label = null, array $items = []): Control\MultiWhisperer
	{
		return $this[$name] = new Control\MultiWhisperer($label, isset($items['']) ? $items : ['' => ''] + $items)
			->setHtmlAttribute('data-placeholder', 'Vyberte');
	}


	public function addDivider(Html|string $content, ?string $previousControl = null): void
	{
		if(!$previousControl)
		{
			$controlArray = iterator_to_array($this->getControls());
			$lastControl = end($controlArray);

			assert($lastControl instanceof BaseControl || $lastControl === false);

			$previousControl = $lastControl ? $lastControl->getName() : null;
		}

		$this->dividerArray[$previousControl] = $content;
	}


	/**
	 * Content rendered after the given control
	 */
	public function getDivider(string $controlName): Html|string|null
	{
		return $this->dividerArray[$controlName] ?? null;
	}


	public function addContainer(string|int $name): Container
	{
		$control = new Container;

		$control->currentGroup = $this->currentGroup;

		if($this->currentGroup !== null)
		{
			$this->currentGroup->add($control);
		}

		return $this[(string) $name] = $control;
	}
}

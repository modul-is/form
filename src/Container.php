<?php

declare(strict_types=1);

namespace ModulIS\Form;


class Container extends \Nette\Forms\Container
{
	public string $color = 'white';


	public function setColor(string $color): self
	{
		$this->color = $color;
		return $this;
	}


	public function addText(string $name, $label = null, ?int $cols = null, ?int $maxLength = null): TextInput
	{
		return $this[$name] = (new TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols);
	}


	public function addPassword(string $name, $label = null, ?int $cols = null, ?int $maxLength = null): TextInput
	{
		return $this[$name] = (new TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols)
			->setHtmlType('password');
	}


	public function addTextArea(string $name, $label = null, ?int $cols = null, ?int $rows = null): TextArea
	{
		return $this[$name] = (new TextArea($label))
			->setHtmlAttribute('cols', $cols)
			->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, $label = null): TextInput
	{
		return $this[$name] = (new TextInput($label))
			->setRequired(false)
			->addRule(Form::EMAIL);
	}


	public function addInteger(string $name, $label = null): TextInput
	{
		return $this[$name] = (new TextInput($label))
			->setNullable()
			->setRequired(false)
			->addRule(Form::INTEGER);
	}


	public function addUpload(string $name, $label = null, $multiple = false): UploadControl
	{
		return $this[$name] = new UploadControl($label, $multiple);
	}


	public function addMultiUpload(string $name, $label = null): UploadControl
	{
		return $this[$name] = new UploadControl($label, true);
	}


	public function addCheckbox(string $name, $caption = null): Checkbox
	{
		return $this[$name] = new Checkbox($caption);
	}


	public function addRadioList(string $name, $label = null, array $items = null): RadioList
	{
		return $this[$name] = new RadioList($label, $items);
	}


	public function addCheckboxList(string $name, $label = null, array $items = null): CheckboxList
	{
		return $this[$name] = new CheckboxList($label, $items);
	}


	public function addSelect(string $name, $label = null, array $items = null, $size = null): SelectBox
	{
		return $this[$name] = (new SelectBox($label, $items))
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	public function addMultiSelect(string $name, $label = null, array $items = null, $size = null): MultiSelectBox
	{
		return $this[$name] = (new MultiSelectBox($label, $items))
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	public function addSubmit(string $name, $caption = null): SubmitButton
	{
		return $this[$name] = new SubmitButton($caption);
	}


	public function addButton(string $name, $caption = null): Button
	{
		return $this[$name] = new Button($caption);
	}


	public function addLink(string $name, string $caption = null): Link
	{
		return $this[$name] = new Link($caption);
	}


	public function addDependentSelect(string $name, string $label = null, array $parents = [], callable $dependentCallback = null): DependentSelect
	{
		return $this[$name] = new DependentSelect($label, $parents, $dependentCallback);
	}


	public function addDependentMultiSelect(string $name, string $label = null, array $parents = [], callable $dependentCallback = null): DependentMultiSelect
	{
		return $this[$name] = new DependentMultiSelect($label, $parents, $dependentCallback);
	}


	public function addDuplicator($name, $factory, $copyNumber = 1, $maxCopies = null): Duplicator
	{
		$duplicator = new Duplicator($factory, $copyNumber, $maxCopies);

		$duplicator->setCurrentGroup($this->getCurrentGroup());

		return $this[$name] = $duplicator;
	}


	public function addWhisperer(string $name, string $label = null, array $items = []): Whisperer
	{
		$itemArray = is_callable($items) ? [] : $items;

		$whisperer = (new Whisperer($label, isset($itemArray['']) ? $itemArray : ['' => ''] + $itemArray))
			->setAttribute('data-placeholder', 'Vyberte')
			->checkDefaultValue(false);

		if(is_callable($items))
		{
			$whisperer->setOnChangeCallback($items);
		}

		return $this[$name] = $whisperer;
	}


	public function addContainer($name): \Nette\Forms\Container
	{
		$control = new self;

		$control->currentGroup = $this->currentGroup;

		if($this->currentGroup !== null)
		{
			$this->currentGroup->add($control);
		}

		return $this[$name] = $control;
	}
}

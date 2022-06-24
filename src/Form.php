<?php

declare(strict_types=1);

namespace ModulIS\Form;

class Form extends \Nette\Application\UI\Form
{
	public const GREATER_EQUAL = \Nette\Application\UI\Form::MIN;

	public const LESS_EQUAL = \Nette\Application\UI\Form::MAX;

	public const GREATER = '\ModulIS\Form\FormValidator::greater';

	public const LESS = 'ModulIS\Form\FormValidator::less';

	public const SAME_LENGTH = 'ModulIS\Form\FormValidator::sameLength';

	public ?string $color = null;

	public bool $ajax = false;

	public ?string $title = null;

	public ?string $icon = null;

	public bool $noValidate = true;

	public bool $floatingLabel = false;

	protected array $boxes = [];

	protected string|int $boxCurrent;


	public function __construct(\Nette\ComponentModel\IContainer $parent = null, $name = null)
	{
		parent::__construct($parent, $name);

		$this->addBox();
	}


	public function getSubmitterArray(): array
	{
		$submitterArray = [];

		foreach($this->getBoxes() as $box)
		{
			$submitterArray = array_merge($submitterArray, $box->getSubmitterArray());
		}

		return $submitterArray;
	}


	public function setColor(string $color): self
	{
		$this->color = $color;
		return $this;
	}


	public function addHidden(string $name, $default = null): Control\Hidden
	{
		return $this[$name] = (new Control\Hidden)
			->setDefaultValue($default);
	}


	public function addText(string $name, $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols);
	}


	public function addDate(string $name, $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\DateInput($label))
			->setRequired(false)
			->addRule(fn($input) => \Nette\Utils\DateTime::createFromFormat('Y-m-d', $input->getValue()), 'Vložte datum ve formátu dd.mm.yyyy');
	}


	public function addPassword(string $name, $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols)
			->setHtmlType('password');
	}


	public function addTextArea(string $name, $label = null, ?int $cols = null, ?int $rows = null): Control\TextArea
	{
		return $this[$name] = (new Control\TextArea($label))
			->setHtmlAttribute('cols', $cols)->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setRequired(false)
			->addRule(self::EMAIL);
	}


	public function addInteger(string $name, $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setNullable()
			->setRequired(false)
			->addRule(self::INTEGER);
	}


	public function addUpload(string $name, $label = null, $multiple = false): Control\UploadControl
	{
		return $this[$name] = new Control\UploadControl($label, $multiple);
	}


	public function addMultiUpload(string $name, $label = null): Control\UploadControl
	{
		return $this[$name] = new Control\UploadControl($label, true);
	}


	public function addCheckbox(string $name, $caption = null): Control\Checkbox
	{
		return $this[$name] = new Control\Checkbox($caption);
	}


	public function addRadioList(string $name, $label = null, array $items = null): Control\RadioList
	{
		return $this[$name] = new Control\RadioList($label, $items);
	}


	public function addCheckboxList(string $name, $label = null, array $items = null): Control\CheckboxList
	{
		return $this[$name] = new Control\CheckboxList($label, $items);
	}


	public function addSelect(string $name, $label = null, array $items = null, $size = null): Control\SelectBox
	{
		return $this[$name] = (new Control\SelectBox($label, $items))
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	public function addMultiSelect(string $name, $label = null, array $items = null, $size = null): Control\MultiSelectBox
	{
		return $this[$name] = (new Control\MultiSelectBox($label, $items))
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	public function addSubmit(string $name, $caption = null): Control\SubmitButton
	{
		return $this[$name] = (new Control\SubmitButton($caption))
			->setIcon('save')
			->setColor('success');
	}


	public function addButton(string $name, $caption = null): Control\Button
	{
		return $this[$name] = new Control\Button($caption);
	}


	public function addLink(string $name, $caption = null): Control\Link
	{
		return $this[$name] = new Control\Link($caption);
	}


	public function addDependentSelect(string $name, $label = null, array $parents = [], callable $dependentCallback = null): Control\DependentSelect
	{
		return $this[$name] = new Control\DependentSelect($label, $parents, $dependentCallback);
	}


	public function addDependentMultiSelect(string $name, $label = null, array $parents = [], callable $dependentCallback = null): Control\DependentMultiSelect
	{
		return $this[$name] = new Control\DependentMultiSelect($label, $parents, $dependentCallback);
	}


	public function addWhisperer(string $name, $label = null, $items = []): Control\Whisperer
	{
		if(!(is_array($items) || is_callable($items)))
		{
			throw new \Exception("Parameter 'items' has to be array or callback");
		}

		$itemArray = is_callable($items) ? [] : $items;

		$whisperer = (new Control\Whisperer($label, isset($itemArray['']) ? $itemArray : ['' => ''] + $itemArray))
			->setAttribute('data-placeholder', 'Vyberte')
			->checkDefaultValue(false);

		if(is_callable($items))
		{
			$whisperer->setOnChangeCallback($items);
		}

		return $this[$name] = $whisperer;
	}


	public function addDuplicator($name, $factory, $copyNumber = 1, $forceDefault = false): Control\Duplicator
	{
		$duplicator = new Control\Duplicator($factory, $copyNumber, $forceDefault);

		$duplicator->setCurrentGroup($this->getCurrentGroup());

		return $this[$name] = $duplicator;
	}


	public function addMultiWhisperer(string $name, $label = null, array $items = null): Control\MultiWhisperer
	{
		return $this[$name] = (new Control\MultiWhisperer($label, isset($items['']) ? $items : ['' => ''] + $items))
			->setAttribute('class', 'form-control-chosen')
			->setAttribute('data-placeholder', 'Vyberte');
	}


	public function addBox(int|string $caption = 0): Box
	{
		$this->boxes[$caption] ??= new Box;

		$this->boxCurrent = $caption;

		return $this->boxes[$caption];
	}


	public function getBoxes(): array
	{
		return $this->boxes;
	}


	public function addComponent(\Nette\ComponentModel\IComponent $component, $name, $insertBefore = null): self
	{
		$this->boxes[$this->boxCurrent ?? 0]->add($component);

		parent::addComponent($component, $name, $insertBefore);

		return $this;
	}


	public function setAjax(bool $ajax = true): void
	{
		$this->ajax = $ajax;
	}


	public function setTitle(string $title): void
	{
		$this->title = $title;
	}


	public function setIcon(string $icon): void
	{
		$this->icon = $icon;
	}


	public function setNoValidate(bool $noValidate = true): void
	{
		$this->noValidate = $noValidate;
	}


	public function setFloatingLabel(bool $floatingLabel = true): void
	{
		$this->floatingLabel = $floatingLabel;
	}


	public function getFloatingLabel(): bool
	{
		return $this->floatingLabel;
	}


	public function addContainer($name): Container
	{
		$control = new Container;

		$control->currentGroup = $this->currentGroup;

		if($this->currentGroup !== null)
		{
			$this->currentGroup->add($control);
		}

		return $this[$name] = $control;
	}
}

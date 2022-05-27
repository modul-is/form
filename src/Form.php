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

	protected array $boxes = [];

	protected string|int $boxCurrent;


	public function __construct(\Nette\ComponentModel\IContainer $parent = null, $name = null)
	{
		parent::__construct($parent, $name);

		$this->addBox();
	}


	/**
	 * Call a right renderer
	 */
	public function processInput($input)
	{
		if($input->getOption('template'))
		{
			return $input->getOption('template');
		}
		elseif($input instanceof \Nette\Forms\Controls\HiddenField)
		{
			return 'templates/hidden.latte';
		}
		elseif($input instanceof Button || $input instanceof SubmitButton || $input instanceof Link)
		{
			return false;
		}
		else
		{
			return 'templates/input.latte';
		}
	}


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
			->setHtmlAttribute('cols', $cols)->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, $label = null): TextInput
	{
		return $this[$name] = (new TextInput($label))
			->setRequired(false)
			->addRule(self::EMAIL);
	}


	public function addInteger(string $name, $label = null): TextInput
	{
		return $this[$name] = (new TextInput($label))
			->setNullable()
			->setRequired(false)
			->addRule(self::INTEGER);
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
		return $this[$name] = (new SubmitButton($caption))
			->setIcon('save')
			->setColor('success');
	}


	public function addButton(string $name, $caption = null): Button
	{
		return $this[$name] = new Button($caption);
	}


	public function addLink(string $name, $caption = null): Link
	{
		return $this[$name] = new Link($caption);
	}


	public function addDependentSelect(string $name, $label = null, array $parents = [], callable $dependentCallback = null): DependentSelect
	{
		return $this[$name] = new DependentSelect($label, $parents, $dependentCallback);
	}


	public function addDependentMultiSelect(string $name, $label = null, array $parents = [], callable $dependentCallback = null): DependentMultiSelect
	{
		return $this[$name] = new DependentMultiSelect($label, $parents, $dependentCallback);
	}


	public function addWhisperer(string $name, $label = null, $items = []): Whisperer
	{
		if(!(is_array($items) || is_callable($items)))
		{
			throw new \Exception("Parameter 'items' has to be array or callback");
		}

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


	public function addDuplicator($name, $factory, $copyNumber = 1, $forceDefault = false): Duplicator
	{
		$duplicator = new Duplicator($factory, $copyNumber, $forceDefault);

		$duplicator->setCurrentGroup($this->getCurrentGroup());

		return $this[$name] = $duplicator;
	}


	public function addMultiWhisperer(string $name, $label = null, array $items = null): MultiWhisperer
	{
		return $this[$name] = (new MultiWhisperer($label, isset($items['']) ? $items : ['' => ''] + $items))
			->setAttribute('class', 'form-control-chosen')
			->setAttribute('data-placeholder', 'Vyberte');
	}


	public function addBox($caption = 0): Box
	{
		$this->boxes[$caption] ??= new Box;

		$this->boxCurrent = $caption;

		return $this->boxes[$caption];
	}


	public function getBoxes(): array
	{
		return $this->boxes;
	}


	public function addComponent(\Nette\ComponentModel\IComponent $component, $name, $insertBefore = null)
	{
		$this->boxes[$this->boxCurrent ?? 0]->add($component);

		parent::addComponent($component, $name, $insertBefore);

		return $this;
	}


	public function setAjax(): void
	{
		$this->ajax = true;
	}


	public function setTitle(string $title): void
	{
		$this->title = $title;
	}


	public function setIcon(string $icon): void
	{
		$this->icon = $icon;
	}


	public function setNoValidate(bool $noValidate): void
	{
		$this->noValidate = $noValidate;
	}


	public function addContainer($name): \Nette\Forms\Container
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

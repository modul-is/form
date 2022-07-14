<?php

declare(strict_types=1);

namespace ModulIS\Form;

use Nette\Utils\Html;
use Nette\Utils\DateTime;

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

	public bool $renderFloating = false;

	private bool $renderInline = false;

	private array $groups = [];

	private array $formErrors = [];


	public function __construct(\Nette\ComponentModel\IContainer $parent = null, $name = null)
	{
		parent::__construct($parent, $name);

		$this->addGroup();
	}


	public function renderForm()
	{
		$groups = null;
		$submitters = null;

		foreach($this->getSubmitterArray() as $submitter)
		{
			$submitters .= $submitter->render();
		}

		$cardFooter = Html::el('div')
			->class('card-footer')
			->setHtml($submitters);

		$groupArray = $this->getGroups();

		/** @var ControlGroup $group */
		foreach($groupArray as $groupTitle => $group)
		{
			$inputs = null;

			foreach($group->getInputArray() as $input)
			{
				/**
				 * Duplicator container render is handled within duplicator
				 */
				if($input instanceof DuplicatorContainer)
				{
					continue;
				}

				/**
				 * Nette form hidden input
				 */
				$inputs .= $input instanceof \Nette\Forms\Controls\HiddenField ? $input->getControl() : $input->render();
			}

			$row = Html::el('div')
				->class('row')
				->setHtml($inputs);

			$cardBody = Html::el('div')
				->class('card-body')
				->setHtml($row);

			$carHeader = null;

			if($groupTitle || $this->getTitle())
			{
				$groupColor = $group->getOption('color') ? ' ' . $group->getOption('color') : null;

				$carHeader = Html::el('div')
					->class('card-header' . $groupColor)
					->setHtml($groupTitle ?: $this->getTitle());
			}

			$content = $carHeader . $cardBody;

			/**
			 * Last iteration - add footer with submitters
			 */
			if($groupTitle === array_key_last($groupArray))
			{
				$content .= $cardFooter;
			}

			$card = Html::el('div')
				->class('card mt-2')
				->setHtml($content);

			$wrapCard = Html::el('div')
				->class($group->getClass() ?? 'col-12')
				->setHtml($card);

			if($group->getOption('id'))
			{
				$card->id($group->getOption('id'));
			}

			$groups .= $wrapCard;
		}

		$formRow = Html::el('div')
			->class('row')
			->setHtml($groups);

		$errorHtml = null;

		if($this->getFormErrors())
		{
			$errorString = null;

			foreach($this->getFormErrors() as $error)
			{
				$errorString .= $error . '<br>';
			}

			$errorHtml = Html::el('div')
				->class('alert alert-danger')
				->setAttribute('role', 'alert')
				->addHtml($errorString);
		}

		return $errorHtml . $formRow;
	}


	public function addGroup($caption = null, bool $setAsCurrent = true): ControlGroup
	{
		$group = new ControlGroup;
		$group->setOption('label', $caption);
		$group->setOption('visual', true);

		if($setAsCurrent)
		{
			$this->setCurrentGroup($group);
		}

		return !is_scalar($caption) || isset($this->groups[$caption])
			? $this->groups[] = $group
			: $this->groups[$caption] = $group;
	}


	public function getGroups(): array
	{
		return $this->groups;
	}


	public function getSubmitterArray(): array
	{
		$submitterArray = [];

		/** @var ControlGroup $group */
		foreach($this->getGroups() as $group)
		{
			$submitterArray = array_merge($submitterArray, $group->getSubmitterArray());
		}

		return $submitterArray;
	}


	public function addError($message, bool $translate = true): void
	{
		$this->formErrors[] = $message;

		parent::addError($message, $translate);
	}


	public function getFormErrors(): array
	{
		return $this->formErrors;
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


	public function addDate(string $name, $label = null, string $min = null, string $max = null): Control\TextInput
	{
		$dateInput = new Control\DateInput($label);

		if($min)
		{
			$dateInput->setHtmlAttribute('min', (new DateTime($min))->format('Y-m-d'));
		}

		if($max)
		{
			$dateInput->setHtmlAttribute('max', (new DateTime($max))->format('Y-m-d'));
		}

		return $this[$name] = $dateInput->setRequired(false)
			->addRule(fn($input) => DateTime::createFromFormat('Y-m-d', $input->getValue()), 'Vložte datum ve formátu dd.mm.yyyy');
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


	public function setRenderInline(bool $renderInline = true): self
	{
		$this->renderInline = $renderInline;

		return $this;
	}


	public function getRenderInline(): bool
	{
		return $this->renderInline;
	}


	public function setAjax(bool $ajax = true): self
	{
		$this->ajax = $ajax;

		return $this;
	}


	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}


	public function getTitle(): ?string
	{
		return $this->title;
	}


	public function setColor(string $color): self
	{
		$this->color = $color;

		return $this;
	}


	public function setIcon(string $icon): self
	{
		$this->icon = $icon;

		return $this;
	}


	public function setNoValidate(bool $noValidate = true): self
	{
		$this->noValidate = $noValidate;

		return $this;
	}


	public function setRenderFloating(bool $renderFloating = true): self
	{
		$this->renderFloating = $renderFloating;

		return $this;
	}


	public function getRenderFloating(): bool
	{
		return $this->renderFloating;
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

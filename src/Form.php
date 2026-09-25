<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use ModulIS\Form\Enum\RenderType;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\DateTimeControl;
use Nette\Forms\Controls\HiddenField;
use Nette\Utils\Html;
use Stringable;
use function assert;

class Form extends UIForm
{
	public const GreaterEqual = UIForm::Min;

	public const LessEqual = UIForm::Max;

	public const Greater = 'ModulIS\Form\FormValidator::greater';

	public const Less = 'ModulIS\Form\FormValidator::less';

	public const SameLength = 'ModulIS\Form\FormValidator::sameLength';

	public const ValidateRC = 'ModulIS\Form\FormValidator::validateRC';

	public const ValidateIC = 'ModulIS\Form\FormValidator::validateIC';

	public ?string $color = null;

	public bool $ajax = false;

	public Html|string|null $title = null;

	public ?string $icon = null;

	public bool $noValidate = true;

	private ?string $buttonClass = null;

	private RenderType $renderType = RenderType::Default;

	/** @var array<int|string, ControlGroup> */
	private array $groups = [];

	private string $defaultInputWrapClass = 'mb-2 col-12';

	/** @var array<string, Html|string> */
	private array $dividerArray = [];


	public function __construct
	(
		?IContainer $parent = null,
		?string $name = null
	)
	{
		parent::__construct($parent, $name);

		$this->addGroup();
	}


	public function renderForm(): string
	{
		$submitters = null;

		foreach($this->getSubmitterArray() as $submitter)
		{
			$submitters .= $submitter->render();
		}

		/** @var list<array{group: ControlGroup, content: string}> $cards */
		$cards = [];

		foreach($this->getGroups() as $group)
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
				$inputs .= $input instanceof HiddenField ? $input->getControl() : $input->render();

				if(array_key_exists((string) $input->getName(), $this->dividerArray))
				{
					$inputs .= $this->dividerArray[(string) $input->getName()];
				}
			}

			if($inputs === null)
			{
				continue;
			}

			$row = Html::el('div')
				->class('row')
				->setHtml($inputs);

			$cardBody = Html::el('div')
				->class('card-body')
				->setHtml($row);

			/**
			 * Form title (setTitle / setIcon) is shown on the first card unless its group has a title of its own
			 */
			$isFirstCard = $cards === [];
			$title = $isFirstCard ? $this->getTitle() : null;
			$icon = $isFirstCard ? $this->icon : null;

			$cardHeader = $group->getOption('label') || $group->getIcon() || $title || $icon
				? $group->getHeader($title, $icon)
				: null;

			$cards[] = ['group' => $group, 'content' => $cardHeader . $cardBody];
		}

		if($submitters)
		{
			$cardFooter = Html::el('div')
				->class('card-footer')
				->setHtml($submitters);

			/**
			 * Footer belongs to the last rendered card - a form (or last group) with submitters only gets a card of its own
			 */
			if($cards)
			{
				$lastCard = array_pop($cards);
				$lastCard['content'] .= $cardFooter;
				$cards[] = $lastCard;
			}
			else
			{
				$groupArray = $this->getGroups();

				$cards[] = ['group' => end($groupArray) ?: new ControlGroup, 'content' => (string) $cardFooter];
			}
		}

		$groups = null;

		foreach($cards as ['group' => $group, 'content' => $content])
		{
			$card = Html::el('div')
				->class('card mt-2')
				->setHtml($content);

			if($group->getOption('id'))
			{
				$card->id($group->getOption('id'));
			}

			$groups .= Html::el('div')
				->class($group->getClass() ?? 'col-12')
				->setHtml($card);
		}

		$formRow = Html::el('div')
			->class('row')
			->setHtml($groups);

		$errorHtml = null;

		if($this->getFormErrors())
		{
			$errorHtml = Html::el('div')
				->class('alert alert-danger')
				->setAttribute('role', 'alert');

			foreach($this->getFormErrors() as $i => $error)
			{
				if($i > 0)
				{
					$errorHtml->addHtml(Html::el('br'));
				}

				$errorHtml->addText($error);
			}
		}

		return $errorHtml . $formRow;
	}

	public function addGroup(string|Stringable|null $caption = null, bool $setAsCurrent = true): ControlGroup
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


	public function getGroup(string|int $name): ?ControlGroup
	{
		return $this->groups[$name] ?? null;
	}


	/**
	 * Parent keeps groups in a private property, therefore it has to be overridden together with addGroup()
	 */
	public function removeGroup(string|int|\Nette\Forms\ControlGroup $name): void
	{
		if($name instanceof \Nette\Forms\ControlGroup)
		{
			$key = array_search($name, $this->groups, true);
		}
		else
		{
			$key = isset($this->groups[$name]) ? $name : false;
		}

		if($key === false)
		{
			throw new \Nette\InvalidArgumentException("Group not found in form '{$this->getName()}'");
		}

		foreach($this->groups[$key]->getControls() as $control)
		{
			if($control instanceof \Nette\ComponentModel\IComponent)
			{
				$control->getParent()?->removeComponent($control);
			}
		}

		unset($this->groups[$key]);
	}


	/**
	 * @return array<int|string, ControlGroup>
	 */
	public function getGroups(): array
	{
		return $this->groups;
	}


	/**
	 * @return list<Control\Button|Control\SubmitButton|Control\Link>
	 */
	public function getSubmitterArray(): array
	{
		$submitterArray = [];

		foreach($this->getGroups() as $group)
		{
			$submitterArray = array_merge($submitterArray, $group->getSubmitterArray());
		}

		return $submitterArray;
	}


	/**
	 * Errors of the form itself (not of its controls), already translated
	 * @return list<string|Stringable>
	 */
	public function getFormErrors(): array
	{
		return $this->getOwnErrors();
	}


	public function setDefaultInputWrapClass(string $defaultInputWrapClass): static
	{
		$this->defaultInputWrapClass = $defaultInputWrapClass;

		return $this;
	}


	public function getDefaultInputWrapClass(): string
	{
		return $this->defaultInputWrapClass;
	}


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
			->addRule(self::Float);
	}


	/**
	 * @param ?array<mixed> $itemArray
	 */
	public function addAutocomplete(string $name, null|string|Stringable $label = null, ?int $maxLength = null, ?array $itemArray = []): Control\AutocompleteInput
	{
		return $this[$name] = new Control\AutocompleteInput($label, $maxLength, items: $itemArray ?? [])
			->setHtmlAttribute('autocomplete', 'off')
			->setClass('autocomplete-input');
	}


	public function addDate(string $name, null|string|Stringable $label = null): Control\DateTimeInput
	{
		$dateInput = new Control\DateTimeInput($label, DateTimeControl::TypeDate);

		return $this[$name] = $dateInput->setRequired(false)
			->setFormat('Y-m-d');
	}


	public function addDateWeek(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label)
			->setHtmlAttribute('type', 'week');
	}


	public function addDateTime(string $name, null|string|Stringable $label = null, bool $withSeconds = false): Control\DateTimeInput
	{
		$dateInput = new Control\DateTimeInput($label, DateTimeControl::TypeDateTime, $withSeconds);

		return $this[$name] = $dateInput->setRequired(false)
			->setFormat('Y-m-d H:i:s');
	}


	public function addTime(string $name, null|string|Stringable $label = null, bool $withSeconds = false): Control\DateTimeInput
	{
		return $this[$name] = new Control\DateTimeInput($label, DateTimeControl::TypeTime, $withSeconds)
			->setFormat($withSeconds ? 'H:i:s' : 'H:i');
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
			->setHtmlAttribute('cols', $cols)->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, string|Stringable|null $label = null, int $maxLength = 255): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label, $maxLength)
			->setRequired(false)
			->addRule(self::Email);
	}


	public function addInteger(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = new Control\TextInput($label)
			->setNullable()
			->setRequired(false)
			->addRule(self::Integer);
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
	 * @param array<mixed> $items
	 */
	public function addWhisperer(string $name, null|string|Stringable $label = null, array $items = []): Control\Whisperer
	{
		return $this[$name] = new Control\Whisperer($label, isset($items['']) ? $items : ['' => ''] + $items)
			->setHtmlAttribute('data-placeholder', 'Vyberte')
			->setClass('form-control-chosen')
			->checkDefaultValue(false);
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
	public function addMultiWhisperer(string $name, null|string|Stringable $label = null, array $items = []): Control\MultiWhisperer
	{
		return $this[$name] = new Control\MultiWhisperer($label, isset($items['']) ? $items : ['' => ''] + $items)
			->setClass('form-control-chosen')
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


	public function setRenderType(RenderType $renderType): static
	{
		$this->renderType = $renderType;

		return $this;
	}


	public function setRenderDefault(): static
	{
		$this->renderType = RenderType::Default;

		return $this;
	}


	public function setRenderFloating(): static
	{
		$this->renderType = RenderType::Floating;

		return $this;
	}


	public function setRenderInline(): static
	{
		$this->renderType = RenderType::Inline;

		return $this;
	}


	public function getRenderType(): RenderType
	{
		return $this->renderType;
	}


	public function setAjax(bool $ajax = true): static
	{
		$this->ajax = $ajax;

		return $this;
	}


	public function setTitle(string|Html $title): static
	{
		$this->title = $title;

		return $this;
	}


	public function getTitle(): null|Html|string
	{
		return $this->title;
	}


	public function setColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}


	public function setIcon(string $icon): static
	{
		$this->icon = $icon;

		return $this;
	}


	/**
	 * Default class(es) for all form buttons (e.g. "rounded rounded-4").
	 * Priority: setClass() on the button overrides this.
	 */
	public function setButtonClass(string $class): static
	{
		$this->buttonClass = $class;

		return $this;
	}


	public function getButtonClass(): ?string
	{
		return $this->buttonClass;
	}


	public function setNoValidate(bool $noValidate = true): static
	{
		$this->noValidate = $noValidate;

		return $this;
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

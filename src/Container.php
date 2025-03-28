<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use Nette\Forms\Controls\DateTimeControl;
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use Stringable;

class Container extends \Nette\Forms\Container
{
	public string $color = 'white';

	private ?string $title = null;

	private ?string $id = null;

	private bool $showCard = false;

	private ?string $wrapClass = null;

	private array $dividerArray = [];


	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}


	public function setColor(string $color): self
	{
		$this->color = $color;

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


	public function setWrapClass(string $wrapClass): self
	{
		$this->wrapClass = $wrapClass;

		return $this;
	}


	public function showCard(bool $showCard): self
	{
		$this->showCard = $showCard;

		return $this;
	}


	public function addHidden(string $name, $default = null): Control\Hidden
	{
		return $this[$name] = (new Control\Hidden)
			->setDefaultValue($default);
	}


	public function addText(string $name, null|string|Stringable $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols);
	}


	public function addFloat(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setNullable()
			->setHtmlType('number')
			->setHtmlAttribute('step', 'any')
			->addRule(Form::Float);
	}


	public function addAutocomplete(string $name, null|string|Stringable $label = null, ?int $maxLength = null, ?array $itemArray = []): Control\AutocompleteInput
	{
		return $this[$name] = (new Control\AutocompleteInput($label, $maxLength, items: $itemArray ?? []))
			->setHtmlAttribute('autocomplete', 'off')
			->setClass('autocomplete-input');
	}


	public function addPassword(string $name, null|string|Stringable $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols)
			->setHtmlType('password');
	}


	public function addTextArea(string $name, null|string|Stringable $label = null, ?int $cols = null, ?int $rows = null): Control\TextArea
	{
		return $this[$name] = (new Control\TextArea($label))
			->setHtmlAttribute('cols', $cols)
			->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, string|Stringable|null $label = null, int $maxLength = 255): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setRequired(false)
			->addRule(Form::Email);
	}


	public function addInteger(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setNullable()
			->setRequired(false)
			->addRule(Form::Integer);
	}


	public function addDate(string $name, object|string|null $label = null): Control\DateTimeInput
	{
		$dateInput = new Control\DateTimeInput($label, DateTimeControl::TypeDate);

		return $this[$name] = $dateInput->setRequired(false)
			->setFormat('Y-m-d')
			->addRule(fn($input) => DateTime::createFromFormat('Y-m-d', $input->getValue()), 'Vložte datum ve formátu dd.mm.yyyy');
	}


	public function addDateTime(string $name, object|string|null $label = null, bool $withSeconds = false): Control\DateTimeInput
	{
		$dateInput = new Control\DateTimeInput($label, DateTimeControl::TypeDateTime, $withSeconds);

		return $this[$name] = $dateInput->setRequired(false)
			->setFormat('Y-m-d H:i:s')
			->addRule(fn($input) => DateTime::createFromFormat($withSeconds ? 'Y-m-d H:i:s' : 'Y-m-d H:i:00', $input->getValue()), 'Vložte datum ve formátu dd.mm.yyyy ' . ($withSeconds ? 'hh:mm:ss' : 'hh:mm'));
	}


	public function addDateWeek(string $name, null|string|Stringable $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setHtmlAttribute('type', 'week');
	}


	public function addTime(string $name, object|string|null $label = null, bool $withSeconds = false): Control\DateTimeInput
	{
		return $this[$name] = (new Control\DateTimeInput($label, DateTimeControl::TypeTime, $withSeconds))
			->setFormat($withSeconds ? 'H:i:00' : 'H:i');
	}


	public function addUpload(string $name, null|string|Stringable $label = null, $multiple = false): Control\UploadControl
	{
		return $this[$name] = new Control\UploadControl($label, $multiple);
	}


	public function addMultiUpload(string $name, null|string|Stringable $label = null): Control\UploadControl
	{
		return $this[$name] = new Control\UploadControl($label, true);
	}


	public function addCheckbox(string $name, $caption = ''): Control\Checkbox
	{
		return $this[$name] = new Control\Checkbox($caption);
	}


	public function addRadioList(string $name, null|string|Stringable $label = null, ?array $items = null): Control\RadioList
	{
		return $this[$name] = new Control\RadioList($label, $items);
	}


	public function addCheckboxList(string $name, null|string|Stringable $label = null, ?array $items = null): Control\CheckboxList
	{
		return $this[$name] = new Control\CheckboxList($label, $items);
	}


	public function addSelect(string $name, null|string|Stringable $label = null, ?array $items = null, $size = null): Control\SelectBox
	{
		return $this[$name] = (new Control\SelectBox($label, $items))
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	public function addMultiSelect(string $name, null|string|Stringable $label = null, ?array $items = null, $size = null): Control\MultiSelectBox
	{
		return $this[$name] = (new Control\MultiSelectBox($label, $items))
			->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
	}


	public function addSubmit(string $name, $caption = ''): Control\SubmitButton
	{
		return $this[$name] = new Control\SubmitButton($caption);
	}


	public function addButton(string $name, $caption = ''): Control\Button
	{
		return $this[$name] = new Control\Button($caption);
	}


	public function addLink(string $name, string $caption = ''): Control\Link
	{
		return $this[$name] = new Control\Link($caption);
	}


	public function addDependentSelect(string $name, ?string $label = null, array $parents = [], ?callable $dependentCallback = null): Control\DependentSelect
	{
		return $this[$name] = new Control\DependentSelect($label, $parents, $dependentCallback);
	}


	public function addDependentMultiSelect(string $name, ?string $label = null, array $parents = [], ?callable $dependentCallback = null): Control\DependentMultiSelect
	{
		return $this[$name] = new Control\DependentMultiSelect($label, $parents, $dependentCallback);
	}


	public function addDuplicator($name, $factory, $copyNumber = 1, $forceDefault = false): Control\Duplicator
	{
		$duplicator = new Control\Duplicator($factory, $copyNumber, $forceDefault);

		$duplicator->setCurrentGroup($this->getCurrentGroup());

		return $this[$name] = $duplicator;
	}


	public function addWhisperer(string $name, $label = null, array $items = []): Control\Whisperer
	{
		return $this[$name] = (new Control\Whisperer($label, isset($items['']) ? $items : ['' => ''] + $items))
			->setHtmlAttribute('data-placeholder', 'Vyberte')
			->checkDefaultValue(false);
	}


	public function addDivider(Html|string $content, ?string $previousControl = null): void
	{
		if(!$previousControl)
		{
			$controlArray = iterator_to_array($this->getControls());
			$lastControl = end($controlArray);

			$previousControl = $lastControl->getName();
		}

		$this->dividerArray[$previousControl] = $content;
	}


	public function render(): string|Html
	{
		$components = $this->getComponents();

		if(iterator_count($components) === 0)
		{
			return '';
		}

		if($this->showCard)
		{
			$cardHeaderDiv = null;
			$cardBodyDiv = null;
			$cardFooterDiv = null;

			if($this->title)
			{
				$cardHeaderDiv = Html::el('div')
					->class('card-header ' . ($this->color ? 'bg-' . $this->color : ''))
					->addHtml($this->title);
			}

			$inputArray = $this->getInputArray();

			if($inputArray)
			{
				$inputs = null;

				foreach($inputArray as $control)
				{
					$inputs .= $control->render();

					if(array_key_exists($control->getName(), $this->dividerArray))
					{
						$inputs .= $this->dividerArray[$control->getName()];
					}
				}

				$rowDiv = Html::el('div')
					->class('row')
					->addHtml($inputs);

				$cardBodyDiv = Html::el('div')
					->class('card-body')
					->addHtml($rowDiv);
			}

			$submitterArray = $this->getSubmitterArray();

			if($submitterArray)
			{
				$submitterHtml = null;

				foreach($submitterArray as $submitter)
				{
					$submitterHtml .= $submitter->render();
				}

				$footerRowDiv = Html::el('div')
					->class('row')
					->addHtml($submitterHtml);

				$cardFooterDiv = Html::el('div')
					->class('card-footer')
					->addHtml($footerRowDiv);
			}

			$card = Html::el('div')
				->class('card')
				->addHtml($cardHeaderDiv . $cardBodyDiv . $cardFooterDiv);

			$outerDiv = Html::el('div')
				->class('mb-3 ' . ($this->wrapClass ?? 'col-12'))
				->addHtml($card);
		}
		else
		{
			$inputs = null;

			foreach($components as $control)
			{
				\assert($control instanceof Control\Renderable);
				$inputs .= $control->render();

				if(array_key_exists($control->getName(), $this->dividerArray))
				{
					$inputs .= $this->dividerArray[$control->getName()];
				}
			}

			$rowDiv = Html::el('div')
				->class('row')
				->addHtml($inputs);

			$outerDiv = Html::el('div')
				->class($this->wrapClass ?? 'col-12')
				->addHtml($rowDiv);
		}

		if($this->id)
		{
			$outerDiv->id($this->id);
		}

		return $outerDiv;
	}


	public function getInputArray(): array
	{
		$controlArray = [];

		/**
		 * Skip submitters
		 */
		foreach($this->getComponents() as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				continue;
			}

			$controlArray[] = $control;
		}

		return $controlArray;
	}


	public function getSubmitterArray(): array
	{
		$controlArray = [];

		/**
		 * Only submitters
		 */
		foreach($this->getComponents() as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				$controlArray[] = $control;
			}
		}

		return $controlArray;
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

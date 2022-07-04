<?php

declare(strict_types=1);

namespace ModulIS\Form;

use Nette\Utils\Html;

class Container extends \Nette\Forms\Container
{
	public string $color = 'white';

	private ?string $title = null;

	private ?string $id = null;

	private bool $showCard = false;
	
	private ?string $wrapClass = null;


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


	public function addText(string $name, $label = null, ?int $cols = null, ?int $maxLength = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label, $maxLength))
			->setHtmlAttribute('size', $cols);
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
			->setHtmlAttribute('cols', $cols)
			->setHtmlAttribute('rows', $rows);
	}


	public function addEmail(string $name, $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setRequired(false)
			->addRule(Form::EMAIL);
	}


	public function addInteger(string $name, $label = null): Control\TextInput
	{
		return $this[$name] = (new Control\TextInput($label))
			->setNullable()
			->setRequired(false)
			->addRule(Form::INTEGER);
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
		return $this[$name] = new Control\SubmitButton($caption);
	}


	public function addButton(string $name, $caption = null): Control\Button
	{
		return $this[$name] = new Control\Button($caption);
	}


	public function addLink(string $name, string $caption = null): Control\Link
	{
		return $this[$name] = new Control\Link($caption);
	}


	public function addDependentSelect(string $name, string $label = null, array $parents = [], callable $dependentCallback = null): Control\DependentSelect
	{
		return $this[$name] = new Control\DependentSelect($label, $parents, $dependentCallback);
	}


	public function addDependentMultiSelect(string $name, string $label = null, array $parents = [], callable $dependentCallback = null): Control\DependentMultiSelect
	{
		return $this[$name] = new Control\DependentMultiSelect($label, $parents, $dependentCallback);
	}


	public function addDuplicator($name, $factory, $copyNumber = 1, $maxCopies = null): Control\Duplicator
	{
		$duplicator = new Control\Duplicator($factory, $copyNumber, $maxCopies);

		$duplicator->setCurrentGroup($this->getCurrentGroup());

		return $this[$name] = $duplicator;
	}


	public function addWhisperer(string $name, string $label = null, array $items = []): Control\Whisperer
	{
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

			if($this->title)
			{
				$cardHeaderDiv = Html::el('div')
					->class('card-header ' . ($this->color ? 'bg-' . $this->color : ''))
					->addHtml($this->title);
			}

			$inputs = null;

			foreach($this->getInputArray() as $control)
			{
				$inputs .= $control->render();
			}

			$rowDiv = Html::el('div')
				->class('row')
				->addHtml($inputs);

			$cardBodyDiv = Html::el('div')
				->class('card-body')
				->addHtml($rowDiv);

			$submitterArray = $this->getSubmitterArray();

			$cardFooterDiv = null;

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

			/** @var Control\Renderable $control */
			foreach($components as $control)
			{
				$inputs .= $control->render();
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

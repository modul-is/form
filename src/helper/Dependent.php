<?php

/**
 * Based on NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 */
namespace ModulIS\Form\Helper;

trait Dependent
{
	private array $parents;

	private $dependentCallback = null;

	private bool $disabledWhenEmpty;

	private $tempValue;


	public function getControl() : Nette\Utils\Html
	{
		$this->tryLoadItems();

		$attrs = [];
		$control = parent::getControl();
		$form = $this->getForm();

		$parents = [];

		foreach($this->parents as $parent)
		{
			$parents[$this->getNormalizeName($parent)] = $parent->getHtmlId();
		}

		$attrs['data-dependentselectbox-parents'] = Nette\Utils\Json::encode($parents);
		$attrs['data-dependentselectbox'] = $form->getPresenter()->link($this->lookupPath(Presenter::class) . \Nette\ComponentModel\Component::NameSeparator . \ModulIS\Form\Dial\SignalDial::Load . '!');

		$control->addAttributes($attrs);
		return $control;
	}


	private function tryLoadItems(): void
	{
		if($this->parents !== array_filter($this->parents, fn($p) => !$p->hasErrors()))
		{
			return;
		}

		$parentsValues = [];

		foreach($this->parents as $parent)
		{
			$parentsValues[$parent->getName()] = $parent->getValue();
		}

		$data = $this->getDependentData([$parentsValues]);
		$items = $data->getItems();

		if($this->getForm()->isSubmitted())
		{
			$this->setValue($this->value);
		}
		elseif($this->tempValue !== null)
		{
			$this->setValue($this->tempValue);
		}
		else
		{
			$this->setValue($data->getValue());
		}

		if(count($items) > 0)
		{
			$this->loadHttpData();

			$this->setItems($items)
				->setPrompt($data->getPrompt() ?: $this->getPrompt());
		}
		else
		{
			if($this->disabledWhenEmpty === true && !$this->isDisabled())
			{
				$this->setDisabled();
			}
		}
	}


	public function getValue()
	{
		$this->tryLoadItems();

		if (!in_array($this->tempValue, [null, '', []], true))
		{
			return $this->tempValue;
		}

		return parent::getValue();
	}


	public function setValue($value): static
	{
		$this->tempValue = $value;
		return $this;
	}


	public function setItems(array $items, bool $useKeys = true): static
	{
		parent::setItems($items, $useKeys);

		if (!in_array($this->tempValue, [null, '', []], true))
		{
			parent::setValue($this->tempValue);
		}

		return $this;
	}


	private function getDependentData(array $args = []): DependentData
	{
		if($this->dependentCallback === null)
		{
			throw new \Nette\InvalidStateException('Dependent callback for "' . $this->getHtmlId() . '" must be set!');
		}

		$dependentData = call_user_func_array($this->dependentCallback, $args);

		if(!($dependentData instanceof DependentData))
		{
			throw new \Nette\InvalidStateException('Callback for "' . $this->getHtmlId() . '" must return ' . DependentData::class . ' instance!');
		}

		return $dependentData;
	}


	public function setDependentCallback(callable $callback): static
	{
		$this->dependentCallback = $callback;
		return $this;
	}


	public function setDependentCallbackParams(array $params): static
	{
		$this->dependentCallbackParams = $params;
		return $this;
	}


	public function setDisabledWhenEmpty(bool $value = true): static
	{
		$this->disabledWhenEmpty = $value;
		return $this;
	}


	private function getNormalizeName(Nette\Forms\Controls\BaseControl $parent): string
	{
		return str_replace('-', '_', $parent->getHtmlId());
	}
}

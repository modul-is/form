<?php

declare(strict_types=1);

/**
 * Based on NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 */

namespace ModulIS\Form\Helper;

use Nette\Application\UI\Presenter;

trait Dependent
{
	protected array $parents = [];

	private $dependentCallback;

	private bool $disabledWhenEmpty = false;

	private $tempValue;


	public function getControl(): \Nette\Utils\Html
	{
		$this->tryLoadItems();

		$attrs = [];
		$control = parent::getControl();

		$parents = [];

		foreach($this->parents as $parent)
		{
			$parents[$this->getNormalizeName($parent)] = $parent->getHtmlId();
		}

		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		$attrs['data-dependentselectbox-parents'] = \Nette\Utils\Json::encode($parents);
		$attrs['data-dependentselectbox'] = $presenter->link($this->lookupPath(Presenter::class) . \Nette\ComponentModel\Component::NameSeparator . \ModulIS\Form\Dial\SignalDial::Load . '!');

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

			$this->setItems($items);

			if(method_exists($this, 'setPrompt'))
			{
				$inputPrompt = method_exists($this, 'getPrompt') ? $this->getPrompt() : null;

				$this->setPrompt($data->getPrompt() ?: $inputPrompt);
			}
		}
		else
		{
			if($this->disabledWhenEmpty === true && !$this->isDisabled())
			{
				$this->setDisabled();
			}
		}
	}


	public function getValue(): mixed
	{
		$this->tryLoadItems();

		if(!in_array($this->tempValue, [null, '', []], true))
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


	public function setDisabledWhenEmpty(bool $value = true): static
	{
		$this->disabledWhenEmpty = $value;

		return $this;
	}


	private function getNormalizeName(\Nette\Forms\Controls\BaseControl $parent): string
	{
		return str_replace('-', '_', $parent->getHtmlId());
	}
}

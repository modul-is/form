<?php

/**
 * Based on NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 */
namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

class DependentData
{
	public function __construct
	(
		private array $items = [],
		private string|int|null $value = null,
		private ?string $prompt = null
	)
	{
	}


	public function getItems(): array
	{
		return $this->items;
	}


	public function setItems(array $items): self
	{
		$this->items = $items;
		return $this;
	}


	public function getValue(): string|int|null
	{
		return $this->value;
	}


	public function setValue(string|int|null $value): self
	{
		$this->value = $value;
		return $this;
	}


	public function getPrompt(): ?string
	{
		return $this->prompt;
	}


	public function setPrompt(?string $value): self
	{
		$this->prompt = $value;
		return $this;
	}


	public function getPreparedItems(?array $disabledItems = []): array
	{
		$items = [];

		foreach($this->items as $key => $item)
		{
			if(is_array($item))
			{
				$groupItems = [];

				foreach($item as $innerKey => $innerItem)
				{
					$el = $this->getPreparedElement($innerKey, $innerItem, $disabledItems);
					$this->addElementToItemsList($groupItems, $el);
				}

				$items[$key] = [
					'key' => $key,
					'value' => $groupItems,
				];
			}
			else
			{
				$el = $this->getPreparedElement($key, $item, $disabledItems);
				$this->addElementToItemsList($items, $el);
			}
		}
		// make a List so the order of items is preserved when sent as JSON to client
		return array_values($items);
	}


	private function getPreparedElement(string $key, $item, ?array $disabledItems = []): \Nette\Utils\Html
	{
		if(!$item instanceof Nette\Utils\Html)
		{
			$el = Nette\Utils\Html::el('option')
				->value($key)
				->setText($item);
		}
		else
		{
			$el = $item;
		}

		// disable element
		if(is_array($disabledItems) && array_key_exists($key, $disabledItems) && $disabledItems[$key] === true)
		{
			$el->disabled(true);
		}

		return $el;
	}


	private function addElementToItemsList(array &$items, Html $el)
	{
		$items[$el->getAttribute('value')] = [
			'key' => $el->getValue(),
			'value' => $el->getText(),
		];

		end($items);

		$lKey = key($items);

		foreach($el->attrs as $attr => $val)
		{
			$items[$lKey]['attributes'][$attr] = $val;
		}
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait CoreList
{
	public array $tooltips = [];

	public int $itemsPerRow = 0;

	public ?string $itemClass = null;


	public function setTooltips(array $tooltips): self
	{
		$this->tooltips = $tooltips;
		return $this;
	}


	public function setItemsPerRow(int $number): self
	{
		$this->itemsPerRow = $number;
		return $this;
	}


	public function setItemClass(string $itemClass): self
	{
		$this->itemClass = $itemClass;
		return $this;
	}
}

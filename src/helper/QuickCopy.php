<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

interface QuickCopyable
{
	public function getQuickCopy(): bool;
}

trait QuickCopy
{
	protected bool $quickCopy = false;


	/**
	 * Button copying the value to the clipboard at the end of the input group
	 */
	public function setQuickCopy(bool $value = true): static
	{
		$this->quickCopy = $value;

		return $this;
	}


	public function getQuickCopy(): bool
	{
		return $this->quickCopy;
	}
}

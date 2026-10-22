<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Description
{
	protected string|\Stringable|null $description = null;


	/**
	 * Help text under the control (Bootstrap form-text), plain strings are translated and escaped
	 */
	public function setDescription(string|\Stringable|null $description): static
	{
		$this->description = $description;

		return $this;
	}


	public function getDescription(): string|\Stringable|null
	{
		return $this->description;
	}
}

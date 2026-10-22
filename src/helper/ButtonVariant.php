<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait ButtonVariant
{
	protected ?string $variant = null;


	/**
	 * Bootstrap button style: solid (default), outline, subtle, text, link
	 */
	public function setVariant(string $variant): static
	{
		$this->variant = $variant;

		return $this;
	}


	public function getVariant(): ?string
	{
		return $this->variant;
	}
}

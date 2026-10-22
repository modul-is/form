<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Color
{
	public ?string $color = null;


	/**
	 * Bootstrap theme color - primary, secondary, success, danger, warning, info, accent, inverse
	 */
	public function setColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}
}

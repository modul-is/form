<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Tooltip
{
	protected ?string $tooltip = null;


	/**
	 * Help icon with a tooltip next to the caption (plain text)
	 */
	public function setTooltip(?string $text): static
	{
		$this->tooltip = $text;

		return $this;
	}


	public function getTooltip(): ?string
	{
		return $this->tooltip;
	}
}

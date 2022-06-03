<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait Color
{
	public ?string $color = null;


	public function setColor(string $color): self
	{
		$this->color = $color;
		return $this;
	}
}

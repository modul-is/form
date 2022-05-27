<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait Icon
{
	public ?string $icon = null;


	public function setIcon(string $icon): self
	{
		$this->icon = $icon;
		return $this;
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait FloatingLabel
{
	public ?bool $floatingLabel = null;


	public function setFloatingLabel(bool $floatingLabel = true): self
	{
		$this->floatingLabel = $floatingLabel;

		return $this;
	}


	public function getFloatingLabel(): ?bool
	{
		return $this->floatingLabel;
	}
}

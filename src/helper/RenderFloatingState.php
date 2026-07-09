<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait RenderFloatingState
{
	protected ?bool $renderFloating = null;


	public function setRenderFloating(bool $renderFloating = true): static
	{
		$this->renderFloating = $renderFloating;

		return $this;
	}


	public function getRenderFloating(): ?bool
	{
		return $this->renderFloating;
	}
}

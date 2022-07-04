<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait RenderFloating
{
	public ?bool $renderFloating = null;


	public function setRenderFloating(bool $renderFloating = true): self
	{
		$this->renderFloating = $renderFloating;

		return $this;
	}


	public function getRenderFloating(): ?bool
	{
		return $this->renderFloating;
	}
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait RenderInline
{
	protected ?bool $renderInline = null;


	public function setRenderInline(bool $renderInline = true): self
	{
		$this->renderInline = $renderInline;

		return $this;
	}


	public function getRenderInline(): ?bool
	{
		return $this->renderInline;
	}
}

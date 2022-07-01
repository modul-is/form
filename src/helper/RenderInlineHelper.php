<?php

namespace ModulIS\Form\Helper;

trait RenderInlineHelper
{
	private ?bool $renderInline = null;
	
	
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

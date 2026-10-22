<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Enum;

trait RenderType
{
	protected ?Enum\RenderType $renderType = null;


	public function setRenderType(Enum\RenderType $renderType): static
	{
		$this->renderType = $renderType;

		return $this;
	}


	/**
	 * Null means inherited from the form
	 */
	public function getRenderType(): ?Enum\RenderType
	{
		return $this->renderType;
	}


	public function setRenderDefault(): static
	{
		return $this->setRenderType(Enum\RenderType::Default);
	}


	public function setRenderFloating(): static
	{
		return $this->setRenderType(Enum\RenderType::Floating);
	}


	public function setRenderInline(): static
	{
		return $this->setRenderType(Enum\RenderType::Inline);
	}
}

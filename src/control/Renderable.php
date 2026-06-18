<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Enum\RenderType;

interface Renderable
{
	public function setRenderType(RenderType $renderType): self;

	public function getRenderType(): ?RenderType;

	public function render(): \Nette\Utils\Html|string;
}

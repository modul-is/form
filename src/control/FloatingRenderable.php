<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

interface FloatingRenderable
{
	public function setRenderFloating(): self;

	public function getRenderFloating(): ?bool;

	public function renderFloating(): \Nette\Utils\Html;
}

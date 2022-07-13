<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

interface FloatingRenderable
{
	public function renderFloating(): \Nette\Utils\Html;
	
	public function getRenderFloating(): ?bool;
}

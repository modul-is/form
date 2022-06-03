<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

interface Renderable
{
	public function render(): \Nette\Utils\Html|string;
}

<?php

namespace ModulIS\Form\Control;

interface Renderable
{
	public function render(): \Nette\Utils\Html|string;
}

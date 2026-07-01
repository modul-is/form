<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

interface HasInputGroup
{
	public function getPrepend(): ?Html;

	public function getAppend(): ?Html;
}

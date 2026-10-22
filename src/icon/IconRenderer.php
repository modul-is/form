<?php

declare(strict_types = 1);

namespace ModulIS\Form\Icon;

use Nette\Utils\Html;

/**
 * Renders icons used by the form - icon set is up to the application.
 *
 * The library itself asks for these semantic names: help (tooltip), save (submit), add, remove (duplicator),
 * copy (quick copy) and info. Everything else is passed by the application (setIcon()) as is.
 */
interface IconRenderer
{
	public function render(string $icon): Html;
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait ClassList
{
	/**
	 * Slozi hodnotu atributu class z casti a zahodi prazdne - lepeni pres tecku
	 * vyrabelo dvojite mezery a slepene tridy, kdyz byla nektera cast null.
	 */
	protected function joinClass(?string ...$parts): string
	{
		return implode(' ', array_filter($parts, fn(?string $part): bool => $part !== null && $part !== ''));
	}
}

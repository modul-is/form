<?php

declare(strict_types = 1);

namespace ModulIS\Form\Enum;

interface RadioEnum
{
	/**
	 * @return array<int|string, string|\Stringable>
	 */
	public static function getList(): array;

	/**
	 * @return array<int|string, string>
	 */
	public static function getDescription(): array;
}

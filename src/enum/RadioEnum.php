<?php

declare(strict_types = 1);

namespace ModulIS\Form\Enum;

interface RadioEnum
{
	public static function getList(): array;

	public static function getDescription(): array;
}

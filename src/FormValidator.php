<?php

declare(strict_types=1);

namespace ModulIS\Form;

final class FormValidator
{
	public static function greater($control, $val): bool
	{
		return $control->getValue() > $val;
	}


	public static function less($control, $val): bool
	{
		return $control->getValue() < $val;
	}


	public static function sameLength($control, $val): bool
	{
		return mb_strlen($control->getValue()) === mb_strlen($val);
	}
}

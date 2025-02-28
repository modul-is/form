<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use Nette\Forms\Controls\BaseControl;

final class FormValidator
{
	public static function greater(BaseControl $control, $val): bool
	{
		return $control->getValue() > $val;
	}


	public static function less(BaseControl $control, $val): bool
	{
		return $control->getValue() < $val;
	}


	public static function sameLength(BaseControl $control, $val): bool
	{
		return mb_strlen($control->getValue()) === mb_strlen($val);
	}


	public static function validateRC(BaseControl $control): bool
	{
		$value = strval($control->getValue());

		if(!preg_match('#^(\d\d)(\d\d)(\d\d)[ /]*(\d\d\d)(\d?)$#', $value, $matches))
		{
			return false;
		}

		[$rc, $yearString, $monthString, $day, $ext, $lastDigit] = $matches;

		$year = intval($yearString);
		$month = intval($monthString);

		if($lastDigit === '')
		{
			$year += $year < 54 ? 1900 : 1800;
		}
		else
		{
			$mod = intval($yearString . $monthString . $day . $ext) % 11;

			if($mod === 10)
			{
				$mod = 0;
			}

			if($mod !== intval($lastDigit))
			{
				return false;
			}

			$year += $year < 54 ? 2000 : 1900;
		}

		if($month > 70 && $year > 2003)
		{
			$month -= 70;
		}
		elseif($month > 50)
		{
			$month -= 50;
		}
		elseif($month > 20 && $year > 2003)
		{
			$month -= 20;
		}

		return \Nette\Utils\DateTime::createFromFormat('Ymd', $year . $month . $day) !== false;
	}


	public static function validateIC(BaseControl $control): bool
	{
		$ic = strval($control->getValue());

		if(!preg_match('/^\d{8}$/', $ic))
		{
			return false;
		}

		$a = 0;

		for($i = 0; $i < 7; $i++)
		{
			$a += intval($ic[$i]) * (8 - $i);
		}

		$a %= 11;

		if($a === 0)
		{
			$c = 1;
		}
		elseif($a === 1)
		{
			$c = 0;
		}
		else
		{
			$c = 11 - $a;
		}

		return intval($ic[7]) === $c;
	}
}

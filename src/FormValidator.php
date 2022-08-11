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


	public static function validateRC($control): bool
	{
		$rc = $control->getValue();

		if(!preg_match('#^(\d\d)(\d\d)(\d\d)[ /]*(\d\d\d)(\d?)$#', $rc, $matches))
		{
			return false;
		}

		list($rc, $year, $month, $day, $ext, $control) = $matches;

		if($control === '')
		{
			$year += $year < 54 ? 1900 : 1800;
		}
		else
		{
			$mod = ($year . $month . $day . $ext) % 11;

			if($mod === 10)
			{
				$mod = 0;
			}

			if($mod !== (int) $control)
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

		if(!\Nette\Utils\DateTime::createFromFormat('Ymd', $year . $month . $day))
		{
			return false;
		}

		return true;
	}


	public static function validateIC($control): bool
	{
		$ic = $control->getValue();

		if(!preg_match('/^\d{8}$/', $ic))
		{
			return false;
		}

		$a = 0;

		for($i = 0; $i < 7; $i++)
		{
			$a += $ic[$i] * (8 - $i);
		}

		$a = $a % 11;

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

		return (int) $ic[7] === $c;
	}
}

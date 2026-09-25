<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

final class FormData
{
	/**
	 * Parses serialized form data sent by JS signals - empty fields are dropped, but '0' (zero, unchecked value) is kept
	 * @return array<mixed>
	 */
	public static function parse(mixed $formData): array
	{
		if(!is_string($formData) || $formData === '')
		{
			return [];
		}

		$values = [];

		parse_str($formData, $values);

		return array_filter($values, fn($value) => $value !== '' && $value !== []);
	}
}

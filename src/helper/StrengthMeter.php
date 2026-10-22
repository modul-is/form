<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait StrengthMeter
{
	protected bool $strengthMeter = false;

	protected ?int $strengthMinLength = null;


	/**
	 * Password strength meter under the input (Bootstrap strength) - only informative, add rules for real requirements
	 *
	 * @param ?int $minLength length for the first point of the score (Bootstrap default 8)
	 */
	public function setStrengthMeter(bool $strengthMeter = true, ?int $minLength = null): static
	{
		$this->strengthMeter = $strengthMeter;
		$this->strengthMinLength = $minLength;

		return $this;
	}


	public function hasStrengthMeter(): bool
	{
		return $this->strengthMeter;
	}


	public function getStrengthMinLength(): ?int
	{
		return $this->strengthMinLength;
	}
}

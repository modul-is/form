<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Plaintext
{
	protected bool $plaintext = false;


	/**
	 * Read-only value rendered as plain text (Bootstrap form-control-plaintext)
	 */
	public function setPlaintext(bool $plaintext = true): static
	{
		$this->plaintext = $plaintext;

		return $this;
	}


	public function isPlaintext(): bool
	{
		return $this->plaintext;
	}
}

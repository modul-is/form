<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\HtmlStringable;

trait Validation
{
	private string|HtmlStringable|null $validationSuccessMessage = null;

	private bool $submitted = false;


	public function setValidationSuccessMessage(string|HtmlStringable $validationSuccessMessage): static
	{
		$this->validationSuccessMessage = $validationSuccessMessage;

		return $this;
	}


	public function getValidationSuccessMessage(): string|HtmlStringable|null
	{
		return $this->validationSuccessMessage;
	}


	/**
	 * is-invalid once the form (or the control alone, see setSubmitted()) was submitted, is-valid only for filled
	 * controls with setValidationSuccessMessage() - otherwise a returned form would mark all valid fields green
	 */
	public function getValidationClass(): ?string
	{
		$form = $this->getForm(false);

		if($form === null || !$form->isAnchored() || (!$form->isSubmitted() && !$this->isSubmitted()))
		{
			return null;
		}

		if($this->hasErrors())
		{
			return 'is-invalid';
		}

		return $this->validationSuccessMessage !== null && $this->isFilled() ? 'is-valid' : null;
	}


	public function isSubmitted(): bool
	{
		return $this->submitted;
	}


	/**
	 * Show validation state of the control even when the form was not submitted (e.g. after an AJAX signal)
	 */
	public function setSubmitted(bool $submitted = true): void
	{
		$this->submitted = $submitted;
	}
}

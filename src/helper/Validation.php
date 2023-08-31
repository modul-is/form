<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait Validation
{
	private ?string $validationSuccessMessage = null;

	private bool $submitted = false;


	public function setValidationSuccessMessage(string $validationSuccessMessage): self
	{
		$this->validationSuccessMessage = $validationSuccessMessage;

		return $this;
	}


	public function getValidationSuccessMessage(): ?string
	{
		return $this->validationSuccessMessage;
	}


	protected function getValidationClass(): ?string
	{
		$validationClass = null;

		if($this->getForm()->isAnchored() && ($this->getForm()->isSubmitted() || $this->isSubmitted()))
		{
			if($this->hasErrors())
			{
				$validationClass = 'is-invalid';
			}
			elseif($this->isFilled())
			{
				$validationClass = 'is-valid';
			}
		}

		return $validationClass;
	}


	protected function getValidationFeedback(): string|Html
	{
		$validationFeedBack = '';

		if($this->getForm()->isAnchored() && ($this->getForm()->isSubmitted() || $this->isSubmitted()))
		{
			if($this->hasErrors())
			{
				$validationFeedBack = Html::el('div')
					->class('invalid-feedback')
					->addHtml($this->getError());
			}
			elseif($this->isRequired() && $this->getValidationSuccessMessage())
			{
				$validationFeedBack = Html::el('div')
					->class('valid-feedback')
					->addHtml($this->getValidationSuccessMessage());
			}
		}

		return $validationFeedBack;
	}


	public function isSubmitted(): bool
	{
		return $this->submitted;
	}


	public function setSubmitted(bool $submitted = true): void
	{
		$this->submitted = $submitted;
	}
}

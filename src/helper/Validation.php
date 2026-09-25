<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\HtmlStringable;
use Nette\Utils\Html;

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
				$validationFeedBack = $this->createErrorFeedback();
			}
			elseif($this->isRequired() && $this->getValidationSuccessMessage())
			{
				$validationFeedBack = $this->createSuccessFeedback();
			}
		}

		return $validationFeedBack;
	}


	/**
	 * Error messages may contain the submitted value (%value), therefore plain strings are escaped - same as Nette renderer
	 */
	protected function createErrorFeedback(): Html
	{
		$feedback = Html::el('div')
			->class('invalid-feedback');

		foreach($this->getErrors() as $i => $error)
		{
			if($i > 0)
			{
				$feedback->addText(' ');
			}

			$feedback->addText($error);
		}

		return $feedback;
	}


	protected function createSuccessFeedback(): Html
	{
		$message = $this->getValidationSuccessMessage();

		return Html::el('div')
			->class('valid-feedback')
			->addText($message instanceof HtmlStringable ? $message : $this->translate($message));
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

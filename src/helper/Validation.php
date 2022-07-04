<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait Validation
{
	private ?string $validationSuccessMessage = null;


	public function setValidationSuccessMessage(string $validationSuccessMessage): self
	{
		$this->validationSuccessMessage = $validationSuccessMessage;

		return $this;
	}


	public function getValidationSuccessMessage(): ?string
	{
		return $this->validationSuccessMessage;
	}


	public function getValdiationClass(): ?string
	{
		$validationClass = null;

		if($this->getForm()->isSubmitted())
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


	public function getValidationFeedback(): ?Html
	{
		$validationFeedBack = null;

		if($this->getForm()->isSubmitted())
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
}

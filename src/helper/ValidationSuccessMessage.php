<?php

namespace ModulIS\Form\Helper;

trait ValidationSuccessMessage
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
}

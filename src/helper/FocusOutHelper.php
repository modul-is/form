<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait FocusOutHelper
{

	
	public ?bool $onSuccess = null;
	
	
	public function signalReceived($signal): void
	{
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');
		

		if(!$presenter->isAjax() || $this->isDisabled())
		{
			return;
		}
		
		if($signal === self::S)
		{
			$this->onSuccess($presenter->getParameter('value'));
			
			$presenter->payload->value = $presenter->getParameter('value');

			$presenter->sendPayload();
		}
		else
		{
			throw new Exception("Unknown signal '$signal' for input '" . $this->getName() . "'");
		}
	}


	public function setFloatingLabel(bool $floatingLabel): void
	{
		$this->floatingLabel = $floatingLabel;
	}


	public function getFloatingLabel(): ?bool
	{
		return $this->floatingLabel;
	}
}

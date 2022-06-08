<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait FocusOutHelper
{
	public $onFocusOut = [];


	public function signalReceived($signal): void
	{
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');

		if($this->isDisabled())
		{
			return;
		}

		if($signal === 'focusout')
		{
			$value = json_decode($presenter->getHttpRequest()->getRawBody())->value;

			$presenter->payload->value = $value;
			$presenter->payload->errorMessage = null;

			call_user_func_array($this->onFocusOut, [&$presenter->payload]);

			$presenter->sendPayload();
		}
		else
		{
			throw new \Exception("Unknown signal '$signal' for input '" . $this->getName() . "'");
		}
	}
}

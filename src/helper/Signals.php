<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait Signals
{
	public $onFocusOut = [];

	public $onChange = [];

	protected string $onFocusOutSignal = 'onfocusout';

	protected string $onChangeSignal = 'onchange';


	public function signalReceived($signal): void
	{
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');

		if($this->isDisabled())
		{
			return;
		}

		if($signal !== $this->onFocusOutSignal && $signal !== $this->onChangeSignal)
		{
			throw new \Exception("Unknown signal '$signal' for input '" . $this->getName() . "'");
		}

		$value = json_decode($presenter->getHttpRequest()->getRawBody())->value;

		$presenter->payload->value = $value;
		$presenter->payload->errorMessage = null;

		if($signal === $this->onFocusOutSignal)
		{
			call_user_func_array($this->onFocusOut, [&$presenter->payload]);
		}
		elseif($signal === $this->onChangeSignal)
		{
			call_user_func_array($this->onChange, [&$presenter->payload]);
		}

		$presenter->sendPayload();
	}


	public function addSignalsToInput(Html &$input): void
	{
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);

		if(!empty($this->onFocusOut))
		{
			$input->setAttribute('data-on-focusout', $presenter->link($this->lookupPath('Nette\Application\UI\Presenter') . '-' . $this->onFocusOutSignal . '!'));
		}

		if(!empty($this->onChange))
		{
			$input->setAttribute('data-on-change', $presenter->link($this->lookupPath('Nette\Application\UI\Presenter') . '-' . $this->onChangeSignal . '!'));
		}
	}


	public function hasSignal(): bool
	{
		return is_callable($this->onChange) || is_callable($this->onFocusOut);
	}
}

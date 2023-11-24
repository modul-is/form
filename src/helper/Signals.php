<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;
use Nette\Application\UI\Presenter;
use ModulIS\Form\Dial\SignalDial;
use Nette\ComponentModel\IComponent;

trait Signals
{
	public $onFocusOut = [];

	public $onChange = [];


	public function signalReceived($signal): void
	{
		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if($signal !== SignalDial::OnFocusOut && $signal !== SignalDial::OnChange)
		{
			throw new \Exception("Unknown signal '$signal' for input '" . $this->getName() . "'");
		}

		$value = $presenter->getParameter('value');
		$inputName = $presenter->getParameter('input');

		$currentValues = [];

		parse_str($presenter->getParameter('formdata'), $currentValues);

		if($signal === SignalDial::OnFocusOut)
		{
			call_user_func_array($this->onFocusOut, [$value, $inputName, array_filter($currentValues)]);
		}
		elseif($signal === SignalDial::OnChange)
		{
			call_user_func_array($this->onChange, [$value, $inputName, array_filter($currentValues)]);
		}
	}


	public function addSignalsToInput(Html &$input): void
	{
		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if(!empty($this->onFocusOut))
		{
			$input->setAttribute('data-on-focusout', $presenter->link($this->lookupPath(Presenter::class) . IComponent::NameSeparator . SignalDial::OnFocusOut . '!'));
		}

		if(!empty($this->onChange))
		{
			$input->setAttribute('data-on-change', $presenter->link($this->lookupPath(Presenter::class) . IComponent::NameSeparator . SignalDial::OnChange . '!'));
		}
	}


	public function hasSignal(): bool
	{
		return is_callable($this->onChange) || is_callable($this->onFocusOut);
	}
}

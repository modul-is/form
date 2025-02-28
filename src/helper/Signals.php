<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Dial\SignalDial;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Html;

trait Signals
{
	protected $onFocusOutCallback;

	protected $onChangeCallback;


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
		$formData = $presenter->getParameter('formdata');

		if(!$formData)
		{
			return;
		}

		$currentValues = [];

		parse_str($formData, $currentValues);

		if($signal === SignalDial::OnFocusOut)
		{
			call_user_func_array($this->onFocusOutCallback, [$value, $inputName, array_filter($currentValues)]);
		}
		elseif($signal === SignalDial::OnChange)
		{
			call_user_func_array($this->onChangeCallback, [$value, $inputName, array_filter($currentValues)]);
		}
	}


	public function addSignalsToInput(Html &$input): void
	{
		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if(!empty($this->onFocusOutCallback))
		{
			$input->setAttribute('data-on-focusout', $presenter->link($this->lookupPath(Presenter::class) . IComponent::NameSeparator . SignalDial::OnFocusOut . '!'));
		}

		if(!empty($this->onChangeCallback))
		{
			$input->setAttribute('data-on-change', $presenter->link($this->lookupPath(Presenter::class) . IComponent::NameSeparator . SignalDial::OnChange . '!'));
		}
	}


	public function hasSignal(): bool
	{
		return is_callable($this->onChangeCallback) || is_callable($this->onFocusOutCallback);
	}


	public function setOnFocusOutCallback(callable $callback): static
	{
		$this->onFocusOutCallback = $callback;

		return $this;
	}


	public function setOnChangeCallback(callable $callback): static
	{
		$this->onChangeCallback = $callback;

		return $this;
	}


	public function getOnChangeCallback()
	{
		return $this->onChangeCallback;
	}


	public function getOnFocusOutCallback()
	{
		return $this->onFocusOutCallback;
	}
}

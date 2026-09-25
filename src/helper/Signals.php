<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Dial\SignalDial;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Html;

trait Signals
{
	/** @var ?\Closure(mixed, mixed, array<mixed>): void */
	protected ?\Closure $onFocusOutCallback = null;

	/** @var ?\Closure(mixed, mixed, array<mixed>): void */
	protected ?\Closure $onChangeCallback = null;


	public function signalReceived(string $signal): void
	{
		$presenter = $this->lookup(Presenter::class);

		if($signal !== SignalDial::OnFocusOut && $signal !== SignalDial::OnChange)
		{
			throw new \Exception("Unknown signal '$signal' for input '" . $this->getName() . "'");
		}

		$value = $presenter->getParameter('value');
		$inputName = $presenter->getParameter('input');
		$formData = $presenter->getParameter('formdata');

		if($formData === null)
		{
			return;
		}

		$callback = $signal === SignalDial::OnFocusOut ? $this->onFocusOutCallback : $this->onChangeCallback;

		if($callback === null)
		{
			throw new \Nette\InvalidStateException("Callback for signal '$signal' not set for input '" . $this->getName() . "'");
		}

		call_user_func_array($callback, [$value, $inputName, FormData::parse($formData)]);
	}


	public function addSignalsToInput(Html &$input): void
	{
		$presenter = $this->lookup(Presenter::class);

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


	/**
	 * @param callable(mixed, mixed, array<mixed>): void $callback
	 */
	public function setOnFocusOutCallback(callable $callback): static
	{
		$this->onFocusOutCallback = $callback(...);

		return $this;
	}


	/**
	 * @param callable(mixed, mixed, array<mixed>): void $callback
	 */
	public function setOnChangeCallback(callable $callback): static
	{
		$this->onChangeCallback = $callback(...);

		return $this;
	}


	public function getOnChangeCallback(): ?\Closure
	{
		return $this->onChangeCallback;
	}


	public function getOnFocusOutCallback(): ?\Closure
	{
		return $this->onFocusOutCallback;
	}
}

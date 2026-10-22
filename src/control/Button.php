<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Dial\SignalDial;
use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\SignalReceiver;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Html;

class Button extends \Nette\Forms\Controls\Button implements Renderable, Signalable, SignalReceiver
{
	use Helper\ButtonIcon;
	use Helper\ButtonVariant;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;
	use Helper\Render;
	use Helper\Size;

	/** @var ?\Closure(array<mixed>): void */
	protected ?\Closure $onChangeCallback = null;


	/**
	 * @param callable(array<mixed>): void $callback
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


	public function hasSignal(): bool
	{
		return is_callable($this->onChangeCallback);
	}


	public function signalReceived(string $signal): void
	{
		$presenter = $this->lookup(Presenter::class);

		if($signal !== SignalDial::OnClick)
		{
			throw new \Exception("Unknown signal '$signal' for button '" . $this->getName() . "'");
		}

		if($this->onChangeCallback === null)
		{
			throw new \Nette\InvalidStateException('OnClick callback not set for button "' . $this->getName() . '"');
		}

		$formData = $presenter->getParameter('formdata');

		if($formData === null)
		{
			return;
		}

		call_user_func_array($this->onChangeCallback, [Helper\FormData::parse($formData)]);
	}


	public function addSignalsToInput(Html &$input): void
	{
		if(!empty($this->onChangeCallback))
		{
			$presenter = $this->lookup(Presenter::class);

			$input->setAttribute('data-on-click', $presenter->link($this->lookupPath(Presenter::class) . IComponent::NameSeparator . SignalDial::OnClick . '!'));
		}
	}
}

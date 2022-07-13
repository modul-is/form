<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;
use Kravcik\LatteFontAwesomeIcon\Extension;

trait Signals
{
	public $onFocusOut = [];
	
	public $onChange = [];
	
	public string $onFocusOutSignal = 'onfocusout';
	
	public string $onChangeSignal = 'onchange';


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
	
	
	public function getSignalTooltip(): string
	{
		$waiting = Html::el('span')
			->class('input-group-text signal-waiting')
			->addHtml(Extension::render('arrow-right-to-bracket'));

		$loading = Html::el('span')
			->class('input-group-text signal-loading')
			->style('display', 'none')
			->addHtml(Extension::render('spinner fa-spin'));

		$success = Html::el('span')
			->class('input-group-text signal-success')
			->title('')
			->style('display', 'none')
			->addHtml(Extension::render('check', color: 'green'));

		$error = Html::el('span')
			->class('input-group-text signal-error')
			->title('')
			->style('display', 'none')
			->addHtml(Extension::render('times', color: 'red'));

		return $waiting . $loading . $success . $error;
	}
	
	
	public function addSignalsToInput(&$input)
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
		return $this->onChange || $this->onFocusOut;
	}
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use Stringable;

class DuplicatorContainer extends Container
{
	/**
	 * Callback dostane duplikátor a kontejner mazané položky - viz DuplicatorRemoveSubmit::addRemoveOnClick().
	 * Bez vlastní anotace se zdědí nesouhlasící @param z Nette\Forms\Container::addSubmit().
	 * @param ?\Closure(Control\Duplicator, DuplicatorContainer): void $callable
	 */
	public function addSubmit(string $name, Stringable|string|null $caption = null, $callable = null): Control\DuplicatorRemoveSubmit
	{
		$control = new Control\DuplicatorRemoveSubmit($caption);

		$control->setValidationScope([])
			->addRemoveOnClick($callable);

		return $this[$name] = $control;
	}
}

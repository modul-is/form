<?php

declare(strict_types = 1);

namespace ModulIS\Form;

class DuplicatorContainer extends Container
{
	public function addSubmit(string $name, $caption = '', $callable = null): Control\DuplicatorRemoveSubmit
	{
		$control = new Control\DuplicatorRemoveSubmit($caption);

		$control->setValidationScope([])
			->addRemoveOnClick($callable);

		return $this[$name] = $control;
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form;

class DuplicatorContainer extends Container
{
	public function addSubmit(string $name, $caption = null): Control\DuplicatorRemoveSubmit
	{
		$control = new Control\DuplicatorRemoveSubmit($caption);
		
		$control->setValidationScope([])
			->addRemoveOnClick();
		
		return $this[$name] = $control;
	}
}

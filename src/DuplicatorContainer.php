<?php

declare(strict_types=1);

namespace ModulIS\Form;

class DuplicatorContainer extends Container
{
	public function addSubmit(string $name, $caption = null): SubmitButton
	{
		return $this[$name] = new DuplicatorRemoveSubmit($caption);
	}
}

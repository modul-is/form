<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class DateTimeInput extends TextInput
{
	public function getControl(): Html
	{
		return parent::getControl()->addAttributes(['type' => 'datetime-local']);
	}
}

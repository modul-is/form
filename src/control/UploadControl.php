<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class UploadControl extends \Nette\Forms\Controls\UploadControl implements InputField
{
	use Helper\Field;

	public function getControl(): \Nette\Utils\Html
	{
		$control = parent::getControl();
		\assert($control instanceof \Nette\Utils\Html);

		return $control;
	}
}

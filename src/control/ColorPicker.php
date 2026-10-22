<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

/**
 * Color in #rrggbb format (Bootstrap form-control-color)
 */
class ColorPicker extends \Nette\Forms\Controls\ColorPicker implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;
}

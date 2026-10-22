<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Application\UI\SignalReceiver;

class MultiSelectBox extends \Nette\Forms\Controls\MultiSelectBox implements InputField, Signalable, SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class TextArea extends \Nette\Forms\Controls\TextArea implements InputField, Signalable, Helper\QuickCopyable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\QuickCopy;
	use Helper\Signals;
}

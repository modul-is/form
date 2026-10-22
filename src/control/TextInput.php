<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class TextInput extends \Nette\Forms\Controls\TextInput implements InputField, HasSuggestions, Signalable, Helper\QuickCopyable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Suggestions;
	use Helper\StrengthMeter;
	use Helper\QuickCopy;
	use Helper\Signals;
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class CheckboxList extends \Nette\Forms\Controls\CheckboxList implements Field, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\Description;
	use Helper\CoreList;
	use Helper\Render;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\WrapControl;
	use Helper\Size;
}

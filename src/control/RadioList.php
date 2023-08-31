<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class RadioList extends \Nette\Forms\Controls\RadioList implements Renderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\CoreList;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\RenderBasic;
	use Helper\Signals;
	use Helper\ToggleButton;
}

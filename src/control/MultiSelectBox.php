<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Application\UI\SignalReceiver;

class MultiSelectBox extends \Nette\Forms\Controls\MultiSelectBox implements Renderable, HasInputGroup, Signalable, SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\RenderBasic;
	use Helper\RenderInline;
	use Helper\WrapControl;
	use Helper\RenderFloating;
	use Helper\RenderDefault;
}

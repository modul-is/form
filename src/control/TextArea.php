<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class TextArea extends \Nette\Forms\Controls\TextArea implements Renderable, Signalable, Helper\QuickCopyable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\QuickCopy;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\Signals;
	use Helper\Render;
	use Helper\RenderDefault;
	use Helper\RenderFloating;
	use Helper\RenderInline;
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class MultiSelectBox extends \Nette\Forms\Controls\MultiSelectBox implements Renderable
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\RenderBasic;
}

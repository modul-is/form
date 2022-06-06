<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class RadioList extends \Nette\Forms\Controls\RadioList implements Renderable
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\CoreList;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\ValidationSuccessMessage;
}
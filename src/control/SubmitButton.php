<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class SubmitButton extends \Nette\Forms\Controls\SubmitButton implements Renderable
{
	use Helper\ButtonIcon;
	use Helper\ButtonVariant;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;
	use Helper\Render;
	use Helper\Size;
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class TextInput extends \Nette\Forms\Controls\TextInput
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Input;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
}

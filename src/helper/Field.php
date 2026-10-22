<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

/**
 * Settings of a text-like field - see Control\InputField
 */
trait Field
{
	use InputGroup;
	use Tooltip;
	use Description;
	use Render;
	use AutoRenderSkip;
	use Template;
	use Validation;
	use ControlClass;
	use RenderType;
	use WrapControl;
}

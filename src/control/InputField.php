<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Enum\RenderType;

/**
 * Text-like field (input, textarea, select, upload) - can be rendered in all render types and with an input group
 */
interface InputField extends Field, HasInputGroup
{
	public function getRenderType(): ?RenderType;
}

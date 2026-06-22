<?php

declare(strict_types = 1);

namespace ModulIS\Form\Enum;

enum RenderListType
{
	case Default;

	case Floating;

	case Inline;

	case Big;

	case Compact;
}

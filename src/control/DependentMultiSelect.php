<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class DependentMultiSelect extends \NasExt\Forms\Controls\DependentMultiSelectBox implements Renderable
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
	use Helper\RenderBasic;

	public function __construct($label = null, array $parents = [], callable $dependentCallback = null)
	{
		parent::__construct($label, $parents);

		$this->setDependentCallback($dependentCallback);
	}
}

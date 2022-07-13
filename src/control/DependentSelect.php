<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class DependentSelect extends \NasExt\Forms\Controls\DependentSelectBox implements Renderable, FloatingRenderable
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\RenderFloating;
	use Helper\Validation;
	use Helper\WrapClass;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\RenderBasic;

	public function __construct($label = null, array $parents = [], callable $dependentCallback = null)
	{
		parent::__construct($label, $parents);

		$this->setDependentCallback($dependentCallback);

		$this->controlClass = 'form-select';
	}
}

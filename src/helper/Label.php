<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Enum\RenderType;
use ModulIS\Form\Form;
use Nette\Utils\Html;
use function assert;

trait Label
{
	public function getCoreLabel()
	{
		$label = $this->getLabel();

		if($this->isRequired())
		{
			$required = Html::el('span')
				->class('required')
				->setText('*');

			$label->addHtml($required);
		}

		$tooltip = $this->getTooltipHtml();

		if(!$tooltip)
		{
			return $label;
		}

		$form = $this->getForm();
		assert($form instanceof Form);

		$renderType = $this->getRenderType() ?? $form->getRenderType();

		if($renderType === RenderType::Floating)
		{
			$label->addHtml(' ');
			if($this->isRequired())
			{
				$label->addHtml(Html::el('span')
					->class('required-inline-star')
					->setText('★'));
				$label->class($label->getAttribute('class') . ' has-inline-required-star');
			}
			$label->addHtml($tooltip);

			return $label;
		}

		return $label . $tooltip;
	}
}

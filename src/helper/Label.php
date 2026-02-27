<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Form;
use Nette\Utils\Html;
use function assert;

trait Label
{
	public function getCoreLabel()
	{
		$required = $this->isRequired() ? ' required' : '';

		$label = $this->getLabel()->class('col-form-label' . $required);

		if(!$this->tooltip)
		{
			return $label;
		}

		$tooltip = Html::el('span')
			->title($this->tooltip)
			->addAttributes(['data-bs-placement' => 'right', 'data-bs-toggle' => 'tooltip', 'data-bs-html' => 'true'])
			->addHtml(Extension::render('question-circle', color: 'blue'));

		$form = $this->getForm();
		assert($form instanceof Form);

		if($form->getRenderFloating() === true)
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

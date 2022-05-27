<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class DuplicatorRemoveSubmit extends SubmitButton
{
	public function addRemoveOnClick(?callable $callback = null)
	{
		$this->setValidationScope([]);

		$this->onClick[] = function(\Nette\Forms\Controls\SubmitButton $button) use ($callback): void
		{
			/** @var Duplicator $duplicator */
			$duplicator = $button->lookup(Duplicator::class);

			if(is_callable($callback))
			{
				$callback($duplicator, $button->parent);
			}

			$form = $button->getForm(false);

			if($form)
			{
				$form->onSuccess = [];
			}

			$duplicator->removeComponent($button->parent);
		};
	}
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

interface QuickCopyable
{
	public function getQuickCopy(): bool;


	public function getQuickCopyButton(): Html;
}

trait QuickCopy
{
	protected bool $quickCopy = false;


	public function setQuickCopy(bool $value = true): self
	{
		$this->quickCopy = $value;

		return $this;
	}


	public function getQuickCopy(): bool
	{
		return $this->quickCopy;
	}


	public function getQuickCopyButton(): Html
	{
		return Html::el('span')
			->class('input-group-text quick-copy-wrap')
			->addHtml(
				Html::el('button')
					->type('button')
					->class('btn quick-copy-btn')
					->setAttribute('title', 'Zkopírovat do schránky')
					->addHtml(Html::el('i')->class('fal fa-copy fa-fw'))
			);
	}
}

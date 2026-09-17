<?php

declare(strict_types = 1);

namespace ModulIS\Extension;

use Latte\CompileException;
use Latte\Compiler\PrintContext;


/**
 * {inputRender ...}
 *
 * Na rozdil od {inputCore}, ktery vykresli jen samotny input (getCoreControl), zavola
 * cele render() prvku - tedy i obalku s labelem, validaci a podle nastaveneho RenderType.
 * Je to tag ekvivalentni volani $form->getComponent('name')->render().
 */
class RenderInputNode extends \Nette\Bridges\FormsLatte\Nodes\InputNode
{
	public function print(PrintContext $context): string
	{
		if($this->part)
		{
			throw new CompileException('Tag {inputRender} cannot render a part of an input, use {inputCore} instead.', $this->position);
		}

		return $context->format(
			'$ʟ_rendered = $this->global->forms->get(%node, \ModulIS\Form\Control\Renderable::class)->render();'
			. ($this->attributes->items
				? ' if($ʟ_rendered instanceof \Nette\Utils\Html) { $ʟ_rendered->addAttributes(%2.node); }'
				: '')
			. ' echo $ʟ_rendered %3.line;',
			$this->name,
			$this->part,
			$this->attributes,
			$this->position,
		);
	}
}

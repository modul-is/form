<?php

declare(strict_types = 1);

namespace ModulIS\Extension;

use Latte\Compiler\PrintContext;


/**
 * {inputCore ...}
 */
class CoreInputNode extends \Nette\Bridges\FormsLatte\Nodes\InputNode
{
	public function print(PrintContext $context): string
	{
		/**
		 * Core control may be a plain string (e.g. lists), attributes can be added to Html only
		 */
		return $context->format(
			'$ʟ_core = $this->global->forms->get(%node)->'
			. ($this->part ? 'getCoreControlPart(%node)' : 'getCoreControl()') . ';'
			. ($this->attributes->items ? ' if($ʟ_core instanceof \Nette\Utils\Html) { $ʟ_core->addAttributes(%2.node); }' : '')
			. ' echo $ʟ_core %3.line;',
			$this->name,
			$this->part,
			$this->attributes,
			$this->position,
		);
	}
}

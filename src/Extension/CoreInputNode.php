<?php

declare(strict_types = 1);

namespace ModulIS\Extension;

use Latte\Compiler\Nodes\Php\Scalar\StringNode;
use Latte\Compiler\PrintContext;


/**
 * {inputCore ...}
 */
class CoreInputNode extends \Nette\Bridges\FormsLatte\Nodes\InputNode
{
	public function print(PrintContext $context): string
	{
		return $context->format(
			($this->name instanceof StringNode
				? 'echo end($this->global->formsStack)[%node]->'
				: '$ʟ_input = is_object($ʟ_tmp = %node) ? $ʟ_tmp : end($this->global->formsStack)[$ʟ_tmp]; echo $ʟ_input->')
			. ($this->part ? ('getCoreControlPart(%node)') : 'getCoreControl()')
			. ($this->attributes->items ? '->addAttributes(%2.node)' : '')
			. ' %3.line;',
			$this->name,
			$this->part,
			$this->attributes,
			$this->position,
		);
	}
}

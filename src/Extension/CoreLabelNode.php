<?php

declare(strict_types = 1);

namespace ModulIS\Extension;

use Latte\Compiler\Nodes\Php\Scalar\StringNode;
use Latte\Compiler\PrintContext;


/**
 * {labelCore ...} ... {/labelCore}
 */
class CoreLabelNode extends \Nette\Bridges\FormsLatte\Nodes\LabelNode
{
	public function print(PrintContext $context): string
	{
		return $context->format(
			($this->name instanceof StringNode
				? 'if($ʟ_label = end($this->global->formsStack)[%node]->'
				: '$ʟ_input = is_object($ʟ_tmp = %node) ? $ʟ_tmp : end($this->global->formsStack)[$ʟ_tmp]; if($ʟ_label = $ʟ_input->')
			. ($this->part ? ('getCoreLabelPart(%node)') : 'getCoreLabel()')
			. ') echo $ʟ_label'
			. ($this->attributes->items ? '->addAttributes(%2.node)' : '')
			. ($this->void ? ' %3.line;' : '->startTag() %3.line; %4.node if($ʟ_label) echo $ʟ_label->endTag() %5.line;'),
			$this->name,
			$this->part,
			$this->attributes,
			$this->position,
			$this->content,
			$this->endLine,
		);
	}
}

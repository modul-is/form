<?php

declare(strict_types = 1);

namespace ModulIS\Extension;

use Latte\Compiler\PrintContext;


/**
 * {labelCore ...} ... {/labelCore}
 */
class CoreLabelNode extends \Nette\Bridges\FormsLatte\Nodes\LabelNode
{
	public function print(PrintContext $context): string
	{
		/**
		 * Core label may be a plain string (label with tooltip) or null (checkbox) - tag methods are called on Html only
		 */
		return $context->format(
			'$ʟ_label = $this->global->forms->get(%node)->'
			. ($this->part ? 'getCoreLabelPart(%node)' : 'getCoreLabel()') . ';'
			. ($this->attributes->items ? ' if($ʟ_label instanceof \Nette\Utils\Html) { $ʟ_label->addAttributes(%2.node); }' : '')
			. ($this->void
				? ' echo $ʟ_label %3.line;'
				: ' echo $ʟ_label instanceof \Nette\Utils\Html ? $ʟ_label->startTag() : $ʟ_label %3.line; %4.node if($ʟ_label instanceof \Nette\Utils\Html) echo $ʟ_label->endTag() %5.line;'),
			$this->name,
			$this->part,
			$this->attributes,
			$this->position,
			$this->content,
			$this->tagRanges ? end($this->tagRanges) : $this->position,
		);
	}
}

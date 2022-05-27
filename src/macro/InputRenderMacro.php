<?php

declare(strict_types=1);

namespace ModulIS\Form;

use Latte\CompileException;

final class InputRenderMacro extends \Latte\Macros\MacroSet
{
	public static function install(\Latte\Compiler $compiler)
	{
		$set = new static($compiler);

		$set->addMacro('inputCore', [$set, 'macroInput']);
		$set->addMacro('labelCore', [$set, 'macroLabel']);

		return $set;
	}


	public function macroInput(\Latte\MacroNode $node, \Latte\PhpWriter $writer)
	{
		if($node->modifiers)
		{
			throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
		}

		$words = $node->tokenizer->fetchWords();

		if(!$words)
		{
			throw new CompileException('Missing name in ' . $node->getNotation());
		}

		$node->replaced = true;
		$name = array_shift($words);

		return $writer->write(
			($name[0] === '$' ? '$_input = is_object(%0.word) ? %0.word : end($this->global->formsStack)[%0.word]; echo $_input' : 'echo end($this->global->formsStack)[%0.word]')
					. '->%1.raw'
					. ($node->tokenizer->isNext() ? '->addAttributes(%node.array)' : '')
					. " /* line $node->startLine */",
			$name,
			$words ? 'getCoreControlPart(' . implode(', ', array_map([$writer, 'formatWord'], $words)) . ')' : 'getCoreControl()'
		);
	}


	public function macroLabel(\Latte\MacroNode $node, \Latte\PhpWriter $writer)
	{
		if($node->modifiers)
		{
			throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
		}

		$words = $node->tokenizer->fetchWords();

		if(!$words)
		{
			throw new CompileException('Missing name in ' . $node->getNotation());
		}

		$node->replaced = true;
		$name = array_shift($words);

		return $writer->write(
			($name[0] === '$' ? '$_input = is_object(%0.word) ? %0.word : end($this->global->formsStack)[%0.word]; if ($_label = $_input' : 'if ($_label = end($this->global->formsStack)[%0.word]')
					. '->%1.raw) echo $_label'
					. ($node->tokenizer->isNext() ? '->addAttributes(%node.array)' : ''),
			$name,
			$words ? ('getCoreLabelPart(' . implode(', ', array_map([$writer, 'formatWord'], $words)) . ')') : 'getCoreLabel()'
		);
	}
}

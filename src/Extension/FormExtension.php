<?php

declare(strict_types = 1);

namespace ModulIS\Extension;

final class FormExtension extends \Latte\Extension
{
	public function getTags(): array
	{
		return [
			'labelCore' => [\ModulIS\Extension\CoreLabelNode::class, 'create'],
			'inputCore' => [\ModulIS\Extension\CoreInputNode::class, 'create']
		];
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

interface Signalable
{
	public function hasSignal(): bool;

	public function addSignalsToInput(\Nette\Utils\Html &$input): void;

	public function getSignalTooltip(): string;
}

<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

interface HasInputGroup
{
	/**
	 * HTML of the input group text before the control
	 */
	public function getPrepend(): ?string;

	public function getPrependClass(): ?string;

	/**
	 * HTML of the input group text after the control
	 */
	public function getAppend(): ?string;

	public function getAppendClass(): ?string;

	/**
	 * Icon inside the control (form-adorn)
	 */
	public function getIcon(): ?string;
}

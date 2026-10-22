<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait InputGroup
{
	protected ?string $prepend = null;

	protected ?string $prependClass = null;

	protected ?string $append = null;

	protected ?string $appendClass = null;

	protected ?string $icon = null;

	protected ?string $prefix = null;

	protected ?string $suffix = null;


	/**
	 * Input group text before the control - HTML
	 */
	public function setPrepend(string|\Stringable $html, ?string $class = null): static
	{
		$this->prepend = (string) $html;
		$this->prependClass = $class;

		return $this;
	}


	/**
	 * Input group text after the control - HTML
	 */
	public function setAppend(string|\Stringable $html, ?string $class = null): static
	{
		$this->append = (string) $html;
		$this->appendClass = $class;

		return $this;
	}


	/**
	 * Icon inside the control (Bootstrap form-adorn), name is passed to the icon renderer
	 */
	public function setIcon(string $icon): static
	{
		$this->icon = $icon;

		return $this;
	}


	public function getPrepend(): ?string
	{
		return $this->prepend === '' ? null : $this->prepend;
	}


	public function getPrependClass(): ?string
	{
		return $this->prependClass;
	}


	public function getAppend(): ?string
	{
		return $this->append === '' ? null : $this->append;
	}


	public function getAppendClass(): ?string
	{
		return $this->appendClass;
	}


	public function getIcon(): ?string
	{
		return $this->icon;
	}


	/**
	 * Text inside the input before the value (e.g. $) - Bootstrap form-adorn
	 */
	public function setPrefix(?string $text): static
	{
		$this->prefix = $text;

		return $this;
	}


	public function getPrefix(): ?string
	{
		return $this->prefix === '' ? null : $this->prefix;
	}


	/**
	 * Text inside the input after the value (e.g. a unit) - Bootstrap form-adorn
	 */
	public function setSuffix(?string $text): static
	{
		$this->suffix = $text;

		return $this;
	}


	public function getSuffix(): ?string
	{
		return $this->suffix === '' ? null : $this->suffix;
	}
}

<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

class TestPresenter extends \Nette\Application\UI\Presenter
{
	public function __construct()
	{
		parent::__construct();

		$this->changeAction('');
		$this->setParent(null, '');
		$this->injectPrimary(null, null, new \Nette\Routing\RouteList, new \Nette\Http\Request(new \Nette\Http\UrlScript()), new \Nette\Http\Response);
	}
}
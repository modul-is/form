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
		$this->injectPrimary(new \Nette\Http\Request(new \Nette\Http\UrlScript()), new \Nette\Http\Response, new \Nette\Application\PresenterFactory,  new \Nette\Routing\RouteList);
	}
}
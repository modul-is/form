<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class ContainerTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$container = $form->addContainer('container');

		$container->addText('text', 'Text');

		$container->addSubmit('save', 'Uložit');

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('container')->render()->__toString());
	}


	public function testRenderEmpty()
	{
		$form = $this->getForm();

		$form->addContainer('container');

		Assert::same('', $form->getComponent('container')->render());
	}


	public function testRenderId()
	{
		$form = $this->getForm();

		$container = $form->addContainer('container');

		$container->setId('customId');

		$container->addText('text', 'Text');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('container')->render()->__toString());
	}


	public function testRenderCard()
	{
		$form = $this->getForm();

		$container = $form->addContainer('container');

		$container->addSubmit('save', 'Uložit');

		$container->showCard(true)
			->setTitle('Title');

		$container->addText('text', 'Text');

		$this->assertRender(__DIR__ . '/card.latte', $form->getComponent('container')->render()->__toString());
	}
}

$testcase = new ContainerTest;
$testcase->run();

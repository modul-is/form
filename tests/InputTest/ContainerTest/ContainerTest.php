<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class ContainerTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;
		
		$container = $form->addContainer('container');

		$container->addText('text', 'Text');
		
		$container->addSubmit('save', 'Uložit');
		
		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('container')->render()->__toString());
	}
	
	
	public function testRenderInputsPerRow()
	{
		$form = new Form;
		
		$container = $form->addContainer('container');

		$container->addText('text', 'Text');
		
		$container->addText('text1', 'Text1');
		
		$container->setInputsPerRow(2);
		
		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/inputsPerRow.latte'));

		Assert::same($html, $form->getComponent('container')->render()->__toString());
	}
	
	
	public function testRenderEmpty()
	{
		$form = new Form;

		$form->addContainer('container');

		Assert::same('', $form->getComponent('container')->render());
	}


	public function testRenderId()
	{
		$form = new Form;

		$container = $form->addContainer('container');
		
		$container->setId('customId');

		$container->addText('text', 'Text');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('container')->render()->__toString());
	}
	
	
	public function testRenderCard()
	{
		$form = new Form;

		$container = $form->addContainer('container');
		
		$container->addSubmit('save', 'Uložit');
		
		$container->showCard(true)
			->setTitle('Title');

		$container->addText('text', 'Text');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/card.latte'));

		Assert::same($html, $form->getComponent('container')->render()->__toString());
	}
}

$testcase = new ContainerTest;
$testcase->run();

<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class UploadTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor');
		
		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setPrepend('prepend');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/prepend.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setAppend('append');
		
		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/append.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setIcon('user');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/icon.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setOption('id', 'customId');

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setTemplate(__DIR__ . '/customUpload.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('file')->render());
	}


	public function testRenderHidden()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('file')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$form->addUpload('file', 'Vyberte soubor')
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('file')->render());
	}
}

$testcase = new UploadTest;
$testcase->run();

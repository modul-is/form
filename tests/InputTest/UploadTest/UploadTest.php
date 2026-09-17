<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class UploadTest extends TestCase
{
	private int $uploadSize = 0;


	public function setUp(): void
	{
		$this->uploadSize = $this->getUploadFilesize();
	}

	public function testRender()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor');

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('file')->render()->__toString(), ['__UPLOAD_SIZE__' => $this->uploadSize]);
	}


	public function testRenderPrepend()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setPrepend('prepend');

		$this->assertRender(__DIR__ . '/prepend.latte', $form->getComponent('file')->render()->__toString(), ['__UPLOAD_SIZE__' => $this->uploadSize]);
	}


	public function testRenderAppend()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setAppend('append');

		$this->assertRender(__DIR__ . '/append.latte', $form->getComponent('file')->render()->__toString(), ['__UPLOAD_SIZE__' => $this->uploadSize]);
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setIcon('user');

		$this->assertRender(__DIR__ . '/icon.latte', $form->getComponent('file')->render()->__toString(), ['__UPLOAD_SIZE__' => $this->uploadSize]);
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setOption('id', 'customId');

		$this->assertRender(__DIR__ . '/id.latte', $form->getComponent('file')->render()->__toString(), ['__UPLOAD_SIZE__' => $this->uploadSize]);
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setTemplate(__DIR__ . '/customUpload.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('file')->render());
	}


	public function testRenderHidden()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('file')->render());
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('file')->render());
	}


	function getUploadFilesize(): ?int
	{
		$iniUploadValue = ini_get('upload_max_filesize');

		$units = ['B', 'K', 'M', 'G'];
		$number = substr($iniUploadValue, 0, -1);
		$suffix = strtoupper(substr($iniUploadValue, -1));

		//B or no suffix
		if(is_numeric(substr($suffix, 0, 1)))
		{
			return preg_replace('/[^\d]/', '', $iniUploadValue);
		}

		$exponent = array_flip($units)[$suffix] ?? null;

		if($exponent === null)
		{
			return null;
		}

		return $number * (1024 ** $exponent);
	}
}

$testcase = new UploadTest;
$testcase->run();

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

		$html = str_replace(["__UPLOAD_SIZE__", "\t", "\n", "\r"], [$this->uploadSize], file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setPrepend('prepend');

		$html = str_replace(["__UPLOAD_SIZE__", "\t", "\n", "\r"], [$this->uploadSize], file_get_contents(__DIR__ . '/prepend.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setAppend('append');

		$html = str_replace(["__UPLOAD_SIZE__", "\t", "\n", "\r"], [$this->uploadSize], file_get_contents(__DIR__ . '/append.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setIcon('user');

		$html = str_replace(["__UPLOAD_SIZE__", "\t", "\n", "\r"], [$this->uploadSize], file_get_contents(__DIR__ . '/icon.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = $this->getForm();

		$form->addUpload('file', 'Vyberte soubor')
			->setOption('id', 'customId');

		$html = str_replace(["__UPLOAD_SIZE__", "\t", "\n", "\r"], [$this->uploadSize], file_get_contents(__DIR__ . '/id.latte'));

		Assert::same($html, $form->getComponent('file')->render()->__toString());
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

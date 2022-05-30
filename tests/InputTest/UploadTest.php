<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class UploadTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;
		
		$form->addUpload('file', 'Vyberte soubor');
		
		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-file" class="col-form-label ">Vyberte soubor</label></div><div class="col-sm-8"><div class="input-group"><div class="custom-file"><input type="file" name="file" id="frm-file" data-nette-rules=\'[{"op":":fileSize","msg":"The size of the uploaded file can be up to 419430400 bytes.","arg":419430400}]\' class="upload custom-file-input  "><label class="custom-file-label" for="frm-file">Není vybrán soubor</label></div></div></div></div>';
		
		
		Assert::same($string, $form->getComponent('file')->render()->__toString());
	}
	
	
	public function testRenderPrepend()
	{
		$form = new Form;
		
		$form->addUpload('file', 'Vyberte soubor')
			->setPrepend('prepend');
		
		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-file" class="col-form-label ">Vyberte soubor</label></div><div class="col-sm-8"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">prepend</span></div><div class="custom-file"><input type="file" name="file" id="frm-file" data-nette-rules=\'[{"op":":fileSize","msg":"The size of the uploaded file can be up to 419430400 bytes.","arg":419430400}]\' class="upload custom-file-input  "><label class="custom-file-label" for="frm-file">Není vybrán soubor</label></div></div></div></div>';
		
		Assert::same($string, $form->getComponent('file')->render()->__toString());
	}
	
	
	public function testRenderAppend()
	{
		$form = new Form;
		
		$form->addUpload('file', 'Vyberte soubor')
			->setAppend('append');
		
		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-file" class="col-form-label ">Vyberte soubor</label></div><div class="col-sm-8"><div class="input-group"><div class="custom-file"><input type="file" name="file" id="frm-file" data-nette-rules=\'[{"op":":fileSize","msg":"The size of the uploaded file can be up to 419430400 bytes.","arg":419430400}]\' class="upload custom-file-input  "><label class="custom-file-label" for="frm-file">Není vybrán soubor</label></div><div class="input-group-append"><span class="input-group-text">append</span></div></div></div></div>';
		
		Assert::same($string, $form->getComponent('file')->render()->__toString());
	}
	
	
	public function testRenderIcon()
	{
		$form = new Form;
		
		$form->addUpload('file', 'Vyberte soubor')
			->setIcon('user');
		
		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-file" class="col-form-label ">Vyberte soubor</label></div><div class="col-sm-8"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><span class="fal fa-user fa-fw"></span></span></div><div class="custom-file"><input type="file" name="file" id="frm-file" data-nette-rules=\'[{"op":":fileSize","msg":"The size of the uploaded file can be up to 419430400 bytes.","arg":419430400}]\' class="upload custom-file-input  "><label class="custom-file-label" for="frm-file">Není vybrán soubor</label></div></div></div></div>';
		
		Assert::same($string, $form->getComponent('file')->render()->__toString());
	}
	
	
	public function testRenderOptionId()
	{
		$form = new Form;
		
		$form->addUpload('file', 'Vyberte soubor')
			->setOption('id', 'customId');
		
		$string = '<div class="form-group row" id="customId"><div class="col-sm-4 control-label align-self-center"><label for="frm-file" class="col-form-label ">Vyberte soubor</label></div><div class="col-sm-8"><div class="input-group"><div class="custom-file"><input type="file" name="file" id="frm-file" data-nette-rules=\'[{"op":":fileSize","msg":"The size of the uploaded file can be up to 419430400 bytes.","arg":419430400}]\' class="upload custom-file-input  "><label class="custom-file-label" for="frm-file">Není vybrán soubor</label></div></div></div></div>';
		
		Assert::same($string, $form->getComponent('file')->render()->__toString());
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
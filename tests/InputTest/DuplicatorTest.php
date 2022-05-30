<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class DuplicatorTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$duplicator = $form->addDuplicator('duplicator', function(ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');

			$container->addSubmit('del', 'Smazat')
				->setValidationScope(null)
				->addRemoveOnClick();
		});

		$duplicator->addSubmit('add', 'Přidat')
			->setValidationScope(null)
			->addCreateOnClick(true);

		$duplicator->setValues([['text' => 'Text']]);

		$string = '<div id="containerDuplicator" class="card card-accent-primary"><div class="card-body"><div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-duplicator-0-text" class="col-form-label ">text</label></div><div class="col-sm-8"><div class="input-group"><input type="text" name="duplicator[0][text]" id="frm-duplicator-0-text" value="Text" class="form-control  "></div></div></div><button class="btn btn-xs btn-danger float-right " name="duplicator[0][del]" formnovalidate="" type="submit"><span class="fal fa-times fa-fw"></span>Smazat</button><div class="clearfix"></div><hr /></div><div class="card-footer"><button class="btn btn-primary float-left btn-xs " name="duplicator[add]" value="Přidat" formnovalidate="" data-nette-validation-scope=\'["multiplier"]\' label="Přidat" type="submit"><span class="fal fa-plus fa-fw"></span>Přidat</button></div></div>';

		Assert::same($string, $form->getComponent('duplicator')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$duplicator = $form->addDuplicator('duplicator', function(ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');

			$container->addSubmit('del', 'Smazat')
				->setValidationScope(null)
				->addRemoveOnClick();
		});

		$duplicator->addSubmit('add', 'Přidat')
			->setValidationScope(null)
			->addCreateOnClick(true);

		$duplicator->setTemplate(__DIR__ . '/customDuplicator.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('duplicator')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$duplicator = $form->addDuplicator('duplicator', function(ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');

			$container->addSubmit('del', 'Smazat')
				->setValidationScope(null)
				->addRemoveOnClick();
		});

		$duplicator->addSubmit('add', 'Přidat')
			->setValidationScope(null)
			->addCreateOnClick(true);

		$duplicator->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('duplicator')->render());
	}
}

$testcase = new DuplicatorTest;
$testcase->run();

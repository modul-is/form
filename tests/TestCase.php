<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

use Tester\Assert;

abstract class TestCase extends \Tester\TestCase
{
	public function getForm()
	{
		$form = new \ModulIS\Form\Form;

		$submit = $form->addSubmit('save');

		$form->setSubmittedBy($submit);

		(new TestPresenter)->addComponent($form, 'form');

		return $form;
	}


	/**
	 * Porovna vykreslene HTML s fixture souborem - tabulatory a konce radku se ignoruji,
	 * fixture tedy muze byt zalomeny pro citelnost.
	 *
	 * Fixtures lze po zmene renderovani hromadne pregenerovat:
	 *     BLESS_FIXTURES=1 vendor/bin/tester -C tests/
	 * Vysledek je nutne projit a zkontrolovat, nez se commitne.
	 *
	 * @param array<string, string> $placeholders zastupne znacky ve fixture, napr. ['__UPLOAD_SIZE__' => '2M']
	 */
	protected function assertRender(string $fixturePath, string $actual, array $placeholders = []): void
	{
		if(getenv('BLESS_FIXTURES'))
		{
			$blessed = $actual;

			foreach($placeholders as $placeholder => $value)
			{
				$blessed = str_replace((string) $value, $placeholder, $blessed);
			}

			file_put_contents($fixturePath, $blessed);
		}

		$expected = str_replace(
			array_merge(array_keys($placeholders), ["\t", "\n", "\r"]),
			array_values($placeholders),
			file_get_contents($fixturePath)
		);

		Assert::same($expected, $actual);
	}
}

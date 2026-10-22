<?php

declare(strict_types = 1);

namespace ModulIS\Form\Demo\Form;

use ModulIS\Form\DuplicatorContainer;
use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;
use ModulIS\Form\Helper\DependentData;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Formular s ukazkou vsech inputu z modul-is/form - slouzi k vyvoji a overeni
 * vykreslovani a chovani knihovny po aktualizaci, nic neuklada.
 */
class DemoForm extends FormComponent
{
	private const array Brands = [
		'skoda' => 'Škoda',
		'vw' => 'Volkswagen',
		'toyota' => 'Toyota'
	];

	private const array Models = [
		'skoda' => ['fabia' => 'Fabia', 'octavia' => 'Octavia', 'superb' => 'Superb', 'kodiaq' => 'Kodiaq'],
		'vw' => ['golf' => 'Golf', 'passat' => 'Passat', 'tiguan' => 'Tiguan'],
		'toyota' => ['yaris' => 'Yaris', 'corolla' => 'Corolla', 'rav4' => 'RAV4']
	];

	private const array Cities = [
		1 => 'Praha',
		2 => 'Brno',
		3 => 'Ostrava',
		4 => 'Plzeň',
		5 => 'Liberec',
		6 => 'Olomouc',
		7 => 'České Budějovice',
		8 => 'Hradec Králové',
		9 => 'Ústí nad Labem',
		10 => 'Pardubice'
	];

	/** @var array<string, mixed>|null */
	private ?array $submittedValues = null;


	public function prepare(): void
	{
		$this->getComponent('form')->setDefaults([
			'text_default' => 'Výchozí hodnota',
			'currency' => 1_250_000,
			'slider_single' => 40,
			'slider_range' => [20, 60],
			'switch' => true,
			'radio_default' => 'b'
		]);
	}


	public function createComponentForm(): Form
	{
		$form = $this->getForm();
		$form->setTitle('Testovací formulář modul-is/form');
		$form->setIcon('ui-checks');
		$form->setAjax();

		$this->addTextInputs($form);
		$this->addRenderTypes($form);
		$this->addDateInputs($form);
		$this->addSelects($form);
		$this->addLists($form);
		$this->addSliders($form);
		$this->addUploads($form);
		$this->addContainers($form);
		$this->addSignals($form);

		$form->addSubmit('save', 'Odeslat');

		$form->addSubmit('saveNoValidate', 'Odeslat bez validace')
			->setValidationScope([])
			->setColor('warning')
			->setIcon('skip-forward');

		$form->addButton('reset', 'Reset')
			->setHtmlAttribute('type', 'reset')
			->setColor('secondary')
			->setIcon('arrow-counterclockwise');

		$form->addLink('back', 'Zpět')
			->setLink($this->getPresenter()->link('this'))
			->setColor('light')
			->setIcon('arrow-left');

		$form->onSuccess[] = $this->formSuccess(...);

		return $form;
	}


	private function addTextInputs(Form $form): void
	{
		$form->addGroup('Textové inputy')
			->setIcon('keyboard');

		$form->addText('text_default', 'Text (povinný, tooltip, quick copy)')
			->setRequired('Vyplňte text')
			->setTooltip('Nápověda k textovému poli')
			->setQuickCopy()
			->setWidth(6);

		$form->addText('text_icon', 'Text s ikonou a placeholderem')
			->setIcon('person')
			->setHtmlAttribute('placeholder', 'Jan Novák')
			->setDescription('setDescription() – nápověda pod polem')
			->setWidth(6);

		$form->addText('text_prepend', 'Prepend / append')
			->setPrepend('https://')
			->setAppend('.cz')
			->setWidth(6);

		$form->addText('text_pattern', 'PSČ (pattern, 5 číslic)')
			->setNullable()
			->addRule($form::Pattern, 'PSČ musí mít 5 číslic', '\d{5}')
			->setWidth(6);

		$form->addEmail('email', 'E-mail')
			->setIcon('envelope')
			->setWidth(6);

		$form->addPassword('password', 'Heslo (min. 6 znaků)')
			->setNullable()
			->addRule($form::MinLength, 'Heslo musí mít alespoň %d znaků', 6)
			->setWidth(6);

		$form->addInteger('integer', 'Celé číslo (1–100)')
			->setNullable()
			->addRule($form::Range, 'Zadejte číslo 1 až 100', [1, 100])
			->setWidth(4);

		$form->addFloat('float', 'Desetinné číslo')
			->setNullable()
			->setAppend('%')
			->setWidth(4);

		$form->addCurrency('currency', 'Měna', 'CZK')
			->setWidth(4);

		$form->addAutocomplete('autocomplete', 'Autocomplete (statické návrhy)', null, array_values(self::Cities))
			->setWidth(6);

		$form->addText('text_disabled', 'Zakázaný input')
			->setDefaultValue('Nelze upravit')
			->setDisabled()
			->setWidth(6);

		$form->addTextArea('textarea', 'Textarea', null, 3)
			->setQuickCopy()
			->addRule($form::MaxLength, 'Maximálně %d znaků', 500);

		$form->addDivider(Html::el('p')->class('text-muted small mb-0')->setText('addDivider() – libovolný obsah vložený mezi inputy.'));

		$form->addHidden('hidden', 'skrytá hodnota');
	}


	private function addRenderTypes(Form $form): void
	{
		$form->addGroup('Typy vykreslení')
			->setIcon('palette')
			->setColor('info');

		$form->addText('render_default', 'Default')
			->setRenderDefault()
			->setWidth(4);

		$form->addText('render_floating', 'Floating')
			->setRenderFloating()
			->setWidth(4);

		$form->addSelect('render_floating_select', 'Floating select', self::Brands)
			->setPrompt('-- vyberte --')
			->setRenderFloating()
			->setWidth(4);

		$form->addText('render_inline', 'Inline text')
			->setRenderInline()
			->setWidth(6);

		$form->addSelect('render_inline_select', 'Inline select', self::Brands)
			->setRenderInline()
			->setWidth(6);

		$form->addTextArea('render_floating_textarea', 'Floating textarea')
			->setRenderFloating();
	}


	private function addDateInputs(Form $form): void
	{
		$form->addGroup('Datum a čas')
			->setIcon('calendar');

		$form->addDate('date', 'Datum (min. dnes)')
			->addRule($form::Min, 'Datum nesmí být v minulosti', new \DateTimeImmutable('today'))
			->setWidth(3);

		$form->addDateTime('datetime', 'Datum a čas')
			->setWidth(3);

		$form->addDateTime('datetime_seconds', 'Datum a čas se sekundami', true)
			->setWidth(3);

		$form->addTime('time', 'Čas')
			->setWidth(3);

		$form->addDateWeek('week', 'Týden')
			->setWidth(3);

		$form->addDate('date_floating', 'Datum floating')
			->setRenderFloating()
			->setWidth(3);
	}


	private function addSelects(Form $form): void
	{
		$form->addGroup('Výběry')
			->setIcon('list');

		$form->addSelect('select', 'Select s promptem', self::Brands)
			->setPrompt('-- vyberte --')
			->setRequired('Vyberte značku')
			->setTooltip('Povinný select')
			->setWidth(6);

		$form->addMultiSelect('multiselect', 'Multiselect', self::Cities)
			->setWidth(6);

		$form->addSelect('brand', 'Značka (rodič dependent selectu)', self::Brands)
			->setPrompt('-- vyberte značku --')
			->setWidth(4);

		$form->addDependentSelect('model', 'Model (dependent select)', [$form->getComponent('brand')])
			->setDependentCallback(fn(array $parents): DependentData => new DependentData(
				items: self::Models[$parents['brand']] ?? [],
				prompt: '-- vyberte model --',
			))
			->setDisabledWhenEmpty()
			->setWidth(4);

		$form->addDependentMultiSelect('models', 'Modely (dependent multiselect)', [$form->getComponent('brand')])
			->setDependentCallback(fn(array $parents): DependentData => new DependentData(
				items: self::Models[$parents['brand']] ?? [],
			))
			->setWidth(4);

		$form->addWhisperer('whisperer', 'Whisperer (statický)', self::Cities)
			->setPrompt('Vyberte město')
			->setWidth(4);

		$form->addWhisperer('whisperer_search', 'Whisperer (hledání přes AJAX)', [])
			->setOnSearchChangeCallback($this->searchCities(...))
			->setOnSelectCallback(function(mixed $selected, array $formValues): void
			{
				$this->flash('Whisperer vybral: ' . Json::encode($selected));
			})
			->setWidth(4);

		$form->addMultiWhisperer('multiwhisperer', 'Multi whisperer', self::Cities)
			->setWidth(4);
	}


	private function addLists(Form $form): void
	{
		$form->addGroup('Checkboxy a radia')
			->setIcon('check-square');

		$form->addCheckbox('checkbox', 'Checkbox')
			->setTooltip('Nápověda ke checkboxu')
			->setWidth(4);

		$form->addCheckbox('switch', 'Switch')
			->setSwitch()
			->setWidth(4);

		$form->addCheckbox('checkbox_required', 'Souhlasím (povinný)')
			->setRequired('Musíte souhlasit')
			->setWidth(4);

		$items = ['a' => 'Možnost A', 'b' => 'Možnost B', 'c' => 'Možnost C', 'd' => 'Možnost D'];
		$icons = ['a' => 'house', 'b' => 'car-front', 'c' => 'tree', 'd' => 'bicycle'];
		$tooltips = ['a' => 'Popis A', 'b' => 'Popis B', 'c' => 'Popis C', 'd' => 'Popis D'];

		$form->addCheckboxList('checkboxlist_default', 'CheckboxList – default', $items)
			->setWidth(6);

		$form->addCheckboxList('checkboxlist_compact', 'CheckboxList – compact', $items)
			->setRenderCompact()
			->setWidth(6);

		$form->addCheckboxList('checkboxlist_inline', 'CheckboxList – inline, 2 na řádek', $items)
			->setRenderInline()
			->setItemsPerRow(2)
			->setWidth(6);

		$form->addCheckboxList('checkboxlist_floating', 'CheckboxList – floating', $items)
			->setRenderFloating()
			->setWidth(6);

		$form->addCheckboxList('checkboxlist_big', 'CheckboxList – big (dlaždice)', $items)
			->setRenderBig()
			->setIconArray($icons)
			->setTooltips($tooltips);

		$form->addRadioList('radio_default', 'RadioList – default', $items)
			->setWidth(6);

		$form->addRadioList('radio_compact', 'RadioList – compact', $items)
			->setRenderCompact()
			->setWidth(6);

		$form->addRadioList('radio_inline', 'RadioList – inline, barevné', $items)
			->setRenderInline()
			->setItemsColor('success')
			->setWidth(6);

		$form->addRadioList('radio_toggle', 'RadioList – přepínací tlačítka', $items)
			->setToggleButton()
			->setWidth(6);

		$form->addRadioList('radio_floating', 'RadioList – floating', $items)
			->setRenderFloating()
			->setWidth(6);

		$form->addRadioList('radio_big', 'RadioList – big (dlaždice, povinný)', $items)
			->setRenderBig()
			->setIconArray($icons)
			->setTooltips($tooltips)
			->setRequired('Vyberte dlaždici');
	}


	private function addSliders(Form $form): void
	{
		$form->addGroup('Posuvníky')
			->setIcon('sliders');

		$form->addSlider('slider_single', 'Slider (0–100, krok 5)', 0, 100, 5)
			->setWidth(4);

		$form->addSlider('slider_range', 'Slider rozsah', 0, 100, 10)
			->setRange()
			->setWidth(4);

		$form->addSlider('slider_items', 'Slider z hodnot', null, null)
			->setItems([2020, 2021, 2022, 2023, 2024, 2025, 2026])
			->showTooltip(false)
			->setWidth(4);
	}


	private function addUploads(Form $form): void
	{
		$form->addGroup('Upload')
			->setIcon('upload');

		$form->addUpload('upload', 'Soubor (max. 2 MB)')
			->addRule($form::MaxFileSize, 'Soubor může mít maximálně 2 MB', 2 * 1024 * 1024)
			->setWidth(6);

		$form->addMultiUpload('multiupload', 'Více souborů')
			->setWidth(6);
	}


	private function addContainers(Form $form): void
	{
		$form->addGroup('Kontejnery a duplikátor')
			->setIcon('layers');

		$container = $form->addContainer('address');
		$container->showCard(true);
		$container->setTitle('Kontejner jako karta');
		$container->setColor('primary');

		$container->addText('street', 'Ulice')
			->setWidth(6);

		$container->addText('city', 'Město')
			->setWidth(4);

		$container->addText('zip', 'PSČ')
			->setWidth(2);

		$container->addCheckbox('primary', 'Hlavní adresa');

		$duplicator = $form->addDuplicator('persons', function(DuplicatorContainer $container): void
		{
			$container->addText('name', 'Jméno')
				->setRequired('Vyplňte jméno osoby')
				->setWidth(5);

			$container->addDate('birth', 'Datum narození')
				->setWidth(4);

			$container->addSelect('role', 'Role', ['owner' => 'Vlastník', 'tenant' => 'Nájemník'])
				->setWidth(3);

			$container->addSubmit('remove', 'Odebrat');
		}, 1);

		$duplicator->setTitle('Duplikátor osob');
		$duplicator->addSubmit('add', 'Přidat osobu');
	}


	private function addSignals(Form $form): void
	{
		$form->addGroup('AJAX callbacky')
			->setIcon('lightning')
			->setColor('warning');

		$form->addText('signal_change', 'onChange (text)')
			->setOnChangeCallback(function(mixed $value, string $inputName, array $formValues): void
			{
				$this->flash("onChange '$inputName': " . Json::encode($value));
			})
			->setWidth(4);

		$form->addText('signal_focusout', 'onFocusOut (text)')
			->setOnFocusOutCallback(function(mixed $value, string $inputName, array $formValues): void
			{
				$this->flash("onFocusOut '$inputName': " . Json::encode($value));
			})
			->setWidth(4);

		$form->addSelect('signal_select', 'onChange (select)', self::Brands)
			->setPrompt('-- vyberte --')
			->setOnChangeCallback(function(mixed $value, string $inputName, array $formValues): void
			{
				$this->flash("onChange '$inputName': " . Json::encode($value));
			})
			->setWidth(4);

		$form->addCheckbox('signal_checkbox', 'onChange (checkbox)')
			->setOnChangeCallback(function(mixed $value, string $inputName, array $formValues): void
			{
				$this->flash("onChange '$inputName': " . Json::encode($value));
			})
			->setWidth(4);

		$form->addSlider('signal_slider', 'onChange (slider)', 0, 10)
			->setOnChangeCallback(function(mixed $value, string $inputName, array $formValues): void
			{
				$this->flash("onChange '$inputName': " . Json::encode($value));
			})
			->setWidth(4);

		$form->addButton('signal_button', 'Tlačítko s onChange')
			->setColor('info')
			->setIcon('lightning')
			->setOnChangeCallback(function(array $formValues): void
			{
				$this->flash('Tlačítko – data formuláře: ' . Json::encode($formValues));
			});
	}


	/**
	 * @param array<string, mixed> $parents
	 * @return array<int, string>
	 */
	private function searchCities(mixed $query, array $parents): array
	{
		$query = Strings::lower(Strings::toAscii((string) $query));

		return array_filter(
			self::Cities,
			fn(string $city): bool => str_contains(Strings::lower(Strings::toAscii($city)), $query)
		);
	}


	private function flash(string $message): void
	{
		$presenter = $this->getPresenter();
		$presenter->flashMessage($message, 'info');
		$presenter->redrawControl('flashMessages');
	}


	public function formSuccess(Form $form, ArrayHash $values): void
	{
		$this->submittedValues = $this->normalize((array) $form->getValues('array'));

		$submitter = $form->isSubmitted();

		if($submitter instanceof SubmitButton)
		{
			$this->flash('Formulář odeslán tlačítkem „' . $submitter->getName() . '“.');
		}

		$this->getPresenter()->redrawControl('result');
	}


	/**
	 * @return array<string, mixed>|null
	 */
	public function getSubmittedValues(): ?array
	{
		return $this->submittedValues;
	}


	private function normalize(mixed $value): mixed
	{
		if(is_array($value))
		{
			return array_map($this->normalize(...), $value);
		}

		if($value instanceof \Nette\Http\FileUpload)
		{
			return $value->isOk() ? $value->getUntrustedName() . ' (' . $value->getSize() . ' B)' : null;
		}

		if($value instanceof \DateTimeInterface)
		{
			return $value->format('Y-m-d H:i:s');
		}

		return $value;
	}
}

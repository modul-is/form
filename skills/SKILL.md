---
name: modulis-form
description: >-
  Vyvolat před úpravou nebo přidáním formulářů postavených na balíčku modul-is/form.
  Použít u FormComponent, createComponentForm, prepare(), getForm(), ModulIS\Form\Form,
  vlastních controlů (addWhisperer, addMultiWhisperer, addDuplicator, addDependentSelect,
  addDependentMultiSelect, addDate, …), validace, událostí formuláře, vykreslení přes Latte
  ({control}, :js, *Js.latte), BS5 karet/addGroup, toggle/setOption id vs setHtmlId,
  kontejnerů setId, nebo při integraci s naja/AJAX. Obecné Nette Forms (factory v presenteru,
  addText, onSuccess, pravidla) platí z dokumentace Nette — tento skill doplňuje vrstvu ModulIS.
  Povinné pravidlo: při zadání typu "chci nový form/formulář" v jakémkoliv projektu
  nejdřív načti tento skill a použij ho jako primární referenční vzor implementace.
---

# ModulIS Form (modul-is/form)

## 0. Povinný start pro nový formulář

Pokud uživatel napíše požadavek typu **"chci nový form"**, **"vytvoř nový formulář"** nebo ekvivalent:

1. **Nejprve otevři a projdi tento skill (`skills/SKILL.md`)** — ještě před návrhem kódu.
2. **Ber tento repozitář jako zdroj pravdy a vzor** pro strukturu `FormComponent`, `createComponentForm()`, `prepare()`, volání `getForm()`, naming a použití controlů.
3. **Až potom** navrhuj/implementuj formulář v projektu.
4. Když se cílový projekt odchyluje, drž se lokálních konvencí projektu, ale API a postupy `modul-is/form` validuj podle tohoto dokumentu.
5. **`prepare()` je povinná metoda každého nového FormComponentu.** Nevynechávej ji ani u jednoduchých formulářů.
6. Pokud je projekt nový a bez konvencí, použij výchozí baseline z tohoto skillu (sekce **1.1 Greenfield baseline**).

Rozšíření Nette Forms o BS5 renderer, vlastní controly a `FormComponent`. Obecné vzory (validace, `createComponent*`, factory) jsou v [dokumentaci Nette Forms](https://doc.nette.org/cs/forms) a ve skillu [nette-forms](https://github.com/nette/claude-code/blob/master/plugins/nette/skills/nette-forms/SKILL.md).

## Jak dokument číst

1. **Koncept** — FormComponent, Latte, ID.  
2. **Reference** — co umí `Form` / controle (bez dlouhých kódů).  
3. **Duplikátor** — zvláštní případ struktury.  
4. **Příklady podle tématu** — úryvky s *Kontext:*.  
5. **Kompaktní recepty** — časté kombinace (prepare, toggle, telefon…).  
6. **Provozní vzory** — checklist chování v aplikaci.  
7. **Odkazy**.

Konvence ukázek: pokud je to možné, používej v příkladech **`use` importy nahoře** místo FQCN (`\Vendor\Package\Class`) přímo v těle kódu.
V ukázkách drž stejný styl závorek jako v projektu: otevírací `{` na nový řádek.

---

## 1. FormComponent a životní cyklus

**Povinné minimum pro nový formulář:** `prepare()` + `createComponentForm()`.  
`prepare()` je závazná součást lifecycle (defaulty, editace, AJAX redraw).

### 1.1 Greenfield baseline (když projekt nemá konvence)

Použij tyto výchozí konvence, dokud uživatel neurčí jiné:

- Název komponenty: `XxxForm` (např. `OrderForm`), volání v Latte `{control xxxForm}`.
- Soubory: `XxxForm.php`, `xxxForm.latte`, volitelně `xxxFormJs.latte`.
- Povinné metody: `prepare(): void` a `createComponentForm(): Form`.
- Factory formuláře: vždy přes `$this->getForm()` (ne přes `new Form`).
- ID pravidla: `setOption('id')` pro obal/toggle target, `setHtmlId()` jen pro input element.
- Styl kódu: `declare(strict_types = 1);` + `use` importy nahoře (bez FQCN v signaturách).
- DI styl: constructor injection, preferovaně `private readonly` pro služby/repozitáře.

Pokud požadavek neupřesní režim odesílání:

- výchozí je klasický submit (bez `setAjax()`),
- `setAjax()` používej u formulářů s redrawi, signály, dependent poli nebo v modálu.

```php
use ModulIS\Form\Form;
use ModulIS\Form\FormComponent;

final class MujForm extends FormComponent
{
	public function prepare(): void
	{
		$form = $this->getComponent('form');
		// setDefaults, setDefaultValue, redrawControl('form') při AJAX…
	}

	public function createComponentForm(): Form
	{
		$form = $this->getForm();
		// $form->setAjax();
		// $form->addGroup('…');
		return $form;
	}
}
```

- **`prepare()` je povinná metoda** a volá ji presenter nebo rodičovská komponenta (`$this->getComponent('mujForm')->prepare()`).
- Šablona: `MujForm.php` → `mujForm.latte`; volitelně `setRenderManually(true)` podle README balíčku.

---

## 2. Latte a JavaScript

- `{control mujForm}` — vykreslení komponenty.
- `{control mujForm:js}` — `renderJs()` → soubor **`mujFormJs.latte`** ve stejné složce jako třída.
- V `*Js.latte`: `<script n:nonce>`, Latte může vložit `{$form->getComponent('pole')->getHtmlId()}`; u chosen často `|replace:'-','_'`.

---

## 3. Identifikátory (`id`) — obal vs. input

| Účel | API |
|------|-----|
| Obal pole (toggle, blok v DOM) | `->setOption('id', 'slug')` |
| Skutečný `id` inputu | `->setHtmlId('slug')` |
| Obal `Container` | `$container->setId('slug')` |
| `id` BS karty skupiny | `$form->addGroup('…')`<br>`->setOption('id', '…')` |

Pro **`toggle('x')`** musí na cíli sedět **`setOption('id', 'x')`** (nebo ekvivalent na skupině / kontejneru).

---

## 4. Reference API balíčku (`src/control`, `src/helper`)

Tato část popisuje **co kde hledat**; konkrétní PHP úryvky jsou v [sekci 6](#6-příklady-kódu-podle-tématu) a [sekci 7](#7-kompaktní-recepty).

### 4.1 Úroveň formuláře (`ModulIS\Form\Form`)

**`setAjax`**, **`setRenderFloating`**, **`setRenderInline`**, **`setTitle`**, **`setIcon`**, **`setColor`**, **`setNoValidate`**, **`setButtonClass`**, **`setDefaultInputWrapClass`**, **`addGroup`** s **`setClass` / `setColor` / `setOption('id')` na kartu**, **`addError`** + **`getFormErrors`**, **`renderForm()`** (HTML bez Latte `{form}`).

#### `setAjax()` vs. klasický submit

`Form::$ajax` je **default `false`** (`src/Form.php:34`). `setAjax()` ho jen flipne; `formComponent.latte` pak přidá CSS třídu `ajax` na `<form>` a duplikátorová tlačítka (viz `DuplicatorRemoveSubmit::getCoreControl()`, `DuplicatorCreateSubmit::getCoreControl()`).

| Režim | Volání | Co se stane při submitu |
|-------|--------|------------------------|
| **AJAX** | `$form->setAjax();` | `naja` zachytí submit přes class `ajax`, redraw snippetů (`redrawControl('form')`), žádný reload. |
| **Klasický** | bez `setAjax()` | Klasický POST + redirect z `onSuccess` (`$presenter->redirect(...)`), full page reload. |

Volba režimu je **nezávislá** na `prepare()`, `getForm()` i renderu — můžeš mít obě varianty se stejným zbytkem kódu. Vol klasický submit u jednoduchých uložení s následným redirectem (CRUD, jednorázové akce) a AJAX u formulářů s duplikátory, dependent selecty, signály, nebo tam, kde je formulář v modálu.

**Smíšený režim** je možný:

- AJAX form + nějaké tlačítko bez AJAXu (nepoužívá `class="ajax"`) → individuální submit jde klasicky.
- Klasický form + tlačítko/odkaz s `setClass('btn-back-validate ajax')` → individuální tlačítko jde AJAXem.

Šablona se po nastavení `setAjax` chová automaticky (auto-render přidá `class ajax`); u manuálních šablon (`{form ..., class => '... ajax'}`) si třídu spravuješ sám.

### 4.2 Tovární metody `Form` / `Container`

`ModulIS\Form\Form` i `ModulIS\Form\Container` sdílí stejné **`add*`**. Přehled: text (`addText`, `addEmail`, `addInteger`, `addFloat`, `addPassword`, `addTextArea`, `addAutocomplete`), výběr (`addCheckbox`, `addRadioList`, `addCheckboxList`, `addSelect`, `addMultiSelect`, `addWhisperer`, `addMultiWhisperer`, `addDependentSelect`, `addDependentMultiSelect`), datum/čas (`addDate`, `addDateTime`, `addTime`, `addDateWeek`), soubory (`addUpload`, `addMultiUpload`), skryté (`addHidden`), akce (`addSubmit`, `addButton`, `addLink`), struktura (`addContainer`, `addDuplicator`, `addDivider`). Signatury: **`src/Form.php`**, **`src/Container.php`**.

### 4.3 Společné vlastnosti controlů (traity)

**`setWrapClass`**, **`setLabelWrapClass`**, **`setInputWrapClass`**, **`setTemplate(path, params)`**, **`setTooltip`**, **`setColor`**, plovoucí label (control + **`$form->setRenderFloating()`**), **`setRenderInline`**, **`setPrepend` / `setAppend`**, **`setAutorenderSkip`**, **`setHtmlAttribute`**, u textu **`setQuickCopy`**, u tlačítek/odkazů **`setIcon`**.

### 4.4 Select a MultiSelect (včetně báze pro Whisperer)

**`setImageArray([hodnota => url, …])`** — třída `select2-image` a skrytý blok `data-src` pro JS (chosen/select2).

### 4.5 RadioList

**`setBig()`**, **`setItemsColor`**, **`setValuesFromEnum()`** pro enum implementující **`ModulIS\Form\Enum\RadioEnum`** (`getList()`, `getDescription()`).

### 4.6 CheckboxList

**CoreList**: **`setItemsPerRow`**, **`setItemClass`**, **`setTooltips`**, **`setWrapAttributes`**. **`setToggleButton()`** + **`setButtonColor`**.

### 4.7 Whisperer a MultiWhisperer

Třída **`form-control-chosen`**. **`setParents`** + dependent callback. **`setOnSelectCallback`** × **`setOnChangeCallback`** se nesmí kombinovat. **`setOnSearchChangeCallback`**. **`addWhisperer`** doplní prázdnou položku `''`, pokud chybí.

### 4.8 Autocomplete (`addAutocomplete`)

**`setPrompt`**, **`setParents`**, callbacky (stejná pravidla vzájemnosti jako u whispereru), položky v konstruktoru, napojení na **`src/js/form.js`**.

**JS závislost (povinná kontrola):** `AutocompleteInput` v runtime volá globální `autocomplete()` (jQuery plugin z **`src/js/jquery.autocomplete.min.js`**). Pokud cílový projekt tuto knihovnu nemá zaregistrovanou v bundleru (vedle `naja` a `form.js`), v konzoli vyskočí **`ReferenceError: autocomplete is not defined`** při každém AJAX redrawu. Před přidáním `addAutocomplete` ověř:

1. že projekt už `addAutocomplete` někde používá (`grep -r addAutocomplete app/`), nebo
2. že má `jquery.autocomplete.min.js` zaregistrovaný v JS bundleru.

Pokud ani jedno, prvek nepřidávej (nebo nejdřív zaregistruj knihovnu).

### 4.9 Datum a čas

**`DateTimeInput`** / Nette `DateTimeControl`; ve **`Form`** jsou formáty a české chybové hlášky.

### 4.10 Upload

**`addUpload`**, **`addMultiUpload`** + stejné layout traity jako u inputů.

### 4.11 Link, Submit, Button

**`setLink`**, výchozí třídy tlačítek z **`$form->setButtonClass`**.

### 4.12 Signály (trait **Signals**)

**`setOnFocusOutCallback`**, **`setOnChangeCallback`** → data atributy + **`inputSignal()`** v **`form.js`**.

### 4.13 Validátory (`Form` / `FormValidator`)

```php
use ModulIS\Form\Form;

$input->addRule(Form::Greater, 'Musí být větší než %d', 0);
$input->addRule(Form::Less, 'Musí být menší než %d', 100);
$input->addRule(Form::SameLength, 'Stejná délka jako srovnání', $druhyRetezec);
// Form::GreaterEqual / LessEqual — aliasy k Nette Min / Max
```

Dále **`Form::ValidateRC`**, **`Form::ValidateIC`** — implementace **`FormValidator`**.

---

## 5. Duplikátor

```php
use ModulIS\Form\DuplicatorContainer;

$form->addDuplicator('rows', function (DuplicatorContainer $c) {
	$c->addText('text', 'Text');
	// 3. argument addSubmit() je $callback (volitelný)
	$c->addSubmit('del', 'Smazat', function () {
		$this->redrawControl('form');
	});
}, 1);

// Tlačítko „přidat“ patří na duplikátor (ne do factory).
$duplicator->addSubmit('add', 'Přidat', function () {
	$this->redrawControl('form');
});
```

Factory **typuj `DuplicatorContainer`**. Tlačítko „přidat“ patří na **duplikátor**, ne do vnitřní továrny.

**Pozor — `addCreateOnClick()` / `addRemoveOnClick()` se NEvolají ručně:**

- `Duplicator::addSubmit($name, $caption, $callback)` interně volá `addCreateOnClick(true, $callback)` (viz `src/control/Duplicator.php`).
- `DuplicatorContainer::addSubmit($name, $caption, $callback)` interně volá `addRemoveOnClick($callback)` (viz `src/DuplicatorContainer.php`).

Pokud po `addSubmit()` ručně doplníš ještě `->addCreateOnClick()` nebo `->addRemoveOnClick()`, **navěsíš onClick callback dvakrát**. Pro 99 % případů stačí předat callback jako 3. argument do `addSubmit()`. Explicitní volání `addCreateOnClick`/`addRemoveOnClick` má smysl jen když si potřebuješ změnit `allowEmpty` u create submit nebo neumíš callback předat při vytváření.

Rozšířený příklad duplikátoru: v části **Duplikátor** u [§ 6.19](#619-kontejner-oddělovač-submit-odkaz-signály).

---

## 6. Příklady kódu podle tématu

Úryvky jsou **zkrácené** (bez importů dialů a helperů aplikace). **Kontext:** = typická situace.

### 6.1 Úroveň formuláře a skupina

*Kontext: detail / vícekrok — AJAX, barevná skupina (karta).*

```php
$form = $this->getForm();
$form->setAjax();

$form->addGroup('Kontaktní osoba')
	->setClass('bg-blue-40');
```

### 6.2 Text, e-mail, integer, desetinné, textarea

*Kontext: osoba/firma; číselný sloupec; dlouhý text s limitem.*

```php
$form->addText('name', 'Jméno a příjmení / Název firmy', maxLength: 255)
	->setHtmlAttribute('placeholder', 'Zadejte celé jméno')
	->setWrapClass('col-12 col-sm-6 mb-1')
	->setRequired();

$form->addText('email', 'Email', maxLength: 150)
	->addRule(Form::Email)
	->setRequired();

$form->addInteger('phone', 'Telefon')
	->addConditionOn(/* $phoneCode */, Form::IsIn, [/* CZ */, /* SK */])
		->addRule(Form::Length, 'Telefon musí mít 9 znaků', 9);

$form->addText('column_width', 'Šířka sloupce')
	->addRule($form::FLOAT);

$form->addTextArea('message', 'Textace', rows: 8)
	->setHtmlAttribute('class', 'summernote')
	->addRule($form::MaxLength, arg: 65535)
	->setRequired();
```

### 6.3 Heslo a `addFloat`

*Kontext: přihlášení; desetinné číslo s pravidlem Float.*

```php
$form->addPassword('heslo', 'Heslo');
$form->addFloat('cena_mena', 'Částka');
```

### 6.4 Checkbox

*Kontext: příznak (viník, souhlas).*

```php
$form->addCheckbox('liability_guilty', 'Viník');
```

### 6.5 RadioList (velké, Html, toggle)

*Kontext: výběr s bohatým popisem a podmíněnými bloky.*

```php
$form->addRadioList('person', '', $personArray)
	->setBig()
	->setLabelWrapClass('d-none')
	->setItemsPerRow(2)
	->setItemsColor('blue-40')
	->setItemClass('ps-0 pe-1')
	->addCondition(Form::Equal, /* hodnota */ 1)
		->toggle('reporter-name');
```

### 6.6 RadioList z enumu (`RadioEnum`)

```php
use ModulIS\Form\Enum\RadioEnum;

enum MujTyp: string implements RadioEnum
{
	case A = 'a';
	case B = 'b';
	public static function getList(): array { return ['a' => 'A', 'b' => 'B']; }
	public static function getDescription(): array { return ['a' => '', 'b' => '']; }
}

$form->addRadioList('stav', 'Stav')
	->setValuesFromEnum(MujTyp::class)
	->setBig();
```

### 6.7 CheckboxList a MultiWhisperer

*Kontext: více příjemců, reakce na změnu (`setOnChangeCallback`).*

`setOnChangeCallback` navěsí AJAX signál (`data-on-change`); při změně pole zavolá Nette callback s argumenty **`(mixed $value, string $inputName, array $formValues)`** — `$value` je hodnota změněného prvku, `$formValues` je zbytek dat formuláře z aktuálního POSTu signálu (po `parse_str`, bez prázdných hodnot). Implementace: trait **`Helper\Signals`**.

```php
// Uvnitř metody komponenty (např. createComponentForm) má closure automaticky $this = FormComponent.
$onRecipientsChange = function (mixed $value, string $inputName, array $formValues): void {
	// $value — hodnota pole, které změnu vyvolalo; $formValues — ostatní odeslaná pole signálu
	if ($this->getPresenter()->isAjax()) {
		$this->redrawControl('form'); // nebo vlastní snippet, např. náhled příjemců
	}
};

$form->addCheckboxList('internal_recipient', 'Hlavní příjemci', /* $pairs */)
	->setItemsPerRow(3)
	->setLabelWrapClass('col-12')
	->setInputWrapClass('col-12')
	->setRequired()
	->setOnChangeCallback($onRecipientsChange);

$form->addMultiWhisperer('main_recipient', 'Kontakty', /* $pairs */)
	->setTooltip(/* … */)
	->setLabelWrapClass('col-12')
	->setInputWrapClass('col-12')
	->setWrapClass('col-6')
	->setOnChangeCallback($onRecipientsChange);
```

Mimo třídu komponenty předej např. **`[$this, 'onRecipientsChanged']`** nebo closure s **`use ($komponenta)`**.

V aplikacích používajících balíček se v closure často volí **`use ($form)`**, aby šlo při přepočtu polí volat **`$form->setDefaults(...)`**, **`$form->getComponent(...)`**; u checkboxů ve **`$formValues`** se může objevit hodnota **`'on'`** (serializace z requestu). Stejný callback lze předat i jako **`$this->someHandler(...)`** (first-class callable), pokud metoda přijímá ty tři argumenty.

### 6.8 Select (vlajky, prompt)

```php
$form->addSelect('phone_code', 'Předvolba')
	->setItems(/* dial předvoleb */)
	->setWrapClass('d-none')
	->setImageArray(/* mapa vlajek */)
	->setDefaultValue(/* výchozí kód */);

$form->addSelect('mail_template', 'Emailová šablona', /* $pairs */)
	->setPrompt(' ~ Vyberte šablonu ~ ');
```

### 6.9 MultiSelect

```php
$form->addMultiSelect('tagy', 'Štítky', ['a' => 'A', 'b' => 'B'], size: 4);
```

### 6.10 Skrytá pole

```php
$form->addHidden('recaptcha_token')
	->setHtmlId('recaptcha_token');
$form->addHidden('other_party')
	->setDefaultValue(true);
```

### 6.11 Datum a datum-čas

```php
$form->addDate('date_event', 'Datum události');

$form->addDateTime('cond_value_date', 'Hodnota podmínky')
	->setOption('id', 'cond_value_date')
	->setTooltip('Podmínku pro datum je potřeba definovat ve tvaru YYYY-mm-dd HH:ii:ss');
```

### 6.12 Čas a týden

```php
$form->addTime('slot', 'Čas')
	->setRequired();
$form->addDateWeek('tyden', 'Týden');
```

### 6.13 Upload

```php
$form->addUpload('file', 'Excel')
	->setTooltip('Vkládejte pouze soubory s příponou .xlsx')
	->addRule(Form::MimeType, arg: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
```

### 6.14 Autocomplete

```php
$form->addAutocomplete('mesto', 'Město', itemArray: ['praha' => 'Praha', 'brno' => 'Brno'])
	->setPrompt('Začni psát…');
```

### 6.15 Whisperer s dynamickým hledáním

```php
$form->addWhisperer('contract_number', 'ČPS')
	->setHtmlAttribute('data-whisperer-delay', 1000)
	->setOnSearchChangeCallback(function ($part) {
		return /* páry z API */ [];
	});
```

### 6.16 Whisperer v kontejneru

```php
$reporter = $form->addContainer('reporter');
$reporter->addWhisperer('user_id', 'Kontaktní osoba', /* $pairs */)
	->setInputWrapClass('col-12')
	->setLabelWrapClass('col-12')
	->setHtmlId('user-id');
```

### 6.17 Dependent select (řetěz)

Callback dostává **jedno pole** (v testech balíčku `$parentArray`): klíče = **jména rodičovských controlů** (`'class_module'`, `'class'`, …), hodnoty = jejich aktuální hodnoty. Musí vracet **`DependentData`**.

- **Testy balíčku** (`tests/InputTest/DependentTest/DependentTest.php`): po tříargumentovém `addDependentSelect` navěsit **`setDependentCallback(function (array $parentArray) { … })`**.
- **Čtvrtý argument** `addDependentSelect(..., $callable)` dělá totéž (předává se v **`Form::addDependentSelect`** do konstruktoru).

Kromě vzoru z testů balíčku (`setDependentCallback`) se často předává přímo **4. parametr** `addDependentSelect` — např. **`[$this, 'entityCallback']`** s metodou **`public function entityCallback(array $values): DependentData`** (parametr se typicky jmenuje `$values`, `$parents` nebo `$inputs`; klíče pole odpovídají **`getName()`** rodičů, např. `'class_module'`, `'partnerId'`), **inline closure** `function (array $parents) { return new DependentData(...); }` včetně **pojmenovaných argumentů** konstruktoru (`items:`, `value:`, `prompt:` dle **`DependentData::__construct`**), nebo **first-class callable** **`$this->názevMetody(...)`** s návratovým typem **`DependentData`**.

**Pozor na prompt u `addDependentMultiSelect`** (single `addDependentSelect` se to netýká):

- **`DependentSelect`** dědí `setPrompt(?string $prompt)` z `Nette\Forms\Controls\SelectBox` — **nullable**, takže prompt může chybět jak v `DependentData`, tak na controlu.
- **`DependentMultiSelect`** má **vlastní override** `setPrompt(string $prompt)` v `src/control/DependentMultiSelect.php` — **non-nullable**. V `Helper\Dependent::tryLoadItems()` se volá `$this->setPrompt($data->getPrompt() ?: $this->getPrompt())` — pokud `DependentData` prompt nemá **a** control sám taky ne, předá se `null` a balíček shodí runtime **`TypeError: Argument #1 ($prompt) must be of type string, null given`**.

U **multi** stačí jedna z těchto cest (alespoň jedna musí prompt poskytnout):

```php
// (1) prompt v DependentData
return new DependentData(items: $items, prompt: '~ Vyberte ~');

// (2) prompt přímo na controlu
$form->addDependentMultiSelect('x', 'X', [$form['parent']])
	->setPrompt('~ Vyberte ~')
	->setDependentCallback(fn (array $p) => new DependentData($items));
```

**Poznámka:** některé existující formuláře volají **`new ModulIS\Form\Form`** místo **`$this->getForm()`** u `FormComponent` — pak chybí výchozí registrace **`onError`** z `FormComponent::getForm()`. Pro nový kód preferuj **`getForm()`**.

```php
use ModulIS\Form\Helper\DependentData;

$form->addSelect('class_module', 'Modul', /* $modules */)
	->setPrompt('~ Vyberte modul ~')
	->setRequired();

$form->addDependentSelect('class', 'Třída', [$form['class_module']])
	->setDependentCallback(function (array $parentArray): DependentData {
		if (($parentArray['class_module'] ?? null) === /* např. klíč modulu */ 'objednavky') {
			$data = ['o1' => 'Objednávka — typ A', 'o2' => 'Objednávka — typ B'];
		} else {
			$data = ['f1' => 'Faktura — standard', 'f2' => 'Faktura — záloha'];
		}

		return new DependentData(items: $data, prompt: '~ Vyberte třídu ~');
	})
	->setRequired();

$form->addDependentSelect('property', 'Sloupec', [$form['class_module'], $form['class']])
	->setDependentCallback(function (array $parentArray): DependentData {
		$modul = $parentArray['class_module'] ?? null;
		$class = $parentArray['class'] ?? null;
		// … výběr sloupců podle $modul a $class …

		return new DependentData(items: [/* value => label */], prompt: '~ Vyberte sloupec ~');
	})
	->setRequired();
```

Logiku lze vytknout do metody komponenty a předat ji jako **`[$this, 'název']`** nebo **`$this->název(...)`** (4. argument), případně **`fn (array $parentArray): DependentData => $this->název($parentArray)`** (viz také § 7.6).

### 6.18 Dependent multi-select

Stejný tvar callbacku jako u **`DependentSelect`** (jedno pole rodičovských hodnot → **`DependentData`**). Opět platí 4. argument vs. **`->setDependentCallback()`**.

```php
use ModulIS\Form\Helper\DependentData;

$form->addDependentMultiSelect(
	'vyber',
	'Kategorie',
	[$form['class_module']],
	function (array $parentArray): DependentData {
		return new DependentData(['a' => 'A', 'b' => 'B']);
	}
);
```

### 6.19 Kontejner, oddělovač, submit, odkaz, signály

**Kontejner**

```php
$client = $form->addContainer('client');
$client->addHidden('crm_id')
	->setNullable();
$client->addText('ico', 'IČO', maxLength: 50)
	->setNullable();
```

**Oddělovač**

```php
$form->addDivider(
	Html::el('div')
		->addHtml(Html::el('strong')
			->setText('Majitel vozidla'))
		->class('mb-3'),
);
```

**Duplikátor**

```php
use ModulIS\Form\DuplicatorContainer;

$injuryDuplicator = $vehicle->addDuplicator('injuryDuplicator', function (DuplicatorContainer $container) use ($form) {
	$container->addHidden('injuryId');
	$container->addText('name', 'Jméno a příjmení', maxLength: 255)
		->setTemplate(/* required.latte */, ['component' => $container->getComponent('name')]);
	$container->addSubmit('del', 'Smazat')
		->addRemoveOnClick();
});
$injuryDuplicator->addSubmit('add', 'Přidat')
	->addCreateOnClick(true);
```

**Submit a zpět**

```php
$form->addSubmit('save', 'Uložit')
	->setIcon('save')
	->setColor('success');

$form->addSubmit('backValidate', 'Zpět')
	->setIcon('')
	->setColor('grey-light')
	->setClass('btn-back-validate ajax')
	->setHtmlAttribute('formnovalidate', true);
```

**Odkaz**

```php
$form->addLink('return', 'Zpět na seznam')
	->setLink($presenter->link('User:default'))
	->setColor('secondary');
```

**Signály**

```php
$form->addText('ico', 'IČO')
	->setOnFocusOutCallback(function (mixed $value, string $inputName, array $values): void {
		// služba / payload
	});
```

*Telefon v jedné Latte šabloně: viz [§ 7.4](#74-telefon-předvolba--číslo-v-jedné-šabloně) a § 8 (provozní vzor).*

---

## 7. Kompaktní recepty

### 7.1 `prepare()` z presenteru

```php
public function actionDetail(int $id): void
{
	$this->getComponent('mujForm')->prepare();
}
```

### 7.2 `prepare()` + defaults + AJAX redraw

```php
public function prepare(): void
{
	$form = $this->getComponent('form');
	$zaznam = $this->repository->getById($this->id);

	if ($zaznam !== null) {
		$form->setDefaults($zaznam->toArray());
	}

	if ($this->getPresenter()->isAjax()) {
		$this->redrawControl('form');
	}
}
```

### 7.3 `toggle` + `setOption('id')`

```php
use ModulIS\Form\Form;

$form->addRadioList('typ', '', ['a' => 'Varianta A', 'b' => 'Varianta B'])
	->addCondition(Form::Equal, 'a')
		->toggle('blok-a');

$form->addText('detail_a', 'Pole jen pro A')
	->setOption('id', 'blok-a');
```

### 7.4 Telefon: předvolba + číslo v jedné šabloně

```php
$form->addSelect('phone_code', 'Předvolba')
	->setItems(['+420' => 'CZ', '+421' => 'SK'])
	->setWrapClass('d-none');

$phone = $form->addInteger('phone', 'Telefon');
$phone->setTemplate(
	__DIR__ . '/phoneInput.latte',
	[
		'phoneCode' => $form->getComponent('phone_code'),
		'phone' => $phone,
	]
);

$phone->addConditionOn($form->getComponent('phone_code'), Form::IsIn, ['+420', '+421'])
	->addRule(Form::Length, 'Zadejte 9 číslic', 9);
```

V Latte: jeden `input-group` — `{input $phoneCode}` a `{input $phone}`.

### 7.5 Duplikátor s `use` v closure

```php
use ModulIS\Form\DuplicatorContainer;

$prefix = 'položka';
$duplicator = $form->addDuplicator('radky', function (DuplicatorContainer $c) use ($prefix) {
	$c->addText('nazev', $prefix . ' — název');
	$c->addSubmit('odebrat', 'Odebrat')
		->addRemoveOnClick();
}, 1);

$duplicator->addSubmit('pridat', 'Přidat řádek')
	->addCreateOnClick(true);
```

### 7.6 Dependent select — jednoduchý callback

Callback je stejný jako v **`tests/InputTest/DependentTest/DependentTest.php`**: jeden argument pole (níže `$parentArray`, klíč = jméno rodiče, zde `'modul'`). Lze ho předat jako **4. argument** `addDependentSelect`, nebo jako **`->setDependentCallback(...)`** po tříargumentové variantě — obojí dělá totéž (`Form::addDependentSelect`).

```php
use ModulIS\Form\Helper\DependentData;

$form->addSelect('modul', 'Modul', ['objednavky' => 'Objednávky', 'faktury' => 'Faktury']);

$form->addDependentSelect('sloupec', 'Sloupec', [$form['modul']], function (array $parentArray): DependentData {
	$modul = $parentArray['modul'] ?? null;
	$polozky = match ($modul) {
		'objednavky' => ['id' => 'ID', 'stav' => 'Stav'],
		'faktury' => ['cislo' => 'Číslo', 'datum' => 'Datum'],
		default => [],
	};

	return new DependentData($polozky);
});
```

### 7.7 Několik whispererů jako filtry

```php
$form->addWhisperer('contract_number', 'Číslo smlouvy', $this->repo->fetchPairsCps());
$form->addWhisperer('plate', 'RZ', $this->repo->fetchPairsPlate());
$form->addWhisperer('vin', 'VIN', $this->repo->fetchPairsVin());
```

### 7.8 AJAX a ruční šablona komponenty

```php
use ModulIS\Form\Form;

public function createComponentForm(): Form
{
	$form = $this->getForm();
	$form->setAjax();
	return $form;
}
```

`$this->renderManually = true;` a v `mujForm.latte` vlastní layout + `{control form}`.

### 7.8.1 Klasický submit bez AJAX

```php
public function createComponentForm(): Form
{
	$form = $this->getForm(); // bez setAjax() => klasický POST + redirect

	$form->addText('name', 'Název')->setRequired();
	$form->addSubmit('save', 'Uložit');

	$form->onSuccess[] = function(Form $form, ArrayHash $values): void
	{
		$this->repository->save(/* ... */);
		$this->getPresenter()->flashMessage('Uloženo', 'success');
		$this->getPresenter()->redirect('default'); // full reload
	};

	return $form;
}
```

Stejný formulář s `setAjax()` zůstane na stránce a redrawne snippet `form` (typicky bez `redirect()` v `onSuccess`, místo toho `redrawControl('form')` nebo `redirect('this')` — záleží na UX).

### 7.9 Více `{control :js}` a vnoření

```latte
{control hlavicka:js}
{control mujForm:js}
```

```latte
<script n:nonce>
	{control vnorenyFormular:js}
</script>
```

### 7.10 Naja po AJAX snippetu

```javascript
naja.snippetHandler.addEventListener('afterUpdate', () => {
	// znovu navěsit listenery
});
```

### 7.11 Pole s hvězdičkou (required šablona)

```php
$input = $form->addText('jmeno', 'Jméno')
	->setRequired();
$input->setTemplate(
	__DIR__ . '/fragments/requiredField.latte',
	['component' => $input]
);
```

### 7.12 Upload pattern (single/multi) + validace názvu

```php
$form->addMultiUpload('doc', 'Přílohy')
	->setTooltip('Pomocí SHIFT/CTRL lze vybrat více souborů');

foreach ($values->doc as $upload)
{
	if (!$upload->isOk() || !$upload->hasFile())
	{
		continue;
	}

	$name = $upload->getName();
	$ext = strrchr((string) $name, '.');
	$safeName = strlen((string) $name) > 100
		? substr((string) $name, 0, 100 - strlen((string) $ext)) . $ext
		: $name;

	// uložit metadata do DB
	// vytvořit cílovou složku
	$upload->move($targetPath . DIRECTORY_SEPARATOR . $safeName);
}
```

K souborům se často doplňuje `handleDownloadFile()` / `handleDeleteFile()` a výpis uložených souborů v `beforeRender()`.

### 7.13 FormComponent: `getForm()` vs. `new Form`

```php
use ModulIS\Form\Form;

public function createComponentForm(): Form
{
	$form = $this->getForm(); // preferovaný způsob ve FormComponent
	$form->setAjax();
	return $form;
}
```

Ve `FormComponent` preferuj `getForm()`. `new Form` používej jen výjimečně (např. mimo FormComponent), jinak snadno vznikne nekonzistence v chování komponenty (`onError`, render, navázání callbacků).

### 7.14 AJAX callback v duplikátoru (přepočet defaults)

```php
use ModulIS\Form\DuplicatorContainer;

$duplicator = $form->addDuplicator('rows', function (DuplicatorContainer $container) use ($form)
{
	$post = $container->addSelect('post', 'Pozice', $postPairs);

	$post->setOnChangeCallback(function ($value, $inputName, $formValues) use ($form)
	{
		$form->setDefaults($formValues); // zachovat už vyplněná data
		$this->redrawControl('form');
	});
}, 0);
```

U složitějších formulářů je běžné redrawnout i separátní snippet (`duplicator`, `preview`, …).

### 7.15 Kdy použít `addButton`, `addSubmit`, `addLink`

```php
$form->addButton('delete', 'Odebrat')
	->setHtmlAttribute('data-id', $id); // JS akce (neodesílá form)

$form->addSubmit('save', 'Uložit'); // skutečné odeslání formuláře

$form->addLink('back', 'Zpět')
	->setLink($this->getPresenter()->link('default')); // navigace
```

Praktický vzor: „fake submit“ jako `addLink` + skrytý `addSubmit` pro vlastní UX.

### 7.16 Editační `prepare()` + předvyplnění duplikátoru

```php
public function prepare(): void
{
	$form = $this->getComponent('form');
	$entity = $this->repository->getByID($this->id);

	if ($entity)
	{
		$form->setDefaults($entity->toArray());
		$form->getComponent('rows')->setDefaults($this->mapRows($entity));
	}

	if ($this->getPresenter()->isAjax())
	{
		$this->redrawControl('form');
	}
}
```

---

## 8. Provozní vzory (checklist)

**Architektura**

- Sdílený **základový formulář** (abstraktní třída / společná metoda) pro kontakt, validace napříč kroky, tlačítko zpět s payloadem.
- Velké obrazovky: **vnořené duplikátory**, ve factory často **`use (...)`** pro proměnné z vnějšího scope.
- Ve **`FormComponent`** preferovat **`$this->getForm()`** před `new Form`.
- **`prepare()` je povinné** pro každý nový formulář (i když zatím jen připravuje skeleton).

**UI a podmíněnost**

- **`toggle`** + stabilní **`id`** na obalech; u celku **`setId`** na kontejneru nebo skupině.
- **Telefon**: select předvolby + číslo + **jedna Latte šablona** + **`addConditionOn`** na délku.
- **Rozvržení seznamů**: `setBig`, `setItemsPerRow`, `setItemsColor`, položky jako **`Html`**.

**Data a AJAX**

- **`setAjax()` je opt-in** (default je klasický POST + `redirect()` z `onSuccess`). AJAX volíš jen tam, kde potřebuješ redraw snippetu / setrvání na stránce (duplikátory, dependent selecty, modaly, signály).
- **`prepare()`** podle ID záznamu; **`redrawControl('form')`** i další snippety podle potřeby.
- **`onValidate`** (křížová pravidla, flash) vs **`onSuccess`** (uložení).
- Více **`{control …:js}`**; po update snippetu **naja** `afterUpdate`.
- U callbacků v duplikátoru po změně pole často navázat `setDefaults($formValues)` + redraw.

**Controle a šablony**

- **`setTemplate`** pro required, tooltip, telefon.
- **Závislé selecty**: řetěz rodičů, callback → **`DependentData`**. U **`addDependentMultiSelect`** vždy zajisti prompt (buď `DependentData(prompt: ...)` nebo `->setPrompt(...)` na controlu) — `DependentMultiSelect::setPrompt(string)` je non-nullable a balíček interně volá `setPrompt($data->getPrompt() ?: $this->getPrompt())`. Single `addDependentSelect` má nullable parent `setPrompt`, tam je prompt volitelný.
- **Whisperer** pro rychlé vyhledávání / filtry (statické páry nebo **`setOnSearchChangeCallback`**).
- **Duplikátor**: callback dávej do **3. argumentu `addSubmit()`**, neopakuj `addCreateOnClick`/`addRemoveOnClick` ručně — interně se už volají.
- **Autocomplete**: před přidáním ověř, že má projekt zaregistrovanou JS knihovnu `jquery.autocomplete.min.js` (jinak `ReferenceError: autocomplete is not defined`).
- Uploady: **`isOk()` + `hasFile()`**, zkrácení názvu, save metadata, `move()`, `download/delete` handlery.

**Validace a vzhled**

- **`Form::ValidateRC`**, **`Form::ValidateIC`**.
- **`$form->setButtonClass`** pro jednotná tlačítka.
- **`addHidden` + `setHtmlId`** pro tokeny a JS.

---

## 9. Odkazy

- [modul-is/form (GitHub)](https://github.com/modul-is/form)
- [Nette Forms – dokumentace](https://doc.nette.org/cs/forms)
- [nette-forms SKILL (inspirace struktury)](https://github.com/nette/claude-code/blob/master/plugins/nette/skills/nette-forms/SKILL.md)

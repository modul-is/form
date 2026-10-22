# Nette form with Bootstrap 6 renderer
This library allows you to spend less time writing templates for Nette forms - it contains renderers for form, containers as well as all inputs

> This branch renders **Bootstrap 6**. Migrating from the Bootstrap 5 version is described in [UPGRADE-BS6.md](UPGRADE-BS6.md).

## Getting started
Easiest way to create form is to create component which extends `FormComponent` class

```
class MyForm extends \ModulIS\Form\FormComponent
{
	public function createComponentForm(): Form
	{
		$form = $this->getForm();
		
		...
		
		return $form;
	}
}
```

Then you just add inputs to form, create `MyForm` component and all done!

A form works without `FormComponent` too - `Form` renders itself (`{control form}`, `echo $form`).

### Installation

```
composer require modul-is/form
npm install @modul-is/form bootstrap@6.0.0-alpha.1 nette-forms
```

Register the DI extension (optional - it registers the Latte tags and sets the defaults of the whole application):

```neon
extensions:
	modulisForm: ModulIS\Form\DI\FormExtension

modulisForm:
	icons: bootstrap         # bootstrap (default), fontawesome, or a class implementing IconRenderer
	iconStyle: fa-solid      # Font Awesome style
	currency: CZK
	currencyDecimals: 0
	latte: true              # tags {inputRender}, {inputCore}, {labelCore}
	clientValidation: false  # rules checked by the server only
```

and the client side (see [Client side](#client-side)):

```js
import { initForms } from '@modul-is/form';
import '@modul-is/form/css';

initForms(naja);
```

## Inputs
Form supports all of the default Nette inputs and adds new ones

+ `addLink()` - Button with link (eg. reset, go back buttons)
+ `addWhisperer()` - select with a search ([combobox](https://getbootstrap.com/docs/6.0/forms/combobox/)) - items are filtered on the client, or loaded from the server by `setOnSearchChangeCallback()`; `setOnSelectCallback()` tells the server about every selection
+ `addMultiWhisperer()` - same as whisperer, more options can be selected
+ `addAutocomplete()` - text input with suggestions (native `<datalist>`), static items or loaded from the server by `setOnSearchChangeCallback()`
+ `addDuplicator()` - container which can be duplicated many times
+ `addDependentSelect()` - select box that can change options via ajax based on change of another input(s) 
+ `addDependentMultiSelect()` - same as DependentSelect, but more options can be selected 
+ `addDate()` - date input, can limit min and max date; `setPicker()` uses the Bootstrap [datepicker](https://getbootstrap.com/docs/6.0/forms/datepicker/) instead of the browser one (limits are taken from `Min` / `Max` rules)
+ `addSlider()` - range slider, single value or an interval
+ `addMonth()` - month as a `Y-m` string
+ `addColor()` - color in `#rrggbb` format
+ `addOtp()` - one-time code rendered as separate slots ([OTP input](https://getbootstrap.com/docs/6.0/forms/otp-input/)) - `setType()` (`numeric`, `alphanumeric`, `alpha`), `setMask()`, `setGroups([3, 3])`
+ `addChips()` - list of free text values entered as [chips](https://getbootstrap.com/docs/6.0/forms/chips/), value is a list of strings - `setSeparator()`, `setMaxChips()`, `setAllowDuplicates()`, `setPlaceholder()`

## Custom settings

### Form

+ `setTitle()` - title in the `card-header` of the first card (unless its group has a title of its own)
+ `setIcon()` - icon next to the form title
+ `setColor()` - theme color of the card headers of groups without a color of their own
+ `setAjax()` - form is submitted via ajax
+ `setRenderType()` - render type for all inputs of the form, see [Render types](#render-types)
+ `setRenderDefault()`, `setRenderFloating()`, `setRenderInline()` - shortcuts for `setRenderType()`
+ `setButtonClass()` - default CSS class for all form buttons (e.g. `rounded rounded-4`); can be overridden by `setClass()` on individual buttons
+ `setClientValidation()` - rules are checked also on the client before the form is sent; off by default - the form is
  validated by the server only and all errors come at once (default for the application: `Form::setDefaultClientValidation()`
  or `clientValidation` of the DI extension)
+ `setDefaultInputWrapClass()` - class of the column around every input (default `col-12`)

### FormComponent

+ `setRenderManually()` (protected) - set manual render, template with same name as the component is used (eg. file `MyForm.php` -> `myForm.latte`), see [Manual rendering](#manual-rendering)

### Groups
Form is rendered as Bootstrap 6 [cards](https://getbootstrap.com/docs/6.0/components/card/) - each card represents one group

+ `setIcon()` - icon in the card header
+ `setColor()` - theme color of the card header
+ `setClass()` - class of the column around the card (default `col-12`)

Inputs are rendered in `card-body` (a `row g-3` grid), submitters, links and buttons in `card-footer` of the last card

### Container
Container works as standard Nette Container and has these new features

+ `setId()` - add html id to outer div of container
+ `showCard()` - show container as Bootstrap [card](https://getbootstrap.com/docs/6.0/components/card/)
+ `setTitle()` - show title of container (only works when container is rendered as card)
+ `setColor()` - theme color of the card header (only works when container is rendered as card)
+ `setWrapClass()` - class of the column around the container (default `col-12`)

### Currency input

`addCurrency()` - numeric input that visually formats values with thousands separators (e.g. `1 000 000`) but returns a plain integer on submit (or float when decimals are set). Currency label is appended to the input.

Default currency can be set globally for the whole project (e.g. in bootstrap or DI extension):

```php
\ModulIS\Form\Control\CurrencyInput::setDefaultCurrency('CZK');
```

Per-input currency can be set via:

```php
$form->addCurrency('price', 'Price', 'EUR');
// or
$form->addCurrency('price', 'Price')->setCurrency('EUR');
```

Decimal places (e.g. cents) can be set globally or per input. The value is then rendered with decimal comma
(`1 234,50`), the user may type both decimal comma and decimal point and the value is returned as float
rounded to the set number of decimals:

```php
\ModulIS\Form\Control\CurrencyInput::setDefaultDecimals(2);
// or
$form->addCurrency('price', 'Price', 'EUR')->setDecimals(2);
```

### Inputs
Some inputs provide new features

+ `setWidth()` - width in grid columns: `setWidth(6)` is half width from the `md` breakpoint up, `setWidth(['md' => 6, 'xl' => 4])` per breakpoint (All non-button inputs)
+ `setIcon()` - icon inside the input ([form adorn](https://getbootstrap.com/docs/6.0/forms/form-adorn/)) or in the button (Text inputs, Selects, Buttons, Links)
+ `setColor()` - theme color - `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `accent`, `inverse` (Buttons, Links, Lists)
+ `setVariant()` - button style - `solid` (default), `outline`, `subtle`, `text`, `link` (Buttons, Links)
+ `setDescription()` - help text under the input (`form-text`) (Text inputs, Checkbox, Lists, Select boxes)
+ `setTooltip()` - help icon with a tooltip next to the caption (Text inputs, Checkbox, Lists, Select boxes)
+ `setTemplate()` - add custom latte template instead of basic render (All inputs)
+ `setPrepend()` - adds prepend part to [input group](https://getbootstrap.com/docs/6.0/forms/input-group/) (Text inputs, TextArea, Select boxes, Slider, Upload)
+ `setAppend()` - adds append part to [input group](https://getbootstrap.com/docs/6.0/forms/input-group/) (Text inputs, TextArea, Select boxes, Slider, Upload)
+ `setRenderType()` - render type of a single input, overwrites the setting from Form, see [Render types](#render-types) (All non-button inputs except Checkbox)
+ `setRenderDefault()`, `setRenderFloating()`, `setRenderInline()` - shortcuts for `setRenderType()`; use `setRenderDefault()` to opt a single input out of a form-wide floating/inline setting
+ `setAutoRenderSkip()` - skips rendering of input, eg. if input is rendered as part of another input with custom template (All inputs)
+ `setQuickCopy()` - add button to copy input value to clipboard (Text inputs, TextArea)
+ `setWrapClass()` - replaces the class of the column around the input (default `col-12`, see `Form::setDefaultInputWrapClass()`)
+ `setLabelWrapClass()` - class of the label; in `Inline` the class of the label column (default `sm:col-4`)
+ `setInputWrapClass()` - class of the input; in `Inline` the class of the control column (default `sm:col-8`)
+ `setSwitch()` - checkbox rendered as a [switch](https://getbootstrap.com/docs/6.0/forms/switch/) (Checkbox)
+ `setIndeterminate()` - mixed state of a checkbox, visual only (Checkbox)
+ `setSize()` - `sm` or `lg`, for the whole form `Form::setSize()` (All inputs and buttons)
+ `setPlaintext()` - read-only value shown as plain text (Text inputs)
+ `setSuggestions()` - values offered while typing, the user may type anything else ([datalist](https://getbootstrap.com/docs/6.0/forms/form-control/#datalists)) (Text input)
+ `setStrengthMeter()` - [password strength](https://getbootstrap.com/docs/6.0/forms/password-strength/) meter under the input - informative only, add rules for real requirements (Password)
+ `setPrefix()`, `setSuffix()` - text inside the input before / after the value ([form adorn](https://getbootstrap.com/docs/6.0/forms/form-adorn/)), e.g. a unit (Text inputs)
+ `setSearch()` - select rendered as a [combobox](https://getbootstrap.com/docs/6.0/forms/combobox/) with a search, `setNoResultMessage()` (Select boxes)
+ `setImageArray()` - images of the items (item key => URL), rendered as a combobox (Select box)

### Icons

Icons are rendered by `\ModulIS\Form\Icon\IconRenderer` - the default renders [Bootstrap Icons](https://icons.getbootstrap.com/)
(`<i class="bi bi-…">`), the font itself has to be loaded by the application. Another icon set can be used for the whole
application or per form:

```php
\ModulIS\Form\Renderer\Bootstrap6Renderer::setDefaultIconRenderer(\ModulIS\Form\Icon\ClassIconRenderer::fontAwesome('fa-light'));

$form->setRenderer(new \ModulIS\Form\Renderer\Bootstrap6Renderer(new MyIconRenderer));
```

The library itself uses semantic names `help`, `save`, `add`, `remove`, `copy` and `info` - icon renderers map them to their icon set.

### Rendering

All markup is created by `\ModulIS\Form\Renderer\Bootstrap6Renderer` (set by `Form` as its Nette form renderer, so
`{control form}` and `echo $form` work as well). Controls only hold their settings - to change the markup of the whole
application extend the renderer and set it with `$form->setRenderer()`.

### Render types

Every input is rendered in one of the modes of `\ModulIS\Form\Enum\RenderType`. The form sets the default,
a single input can override it.

+ `RenderType::Default` - label above the input (default)
+ `RenderType::Floating` - [floating label](https://getbootstrap.com/docs/6.0/forms/floating-labels/) (file inputs and multiselects fall back to `Default`, Bootstrap does not support them)
+ `RenderType::Inline` - label and input side by side ([horizontal form](https://getbootstrap.com/docs/6.0/forms/layout/#horizontal-form))

```php
$form->setRenderFloating();

$form->addText('name', 'Name')
	->setRenderDefault();
```

`CheckboxList` and `RadioList` use `\ModulIS\Form\Enum\RenderListType` instead:

+ `Default` - items under each other
+ `Compact` - items next to each other
+ `Big` - items as cards (`form-field-card`) with icons (`setIconArray()`) and descriptions (`setTooltips()`)
+ `Inline` - label and items side by side
+ `Floating` - same as `Default` (Bootstrap has no floating lists)

Independently of the render type the items can be rendered as toggle buttons (`setToggleButton()`, `setButtonColor()`)
or in several columns (`setItemsPerRow()`).

### Anatomy of an input

```
Default                                          Inline
<div class="WRAP">          setWrapClass()       <div class="WRAP">
  <div class="form-field" data-mis-field>          <div class="row">                         setRowClass()
    <label class="form-label">Caption *</label>      <label class="col-form-label sm:col-4"> setLabelWrapClass()
    <div class="input-group">                        <div class="form-field sm:col-8">       setInputWrapClass()
      <span class="input-group-text">PRE</span>        … input group, feedback, form-text
      <input class="form-control">                   </div>
      <span class="input-group-text">APP</span>    </div>
    </div>                                         </div>
    <div class="invalid-feedback">…</div>
    <div class="form-text">…</div>  setDescription()
  </div>
</div>

Floating                                         With setIcon()
<div class="WRAP">                               <label class="form-control form-adorn">
  <div class="form-field" data-mis-field>          <span class="form-adorn-icon">…</span>
    <div class="form-floating">                    <input class="form-ghost">
      <label>Caption</label>                     </label>
      <input class="form-control" placeholder="Caption">
    </div>
    …
```

`data-mis-field` marks the element validation feedback belongs to - client side validation (`assets/js`) shows errors there
the same way as the server does.

### Client side

The package contains its client side in `assets/` (ES modules, published as npm package `@modul-is/form`) - no jQuery,
only Bootstrap 6 and `nette-forms`:

```js
import naja from 'naja';
import netteForms from 'nette-forms';
import { initForms } from '@modul-is/form';
import '@modul-is/form/css';

netteForms.initOnLoad();
naja.formsHandler.netteForms = netteForms;

initForms(naja);   // before naja.initialize()
```

`initForms()` mounts everything the forms need - also in snippets redrawn by naja:

+ validation - forms validated by the server only are sent without client checks; with `setClientValidation()` errors
  are shown next to the inputs (instead of the alert of nette-forms), rules of `FormValidator` included
+ comboboxes (whisperer, selects with `setSearch()` or `setImageArray()`), dependent selects, autocomplete
+ AJAX callbacks of inputs and buttons (`setOnChangeCallback()`, `setOnFocusOutCallback()`)
+ currency input, quick copy, slider with two handles, chips, OTP, password strength, datepicker, tooltips

AJAX requests go through naja when it is passed to `initForms()` (snippets redrawn by the callbacks are updated),
otherwise through `fetch`. Peer dependencies are `bootstrap` and `nette-forms`, `naja` is optional. Bootstrap brings its own peer
dependencies `@floating-ui/dom` and `vanilla-calendar-pro` (npm and pnpm install them automatically, Yarn does not);
the datepicker with Vanilla Calendar Pro is loaded only when a form uses it.

Controls enhanced on the client (combobox, chips, datepicker, slider with two handles) keep the native control as the
source of truth - it is only hidden, carries the name and Nette rules and the form works with it as with any other input.

### Manual rendering

With `setRenderManually()` the form is rendered from your own template (`MyForm.php` -> `myForm.latte`), which contains
the `{form}` tag and the `form` snippet itself. The Latte tags are registered by the DI extension, or by hand:

```neon
latte:
	extensions:
		- ModulIS\Extension\FormExtension
```

It adds three tags:

| Tag | Renders |
| --- | --- |
| `{inputRender name}` | the whole input - wrapper, label, input group and validation, exactly as `$form->getComponent('name')->render()` |
| `{inputCore name[:part]}` | only the input itself (`getCoreControl()`), without the label and wrapper |
| `{labelCore name[:part]}` | only the label (`getCoreLabel()`) |

Use `{inputRender}` when you only want to decide *where* an input goes and keep its normal look:

```latte
<div class="row">
	<div class="col-6">{inputRender first_name}</div>
	<div class="col-6">{inputRender last_name}</div>
</div>
```

Use `{inputCore}` + `{labelCore}` when you need to build the markup around the input yourself:

```latte
<div class="my-own-wrapper">
	{labelCore property_type /}
	{inputCore property_type}
</div>
```

Client side validation shows errors in the element with `data-mis-field` - when you build the markup yourself, put it on
the element around the label and the control (otherwise the errors of that input are shown by the alert of nette-forms):

```latte
<div class="form-field" data-mis-field>
	{labelCore note class => 'form-label' /}
	{inputCore note}
</div>
```

`{inputCore list:key}` renders one item of a radio / checkbox list including its label.
See `demo/app/Form/manualForm.latte`.

`{inputRender}` honours everything set on the input - `setRenderType()`, `setTemplate()`,
`setAutoRenderSkip()` (renders nothing) - and works for `addDuplicator()` containers as well.
It takes no `:part`; use `{inputCore}` for that.

### Slider

`addSlider()` with a single value renders a native [range](https://getbootstrap.com/docs/6.0/forms/range/) - the scale is a
`<datalist>` with tick marks (every step, or ten parts when there are more than 20 steps). With `setItems()` the input holds
the index of the item, `getValue()` returns the item. Two handles (`setRange()`) are two range inputs
over each other, the value is carried by a hidden input.
The value domain is either a continuous range or an explicit list of values.

```php
// continuous range - min, max and step can be passed straight to addSlider()
$form->addSlider('age', 'Age', 0, 100, 5);

// ...or set later
$form->addSlider('age', 'Age')
	->setMinMax(0, 100, 5);

// explicit list of values
$form->addSlider('year', 'Year')
	->setItems([2020, 2021, 2022]);
```

`setRange()` turns on a second handle - the value then becomes an array `[from, to]`:

```php
$form->addSlider('span', 'Span', 0, 100, 10)
	->setRange()
	->setValue([20, 60]);

$slider->getValue();     // [20, 60]
```

Without `setRange()` the value is a single `int` or `float`:

```php
$form->addSlider('age', 'Age', 0, 100, 5)
	->setValue(35);

$slider->getValue();     // 35
```

Appearance of the slider itself:

+ `showScale()` - ticks under the track (default `true`)
+ `showLabels()` - value labels under the ticks (default `true`)
+ `showTooltip()` - bubble with the current value above the handle (default `true`)

`setMinMax()` and `setItems()` are mutually exclusive - the later call wins. Calling neither
throws when the input is rendered.

### Duplicator

Duplicator is rendered as a card with one bordered item per container, the buttons of `addSubmit()` on the duplicator
add new items in the card footer. Every `addSubmit()` inside the `DuplicatorContainer` is a remove button and is rendered
in the head of the item, regardless of its name. Adding and removing goes through the server (AJAX with `setAjax()`).

+ `setTitle()`, `setColor()` - title and theme color of the card header
+ `setDuplicatorContainerClass()`, `setDuplicatorBodyClass()`, `setDuplicatorFooterClass()`, `setDuplicatorItemClass()`,
  `setDuplicatorButtonWrapClass()` - classes of the parts
+ `setTitle()` on a `DuplicatorContainer` - title in the head of the item

```
$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
{
	$container->addText('text', 'Text input');

	$container->addSubmit('del', 'Smazat');
}, 1);

$duplicator->addSubmit('add', 'Přidat');
```

## Upgrading from v1 to v2

See [UPGRADE.md](UPGRADE.md) - it lists every change needed to migrate a form, including the swapped meaning of `setRenderInline()`.

## Development

The repository contains a demo application (`demo/`) with a form showing every input and a form rendered manually
by Latte tags, configured by the DI extension.

```
composer install
npm install
npm run demo          # builds demo assets to demo/www/dist and rebuilds them on change
```

Open the demo through a web server pointing to `demo/www` (e.g. `http://localhost/modulis-form/demo/www/`
under XAMPP) or run `php -S localhost:8000 -t demo/www`.

Client side of the library lives in `assets/` (ES modules, published to npm as `@modul-is/form`),
the demo page imports it straight from there.

Checks:

```
vendor/bin/tester -C tests/
vendor/bin/phpstan analyse
vendor/modul-is/coding-standard/ecs check src --preset php84
```

Render tests compare HTML with fixtures - after an intended change of the markup regenerate them with
`BLESS_FIXTURES=1 vendor/bin/tester -C tests/` and review the diff before committing.

Publishing the client side to npm: `package.json` is marked `private` until the first release - set the version, remove
`private` and run `npm publish` (scope `@modul-is` has to exist on npm; `publishConfig.access` is `public`).

# Nette form with custom BS5 renderer
This library allows you to spend less time writing templates for Nette forms - it contains renderers for form, containers as well as all inputs

> **v2.0** reworks rendering. If you are coming from v1, see [Upgrading from v1 to v2.0](#upgrading-from-v1-to-v20).

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

## Inputs
Form supports all of the default Nette inputs and adds new ones

+ `addLink()` - Button with link (eg. reset, go back buttons)
+ `addWhisperer()` - select box with whisperer which filters options
+ `addMultiWhisperer()` - same as whisperer, more options can be selected
+ `addDuplicator()` - container which can be duplicated many times
+ `addDependentSelect()` - select box that can change options via ajax based on change of another input(s) 
+ `addDependentMultiSelect()` - same as DependentSelect, but more options can be selected 
+ `addDate()` - date input, can limit min and max date
+ `addSlider()` - range slider, single value or an interval

## Custom settings

### Form

+ `setTitle()` - add `card-header` div with title
+ `setColor()` - set color of form
+ `setAjax()` - form is submitted via ajax
+ `setRenderType()` - render type for all inputs of the form, see [Render types](#render-types)
+ `setRenderDefault()`, `setRenderFloating()`, `setRenderInline()` - shortcuts for `setRenderType()`
+ `setButtonClass()` - default CSS class for all form buttons (e.g. `rounded rounded-4`); can be overridden by `setClass()` on individual buttons
+ `setRenderManually()` - set manual render, template with same name as form is used (eg. file `MyForm.php` -> `myForm.latte`), see `FormComponent`

### Groups
Form is rendered in BS5 [card](https://getbootstrap.com/docs/5.0/components/card/) - each card represents one group

Inputs are rendered in `card-body` div

Submitters, links and buttons in `card-footer`

### Container
Container works as standard Nette Container and has these new features

+ `setId()` - add html id to outer div of container
+ `showCard()` - show container as BS5 [card](https://getbootstrap.com/docs/5.0/components/card/)
+ `setTitle()` - show title of container (only works when container is rendered as card)
+ `setColor()` - set color of container (only works when container is rendered as card)

### Currency input

`addCurrency()` - numeric input that visually formats values with thousands separators (e.g. `1 000 000`) but returns a plain integer on submit. Currency label is appended to the input.

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

### Inputs
Some inputs provide new features

+ `setIcon()` - add icon to input or button (Buttons, Links, Text inputs)
+ `setColor()` - add color to input or button (Buttons, Links, Checkbox, Lists)
+ `setTemplate()` - add custom latte template instead of basic render (All inputs)
+ `setPrepend()` - adds prepend part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setAppend()` - adds append part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setRenderType()` - render type of a single input, overwrites the setting from Form, see [Render types](#render-types) (All non-button inputs)
+ `setRenderDefault()`, `setRenderFloating()`, `setRenderInline()` - shortcuts for `setRenderType()`; use `setRenderDefault()` to opt a single input out of a form-wide floating/inline setting
+ `setAutoRenderSkip()` - skips rendering of input, eg. if input is rendered as part of another input with custom template (All inputs)
+ `setTooltip()` - add icon with tooltip to input (Text inputs, Checkbox, Lists, Select boxes)
+ `setQuickCopy()` - add button to copy input value to clipboard (Text inputs, TextArea)
+ `setWrapClass()` - set class to outer div around label and input - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Select boxes)
+ `setLabelWrapClass()` - set class to wrap div around label - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Select boxes)
+ `setInputWrapClass()` - set class to wrap div around input - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Select boxes)

### Render types

Every input is rendered in one of the modes of `\ModulIS\Form\Enum\RenderType`. The form sets the default,
a single input can override it.

+ `RenderType::Default` - label above the input (default)
+ `RenderType::Floating` - [floating label](https://getbootstrap.com/docs/5.0/forms/floating-labels/)
+ `RenderType::Inline` - label and input side by side

```php
$form->setRenderFloating();

$form->addText('name', 'Name')
	->setRenderDefault();
```

`CheckboxList` and `RadioList` use `\ModulIS\Form\Enum\RenderListType` instead, which adds
`Big` (tiles) and `Compact` on top of `Default`, `Floating` and `Inline`.

### Anatomy of an input

Every input is wrapped in a few nested elements and each of them has its own setter.
The trees below are the real output for `addText()` with all wrap setters used at once,
so you can see which call lands where.

**`RenderType::Default`** - label above the input

```
<div class="WRAP mis-field" id="WRAPID">          setWrapClass() / setWrapId()
    <label class="LABELWRAP">Caption</label>      setLabelWrapClass()   <- caption + setTooltip()
    <div class="input-group">
        <span class="input-group-text">PRE</span>  setPrepend()
        <input class="mis-input form-control INPUTWRAP">
        |                                         setInputWrapClass() lands on the input itself
        |                                         setClass() replaces the whole class attribute
        <span class="input-group-text">APP</span>  setAppend()
        <span class="quick-copy-wrap">…</span>     setQuickCopy()
    </div>
</div>
```

**`RenderType::Floating`** - Bootstrap floating label

```
<div class="WRAP" id="WRAPID">                    setWrapClass() / setWrapId()
    <div class="input-group">
        <span class="input-group-text">PRE</span>  setPrepend()
        <div class="form-floating">
            <input class="form-control" placeholder="Caption">
            <label>Caption</label>                <- caption + setTooltip()
        </div>
        <span class="input-group-text">APP</span>  setAppend()
        <span class="quick-copy-wrap">…</span>     setQuickCopy()
    </div>
</div>
```

**`RenderType::Inline`** - label and input side by side

```
<div class="WRAP mis-field-inline" id="WRAPID">   setWrapClass() / setWrapId()
    <div class="mis-field-inline-label">          fixed class
        <label>Caption</label>                    <- caption + setTooltip()
    </div>
    <div class="mis-field-inline-control">        fixed class
        <div class="input-group">
            <span class="input-group-text">PRE</span>
            <input class="form-control">
            <span class="input-group-text">APP</span>
            <span class="quick-copy-wrap">…</span>
        </div>
    </div>
</div>
```

`CheckboxList` and `RadioList` use `RenderListType` and have their own structure:

```
Default                                  Compact
<div class="WRAP mis-checklist">         <div class="WRAP mis-compact">
    <label>Caption</label>                   <label class="mis-compact-label LABELWRAP">
    <div class="mis-checklist-items">        <div class="mis-compact-items">
        <label class="checkbox">…</label>        <div class="mis-compact-field INPUTWRAP">
    </div>                                           <label>…</label>
</div>                                           </div>
                                             </div>
                                         </div>

Big (tiles)                              Inline
<div class="mis-tiles INPUTWRAP">        <div class="WRAP">
    <div class="mis-tiles-head">             <div class="ROW">            setRowClass()
        <div class="mis-tiles-title">            <div class="… LABELWRAP">
        <div class="mis-tiles-sub">              <div class="… INPUTWRAP">
    </div>                                   </div>
    <div class="mis-tiles-list">         </div>
        <label class="mis-tile">
            <span class="mis-tile-ico">  setIconArray()
            <span class="mis-tile-lbl">
            <span class="mis-tile-desc"> setTooltips()
            <span class="mis-tile-chk">
    </div>
</div>
```

#### Which setter works where

Not every wrap setter is read by every render type - the table says where a call has an effect:

| Setter | Default | Floating | Inline | List: Default | List: Compact | List: Big | List: Inline |
| --- | :-: | :-: | :-: | :-: | :-: | :-: | :-: |
| `setWrapClass()` | yes | yes | yes | yes | yes | - | yes |
| `setWrapId()` | yes | yes | yes | yes | yes | - | yes |
| `setLabelWrapClass()` | yes | - | - | - | yes | - | yes |
| `setInputWrapClass()` | yes* | - | - | - | yes | yes | yes |
| `setRowClass()` | - | - | - | - | - | - | yes |

\* in `Default` the class is appended to the `<input>` element, not to a wrapper div.

Setters that behave the same in all render types:

| Setter | Affects |
| --- | --- |
| `setClass()` | `class` of the `<input>` (replaces it, unlike `setInputWrapClass()`) |
| `setPrepend()` / `setAppend()` | `.input-group-text` before / after the input |
| `setIcon()` | icon rendered as a prepend |
| `setTooltip()` | question-mark icon next to the caption |
| `setQuickCopy()` | copy-to-clipboard button at the end of the input group |
| `setColor()` | colour class of the input / button |
| `setOption('id')` | `id` of the outermost element (same place as `setWrapId()`) |
| `setTemplate()` | replaces the whole render with your own Latte file |
| `setAutoRenderSkip()` | renders nothing |

The wrapper class defaults to `mb-2 col-12` and can be changed for the whole form with
`$form->setDefaultInputWrapClass()`.

### Manual rendering

With `setRenderManually()` the form is rendered from your own template. Register the Latte extension
first:

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

`{inputRender}` honours everything set on the input - `setRenderType()`, `setTemplate()`,
`setAutoRenderSkip()` (renders nothing) - and works for `addDuplicator()` containers as well.
It takes no `:part`; use `{inputCore}` for that.

### Slider

`addSlider()` renders a slider built on [rSlider.js](https://github.com/slawomir-zaziablo/range-slider).
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

### Duplicator example
```
$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
{
	$container->addText('text', 'Text input');

	$container->addSubmit('del', 'Smazat');
}, 1);

$duplicator->addSubmit('add', 'Přidat');
```

## Upgrading from v1 to v2.0

### Render API

`setFloatingLabel()` and the boolean `setRenderInline()` are gone. Rendering is now driven by
`RenderType` / `RenderListType` - see [Render types](#render-types).

| v1 | v2.0 |
| --- | --- |
| `$form->setFloatingLabel()` | `$form->setRenderFloating()` |
| `$form->setRenderInline()` | unchanged (no longer takes a bool) |
| `$input->setFloatingLabel()` | `$input->setRenderFloating()` |
| `$input->setFloatingLabel(false)` | `$input->setRenderDefault()` |
| `$input->setRenderInline(false)` | `$input->setRenderDefault()` |
| `$form->addBox()` | removed, use `addGroup()` |

# Upgrade from v1 to v2 – migration steps

Steps to migrate forms of an application from `modul-is/form` v1 to v2. Every step says what to
**find** (a regular expression for grep over the application code, usually `app/` and templates),
what to **change**, and when to **report** a finding to the developer instead of changing it.

Rules:

- Go through all steps in order; a step with no match needs no change.
- Change only code matched by a step. Don't refactor forms otherwise.
- Collect every "report" item and present them together at the end.
- PHP (`^8.4`) and Nette requirements are the same as in v1 – no composer changes besides the version.

---

## Step 1 – render type calls

v2 setters take no argument (PHP silently ignores it, so `setRenderFloating(false)` would turn
floating **on**) and **the layouts swapped names**:

| Layout | v1 | v2 |
| --- | --- | --- |
| label left, input right | default (no call) | `setRenderInline()` |
| label above input | `setRenderInline()` | default (`setRenderDefault()`) |
| floating label | `setRenderFloating()` | `setRenderFloating()` |

**Find:** `setRenderInline\(|setRenderFloating\(|getRenderInline\(|getRenderFloating\(|setFloatingLabel\(`

**Change** (on a form `$form->…`, on an input `$input->…`):

| v1 | v2 |
| --- | --- |
| `$form->setRenderInline()`, `$form->setRenderInline(true)` | remove the call |
| `$form->setRenderInline(false)`, `$form->setRenderFloating(false)` | `$form->setRenderInline()` |
| `$form->setRenderFloating(true)`, `setFloatingLabel()` | `$form->setRenderFloating()` |
| `$input->setRenderInline()`, `$input->setRenderInline(true)` | `$input->setRenderDefault()` |
| `$input->setRenderInline(false)`, `$input->setRenderFloating(false)` | `$input->setRenderInline()` |
| `$input->setRenderFloating(true)`, `setFloatingLabel()` | `$input->setRenderFloating()` |
| `$form->getRenderInline()` | `$form->getRenderType() === \ModulIS\Form\Enum\RenderType::Inline` |
| `$form->getRenderFloating()` | `$form->getRenderType() === \ModulIS\Form\Enum\RenderType::Floating` |
| `$input->getRenderInline()` / `getRenderFloating()` | `$input->getRenderType()` (`null` = inherited from the form) |

A variable argument (`setRenderInline($inline)`) → replace with
`$inline ? $x->setRenderDefault() : $x->setRenderInline()` for v1 `setRenderInline($inline)`, and
`$floating ? $x->setRenderFloating() : $x->setRenderInline()` for v1 `setRenderFloating($floating)`.

## Step 2 – keep the v1 layout of forms without a render call

v1 default (label left, input right) is v2 `Inline`; v2 default renders the label above the input.

**Find:** classes extending `\ModulIS\Form\FormComponent` (and other places creating
`\ModulIS\Form\Form`) whose form has no `setRender…()` call after step 1.

**Change:** add `$form->setRenderInline();` right after `$form = $this->getForm();` so the form keeps
the v1 look. **Report** the list of affected forms – the developer may prefer the new default layout
and remove the calls again.

## Step 3 – wrapper classes

In v1 `setLabelWrapClass()` / `setInputWrapClass()` replaced the `col-sm-4` / `col-sm-8` columns. In v2
`Inline` renders fixed `mis-field-inline-label` / `mis-field-inline-control` columns and ignores both
setters; in `Default` `setInputWrapClass()` is appended to the `<input>` element itself.

**Find:** `setLabelWrapClass\(|setInputWrapClass\(`

**Change:** nothing automatically. **Report** every match on an input rendered as `Inline` or
`Default` (column widths have to be solved in CSS or with another render type).
The single checkbox is not affected (it has its own implementation of both setters). Radio and checkbox
lists honour them in `Compact` and `Inline` (`setInputWrapClass()` also in `Big`), not in the default
list render – **report** such lists too.

The default wrapper class changed from `mb-3 col-12` to `mb-2 col-12`. **Find:**
`setDefaultInputWrapClass\(` – no change needed where it is set. **Report** that other forms get
smaller spacing (`$form->setDefaultInputWrapClass('mb-3 col-12')` restores it).

## Step 4 – checkbox and radio lists

The v1 list layout (label column + rows of `form-check`) is v2 `RenderListType::Inline`; the v2
default is a new compact vertical list. Several list setters only work in `Inline`.

**Find:** `->setBig\(`

**Change:** `->setBig()` / `->setBig(true)` → `->setRenderBig()`; `->setBig(false)` → remove.

**Find:** `setToggleButton\(|setButtonColor\(|setItemsPerRow\(|setItemClass\(|setWrapAttributes\(|setItemsColor\(|setTooltips\(`
on `addRadioList()` / `addCheckboxList()` / `RadioList` / `CheckboxList`.

**Change:** if the list (or its form) is not rendered as `Inline` after steps 1–2, add
`->setRenderInline()` to the list. `setTooltips()` works also with `setRenderBig()` – leave those.

## Step 5 – single checkbox

The checkbox (`addCheckbox()`) has a new markup without render types, toggle-button look or colour.

**Find:** on `addCheckbox()` / `Checkbox`: `setToggleButton\(|setButtonColor\(|setColor\(|setRenderInline\(|setRenderFloating\(|setRenderDefault\(|setRenderType\(`

**Change:** remove the call. **Report** checkboxes that used `setToggleButton()` or `setColor()` –
the look has no replacement (a radio / checkbox list with `setToggleButton()` + `setRenderInline()`
is the nearest alternative).

Other checkbox setters (`setSwitch()`, `setWrapClass()`, `setLabelWrapClass()`,
`setInputWrapClass()`, `setCheckboxWrapClass()`) stay.

## Step 6 – `setColor()` on inputs

`setColor()` exists only on buttons (`addSubmit`, `addButton`), links (`addLink`) and lists
(`addRadioList`, `addCheckboxList`) – on other inputs it had no effect and was removed.

**Find:** `->setColor\(` chained on `addText`, `addTextArea`, `addPassword`, `addEmail`, `addInteger`,
`addFloat`, `addSelect`, `addMultiSelect`, `addDate`, `addDateTime`, `addTime`, `addCurrency`, `addUpload`,
`addMultiUpload`, `addAutocomplete`, `addWhisperer`, `addMultiWhisperer`, `addDependentSelect`,
`addDependentMultiSelect`, `addSlider`, `addCheckbox`.

**Change:** remove the call. `setColor()` on `Form`, `Container` and `ControlGroup` stays.

## Step 7 – HTML in captions, labels and messages

v2 escapes captions, list item labels, form / container / group / duplicator titles, tooltips of
the `Big` list render, validation messages and form errors (v1 inserted them as raw HTML).

**Find:** string literals or translations containing HTML (`<` followed by a letter or `/`, `&nbsp;`,
`&amp;`) passed as caption / label / item / title / message: arguments of `add*()`, `setTitle()`,
`addGroup()`, `addError()`, `addRule()`, `setRequired()`, `setItems()`, item arrays of selects and lists,
`setTooltip()`, `setTooltips()`, `setValidationSuccessMessage()`.

**Change:** wrap the value in `\Nette\Utils\Html::fromHtml(...)`:

```php
// v1
$form->addCheckbox('terms', 'I agree with <a href="/terms">terms</a>');

// v2
$form->addCheckbox('terms', \Nette\Utils\Html::fromHtml('I agree with <a href="/terms">terms</a>'));
```

For item arrays wrap only the items that contain HTML. When the HTML comes from a translation key,
**report** it (the translation may need to return `Html`).

**Find:** `htmlspecialchars\(|escapeHtml\(|\|escape` applied to a caption, label, item or message.

**Change:** remove the manual escaping – it would be escaped twice now.

## Step 8 – submit buttons

- **Client-side validation now runs.** v1 put `formnovalidate` on every submit button, so the browser
  never validated. v2 adds it only to buttons with a validation scope.

  **Find:** `addSubmit\(` whose button should submit without validating (cancel, back, delete,
  "save draft", …), typically together with `setValidationScope(null)` or no scope at all.

  **Change:** add `->setValidationScope([])` to such buttons. **Report** buttons where it is unclear.

- **The third argument of `addSubmit()` is used.** v1 ignored `$onSubmit`; v2 registers it as `onClick`.

  **Find:** `addSubmit\([^)]*,[^)]*,` (three arguments).

  **Change:** if the same handler is also attached through `->onClick[]` or `$form->onSuccess[]`,
  remove the duplicate so it doesn't run twice.

- **HTML names changed.** A button named `submit` renders as `name="_submit"`, a submit in a container
  as `name="container[save]"` (v1: `save`).

  **Find** in JS, CSS, Latte and tests: `\[name=["']?submit|name="submit"|button\[name=` and selectors
  targeting submit buttons of containers by their short name.

  **Change:** update the selectors to the full HTML name.

- `Container::addSubmit()` now renders with the `save` icon and `success` colour (like `Form`).
  **Report** container submits whose look should stay plain.

## Step 9 – values and inputs

- **Links are not part of form values.** **Find:** reading the name of an `addLink()` from form
  values (`$values->back`, `$values['back']`, `unset($values['back'])`, `array_diff_key`…). **Change:**
  remove the read / unset.
- **Dependent selects.** **Find:** `addDependentSelect\(|addDependentMultiSelect\(` with `null` as the
  parents argument. **Change:** `null` → `[]`.
- **Multi whisperer.** **Find:** `addMultiWhisperer\(` with `null` items. **Change:** `null` → `[]`.
- **Time with seconds.** `addTime($n, $l, true)` returns real seconds (`H:i:s`), v1 always returned
  `:00`. **Find:** `addTime\([^)]*true`. **Report** code comparing or storing the value with `:00`.
- **Date format rule.** `addDate()` / `addDateTime()` no longer add their own rule; invalid input is
  `null`. **Find:** code checking the error message `Vložte datum ve formátu`. **Change:** remove it.
- **Form errors.** `Form::getFormErrors()` returns `getOwnErrors()` (translated). **Find:**
  `getFormErrors\(`. **Report** code that translates the returned messages again.

## Step 10 – AJAX callbacks

- **Find:** `setOnChangeCallback\(|setOnFocusOutCallback\(` whose callback reads the form values
  argument and relies on `'0'` / zero being absent. v2 keeps `'0'` (only empty values are dropped).
  **Report** such callbacks.
- **Find:** `getOnChangeCallback\(|getOnFocusOutCallback\(` compared to the original callable
  (`===`). v2 returns a `\Closure`. **Change:** compare differently or remove the comparison.
- **Find:** assignments to `->onSelectCallback` / `->onSearchChangeCallback` on an autocomplete.
  **Change:** use `setOnSelectCallback()` / `setOnSearchChangeCallback()` (properties require a `\Closure`).

## Step 11 – classes extending the package

Only classes that extend a class of `ModulIS\Form` or implement its interfaces.

**Find:** `extends \\?ModulIS\\Form\\|extends (FormComponent|Form|Container|Duplicator|DuplicatorContainer|TextInput|SelectBox|…)`
and `implements .*(Renderable|Signalable|HasInputGroup)`.

**Change** the overridden methods to the v2 signatures:

| Method | v2 signature |
| --- | --- |
| `FormComponent::beforeRender()` | `protected function beforeRender(): void` |
| `FormComponent::render()` | `public function render(): void` |
| `signalReceived($signal)` | `public function signalReceived(string $signal): void` |
| fluent setters returning `self` | `: static` |
| `Duplicator::loadHttpData()` / `createDefault()` | `: void` |
| `Duplicator::createComponent($name)` | `createComponent(string $name): ?Nette\ComponentModel\IComponent` |
| `Duplicator::createContainer()` | `: DuplicatorContainer` (not nullable) |
| `Duplicator::createOne($name = null)` | `createOne(string\|int\|null $name = null): DuplicatorContainer` |
| `Duplicator::getContainers()` / `getButtons()` | `(bool $recursive = false): array` |
| `getCoreControlPart()` / `getCoreLabelPart()` | `(string\|int\|null $key = null)` |
| constructors of controls | `$label` typed `string\|\Stringable\|null` |

Removed members – **Find** and replace:

| v1 | v2 |
| --- | --- |
| `WrapControl::renderWrap()` | `createWrap(?string $class = null)` |
| `FloatingRenderable` (interface) | remove from `implements` / `instanceof` |
| `MultiWhisperer::validate()` (parent call) | remove the override or `parent::validate()` expectations |
| `Container::addContainer()` typed as `Nette\Forms\Container` | `ModulIS\Form\Container` |
| `Renderable` implementations | add `public function getName(): ?string` if the class is not a Nette component |

## Step 12 – frontend and templates

- **JS bundle.** `form.js` now imports `./rSlider.js`, and `dependentSelect.js` sends requests through
  the global `naja`. **Find** the place importing `vendor/modul-is/form/src/js/form.js`. **Change:**
  nothing if it is imported from the vendor directory; if the files are copied, copy `rSlider.js` too.
  **Report** when Naja is not exposed as `window.naja`.
- **CSS.** `form.css` was rewritten with new `mis-*` classes. **Find** application CSS / SCSS targeting
  form markup: `\.col-form-label|\.form-check|\.align-self-center|\.card-footer|\.input-group` inside form
  scopes. **Report** the rules – they were written against the v1 markup.
- **Snapshots and tests.** **Find** tests comparing rendered form HTML. **Change:** regenerate them after
  the migration and **report** the diff for review.
- **Latte.** Existing `{inputCore}` / `{labelCore}` templates work unchanged – no action.

## Step 13 – verify

1. Run static analysis and tests of the application.
2. Open every changed form and check the layout (step 2), buttons (step 8) and HTML in captions (step 7).
3. Present the collected "report" items to the developer.

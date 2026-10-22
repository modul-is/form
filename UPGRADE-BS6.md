# Upgrade to Bootstrap 6 – migration steps

Steps to migrate forms of an application from the Bootstrap 5 version of `modul-is/form` to the Bootstrap 6 one.
Every step says what to **find** (a regular expression for grep over the application code, usually `app/` and
templates), what to **change**, and when to **report** a finding to the developer instead of changing it.

The client side was rewritten as well - no jQuery, Chosen, select2, autocompleter or rSlider.js are needed anymore.

Rules:

- Go through all steps in order; a step with no match needs no change.
- Change only code matched by a step. Don't refactor forms otherwise.
- Collect every "report" item and present them together at the end.

---

## Step 1 – dependencies and assets

- **Font Awesome is no longer required.** `kravcik/latte-font-awesome-icon` was removed from the dependencies.
  **Find:** `Kravcik\\LatteFontAwesomeIcon` in config (`.neon`) and code. **Change:** keep it only if the application
  itself uses the `{icon}` tag.
- **Icons.** The default icon set is Bootstrap Icons (`<i class="bi bi-…">`). **Change:** either load Bootstrap Icons
  in the application (npm `bootstrap-icons`) and rename icons passed to `setIcon()` (step 9), or keep Font Awesome:

  ```php
  \ModulIS\Form\Renderer\Bootstrap6Renderer::setDefaultIconRenderer(
      \ModulIS\Form\Icon\ClassIconRenderer::fontAwesome('fa-light'),
  );
  ```

- **Client side.** **Find** the import of `vendor/modul-is/form/src/js/form.js` and `src/css/form.css`. **Change:**
  import the npm package instead and call `initForms()` before `naja.initialize()`:

  ```js
  import { initForms } from '@modul-is/form';
  import '@modul-is/form/css';

  initForms(naja);
  ```

  `src/js` and `src/css/form.css` were removed. Peer dependencies of the package are `bootstrap` (with `@floating-ui/dom`
  and `vanilla-calendar-pro`) and `nette-forms`, `naja` is optional.

  **Find** in the application: `jquery|chosen|select2|autocompleter|rSlider|selectize` in `package.json` and in the
  JS entry points. **Change:** remove them when nothing else uses them; **report** what stays.

## Step 2 – grid classes

Bootstrap 6 writes breakpoints as a prefix: `md:col-6` instead of `col-md-6`. The default wrapper class changed
from `mb-2 col-12` to `col-12` - spacing is done by the gutters of the row (`row g-3`).

**Find:** `setWrapClass\(|setDefaultInputWrapClass\(|setLabelWrapClass\(|setInputWrapClass\(|setRowClass\(|->setClass\(`
on groups, containers and inputs.

**Change:**

| Before | After |
| --- | --- |
| `->setWrapClass('mb-2 col-6')`, `->setWrapClass('col-md-6')` | `->setWidth(6)` |
| `->setWrapClass('mb-2 col-12 col-lg-4')` | `->setWidth(['lg' => 4])` |
| other classes in `setWrapClass()` | rewrite `col-{bp}-{n}` → `{bp}:col-{n}`, drop `mb-*` |

`setLabelWrapClass()` / `setInputWrapClass()` now set the columns of the `Inline` render type (default `sm:col-4` /
`sm:col-8`) - rewrite their grid classes the same way. **Report** every changed call with other than grid classes.

## Step 3 – colors and buttons

Buttons are `btn-{variant} theme-{color}`. Colors `default`, `gray`, `light` map to `secondary`, `dark` to `inverse`.

**Find:** `->setColor\(` with a color that is not one of `primary secondary success danger warning info accent inverse light dark gray default`,
and colors written with a prefix like `outline-primary`.

**Change:** `->setColor('outline-primary')` on a button → `->setColor('primary')->setVariant('outline')`.
**Report** other unknown colors.

`setColor()` on a group, container or form colors the card header (not the whole card).

## Step 4 – client side validation is on

Submit buttons no longer carry `formnovalidate` - the form is validated on the client before it is sent
(errors are shown next to the inputs). Buttons with `setValidationScope()` keep their scope.

**Find:** `addSubmit\(`. **Report** forms whose rules can't be checked on the client (custom validators without a
JS counterpart) - add `->setValidationScope([])` to their submit buttons if the server should validate only.

## Step 5 – removed and changed members

| Before | After |
| --- | --- |
| `Checkbox::setCheckboxLeft()` | removed - checkbox is always left of its label |
| `RadioList::setRounded()` | removed |
| `setTooltip()` with HTML | plain text only |
| `setTooltips()` on lists | rendered as item descriptions under the item labels (not tooltips) |
| `setIcon()` on text inputs | icon inside the input (`form-adorn`), not an input group prepend |
| `SelectBox::setImageArray()` | the select is rendered as a combobox with the images |
| `Whisperer::setNoResultMessage()` | available on every select (`setSearch()`), HTML attribute `no-result-message` removed |
| `AutocompleteInput::setPrompt()` | placeholder of the input |
| CSS class `form-control-chosen`, `autocomplete-input`, `select2-image` | removed |
| `ControlGroup::getHeader()` | removed - card header is created by `Bootstrap6Renderer` |
| traits `RenderBasic`, `RenderDefault`, `RenderFloating`, `RenderInline`, `Label`, `InputCoreControl`, `ControlPart`, `ButtonRounded`, `Icon`, `ToggleButton`, `ClassList` | removed - controls use `Helper\Field` / `Helper\Render` |
| `$input->controlClass` | removed |
| `Helper\InputGroup::getPrepend()` / `getAppend()` returning `Html`, public `$prepend` / `$append` | getters return the raw HTML string, properties are protected |
| `getValidationClass()` protected | public |

**Find:** the names above. **Change** according to the table, **report** classes extending controls of the library
(`extends \\?ModulIS\\Form\\`) - their overridden render methods have to move to a renderer subclass.

## Step 6 – slider

`addSlider()` is a native range input now, with `setRange()` two of them over each other (rSlider.js is not used).

- With `setItems()` the HTML input holds the index of the item - `getValue()` still returns the item.
  **Find** JS or tests reading the value of such a slider from the DOM (`[name=…]`, `data-slider`). **Report** them.
- With `setRange()` the value is in a hidden input (`from,to`, items of `setItems()`), the `data-slider` config is gone.
- Without a value the handle starts at the minimum (the browser would put it to the middle).
- `setPrepend()` / `setAppend()` / `setIcon()` are not rendered for a slider. **Find** them on `addSlider()`
  and **report**.
- Ticks: every step, or ten parts when there are more than 20 steps; with more than 11 ticks only the ends are labeled.

## Step 7 – controls driven by JavaScript

The native control stays in the page as the source of truth (hidden), the visible part is created by `assets/js`:

| Control | Visible part |
| --- | --- |
| `addWhisperer()`, `addMultiWhisperer()`, selects with `setSearch()` / `setImageArray()` | Bootstrap combobox next to the hidden select (id of the select + `-combobox`) |
| `addAutocomplete()` | the input itself with a native `<datalist>` (browser look of the suggestions) |
| `addDate()->setPicker()` (new) | text input with the html id, the native date input gets the id + `-value` |
| `addChips()`, slider with `setRange()` | see the README |

- **Find** application JS working with the old widgets: `\.chosen\(|chosen:updated|\.select2\(|autocomplete\(|rSlider|data-slider|form-control-chosen|\.whisperer\(|dependentSelectBox`.
  **Change:** a select changed by the application fires `mis:options` to rebuild its combobox
  (`select.dispatchEvent(new CustomEvent('mis:options'))`); **report** the rest.
- AJAX callbacks (`setOnChangeCallback()`, …) show a spinner next to the label instead of an icon in the input group.
- Currency of `addCurrency()` is shown inside the input (`setSuffix()`), unless the input has `setAppend()`.

## Step 8 – duplicator, manual rendering, configuration

- **Duplicator** is a card with one bordered item per container (`data-mis-duplicator-item`), the remove button is in
  the head of the item, add buttons in the footer. **Find:** `setDuplicatorContainerClass\(|setDuplicatorBodyClass\(|setDuplicatorFooterClass\(|setDuplicatorButtonWrapClass\(`
  and CSS targeting `card-accent-primary|duplicator-head|btn-duplicator`. **Report** them - the default classes changed.
- **Manual templates** (`setRenderManually()`) keep working - `{inputRender}`, `{inputCore}`, `{labelCore}` render the
  Bootstrap 6 markup. **Find** templates combining `{labelCore}` + `{inputCore}` in own markup. **Change:** put
  `data-mis-field` on the element around them, so client side validation shows the errors there (README, Manual rendering).
  `{inputCore list:key}` renders the whole item of a list including its label - **report** templates adding `{labelCore list:key}` next to it.
- **Configuration.** **Find:** `CurrencyInput::setDefaultCurrency\(|CurrencyInput::setDefaultDecimals\(|ModulIS\\Extension\\FormExtension`.
  **Change** (optional): register `ModulIS\Form\DI\FormExtension` and move them to its configuration (README, Installation).

## Step 9 – icon names

Only when switching to Bootstrap Icons (step 1). **Find:** `setIcon\(|setIconArray\(` and group / form icons.
**Change:** Font Awesome names to [Bootstrap Icons](https://icons.getbootstrap.com/) names (e.g. `user` → `person`,
`times` → `x-lg`, `undo` → `arrow-counterclockwise`). `save` on submit buttons is translated by the icon renderer.

## Step 10 – CSS and tests

- **Find** application CSS targeting the old markup: `\.mis-|\.form-check|\.form-select|\.checkbox-switch|\.section-header|\.btn-default|\.chosen-`
  inside forms. **Report** the rules.
- **Find** tests comparing rendered form HTML. **Change:** regenerate them and **report** the diff for review.

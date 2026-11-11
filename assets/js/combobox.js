import Combobox from 'bootstrap/js/dist/combobox.js';
import { register } from './registry.js';
import { getParentValues, request, serializeForm } from './request.js';

/**
 * Select enhanced by Bootstrap combobox (data-mis-combobox) - whisperers, selects with search or images.
 *
 * The native select stays the source of truth: it carries the name, Nette rules and options, it is only hidden.
 * The combobox menu is built from its options and rebuilt whenever they change ("mis:options" from dependent
 * selects or remote search), a selection in the menu is written back to the select and fires its "change".
 *
 * Whisperer with a search callback (data-whisperer) loads the items from the server while typing, with
 * a select callback (data-whisperer-onselect) the server is told about every selection.
 */
const Caret = '<svg class="combobox-caret" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg>';

const SearchDelay = 400;


function isEmptyOption(option)
{
	return option.value === '' && option.text === '';
}


function setSelected(item, selected)
{
	item.classList.toggle('selected', selected);
	item.setAttribute('aria-selected', String(selected));
}


/**
 * Name of the hidden input with the label of the selected item (client[id] => client[id-label]), see Whisperer::getLabelHtmlName()
 */
function getLabelName(name)
{
	return name.endsWith(']') ? `${name.slice(0, -1)}-label]` : `${name}-label`;
}


function createItem(option, config)
{
	const item = document.createElement('button');

	item.type = 'button';
	item.className = 'menu-item';
	item.dataset.bsValue = option.value;
	item.setAttribute('role', 'option');
	item.disabled = option.disabled;

	// the empty option only deselects the value - it is never highlighted as the selected item
	setSelected(item, option.selected && !isEmptyOption(option));

	if (option.dataset.misImage)
	{
		const image = document.createElement('img');
		image.src = option.dataset.misImage;
		image.alt = '';
		image.className = 'menu-item-icon';
		image.width = 20;
		image.height = 20;
		item.append(image);
	}

	const label = document.createElement('span');

	// the empty option of a single select deselects the value - shown as the placeholder
	if (isEmptyOption(option))
	{
		label.textContent = config.placeholder ?? '';
		label.className = 'fg-3';
	}
	else
	{
		label.textContent = option.text;
	}

	item.append(label);

	return item;
}


function buildMenu(select, menu, config)
{
	menu.querySelectorAll(':scope > .menu-item, :scope > .menu-header, :scope > .menu-divider').forEach((el) => el.remove());

	const noResults = menu.querySelector(':scope > .combobox-no-results');
	const items = [];

	const addOption = (option) =>
	{
		// a multiselect has nothing to deselect by an empty option
		if (select.multiple && option.value === '')
		{
			return;
		}

		items.push(createItem(option, config));

		if (option.hasAttribute('data-mis-divider'))
		{
			const divider = document.createElement('hr');
			divider.className = 'menu-divider';
			items.push(divider);
		}
	};

	for (const child of select.children)
	{
		if (child instanceof HTMLOptGroupElement)
		{
			const header = document.createElement('div');
			header.className = 'menu-header';
			header.textContent = child.label;
			items.push(header);

			[...child.children].forEach(addOption);
		}
		else
		{
			addOption(child);
		}
	}

	items.forEach((item) => menu.insertBefore(item, noResults));
	noResults?.classList.toggle('d-none', items.some((item) => item.classList.contains('menu-item')));
}


function updateDisplay(select, toggle, config)
{
	const display = toggle.querySelector('.combobox-value');
	const selected = [...select.selectedOptions].filter((option) => option.value !== '');

	display.classList.toggle('combobox-placeholder', selected.length === 0);
	display.textContent = selected.length ? selected.map((option) => option.text).join(', ') : (config.placeholder ?? '');
	toggle.disabled = select.disabled;
}


function enableRemoteSearch(select, menu, config, refresh)
{
	const searchInput = menu.querySelector('.combobox-search-input');
	const noResults = menu.querySelector('.combobox-no-results');
	const parents = JSON.parse(select.dataset.dependentselectboxParents ?? '{}');
	const delay = Number.parseInt(select.dataset.whispererDelay ?? '', 10) || SearchDelay;
	let timeout = null;
	let controller = null;

	searchInput?.addEventListener('input', () =>
	{
		clearTimeout(timeout);

		const query = searchInput.value.trim();

		if (query === '')
		{
			return;
		}

		timeout = setTimeout(() =>
		{
			controller?.abort();
			controller = new AbortController();

			noResults.textContent = config.loading;
			noResults.classList.remove('d-none');

			request('POST', select.dataset.whisperer, { param: query, parent: getParentValues(parents) }, { signal: controller.signal })
				.then((payload) =>
				{
					if (searchInput.value.trim() !== query)
					{
						return;
					}

					// selected options stay - the value must not get lost by a search
					const kept = [...select.options].filter((option) => option.selected || option.value === '');
					const keptValues = new Set(kept.map((option) => option.value));
					const found = (payload?.suggestions ?? [])
						.filter((suggestion) => !keptValues.has(String(suggestion.data)))
						.map((suggestion) => new Option(suggestion.value, suggestion.data));

					select.replaceChildren(...kept, ...found);
					refresh();

					// the rebuilt menu lost the filter of the combobox - the item which deselects the value does not match the query
					menu.querySelectorAll(':scope > .menu-item[data-bs-value=""]').forEach((item) => item.style.display = 'none');

					noResults.textContent = config.noResults;
					noResults.classList.toggle('d-none', found.length > 0);
				})
				.catch((error) =>
				{
					if (error?.name !== 'AbortError')
					{
						console.error(error);
					}
				});
		}, delay);
	});
}


register('select[data-mis-combobox]', (select) =>
{
	const config = {
		placeholder: '',
		search: false,
		noResults: 'Nic nenalezeno',
		searchPlaceholder: 'Hledat…',
		loading: 'Načítám…',
		...JSON.parse(select.dataset.misCombobox || '{}'),
	};

	const toggle = document.createElement('button');

	toggle.type = 'button';
	toggle.id = `${select.id}-combobox`;
	toggle.className = 'form-control combobox-toggle';
	toggle.dataset.bsToggle = 'combobox';
	toggle.setAttribute('data-mis-proxy', '');
	toggle.innerHTML = `<span class="combobox-value"></span>${Caret}`;

	// size and validation state rendered on the select belong to the visible part
	for (const name of ['form-control-sm', 'form-control-lg', 'is-invalid', 'is-valid'])
	{
		toggle.classList.toggle(name, select.classList.contains(name));
	}

	const menu = document.createElement('div');
	menu.className = 'menu';

	if (config.search)
	{
		const search = document.createElement('div');
		search.className = 'combobox-search';
		search.innerHTML = '<input type="text" class="form-control combobox-search-input" autocomplete="off">';
		search.firstChild.placeholder = config.searchPlaceholder;
		search.firstChild.setAttribute('aria-label', config.searchPlaceholder);
		menu.append(search);
	}

	const noResults = document.createElement('div');
	noResults.className = 'combobox-no-results d-none px-3 py-2 fg-3';
	noResults.textContent = config.noResults;
	menu.append(noResults);

	select.hidden = true;
	select.after(toggle, menu);

	// inside an input group only the toggle takes part in rounding of the corners
	select.classList.add('input-group-ignore');
	menu.classList.add('input-group-ignore');

	document.querySelectorAll(`label[for="${CSS.escape(select.id)}"]`).forEach((label) => label.htmlFor = toggle.id);

	const refresh = () =>
	{
		buildMenu(select, menu, config);
		updateDisplay(select, toggle, config);
	};

	buildMenu(select, menu, config);

	new Combobox(toggle, {
		multiple: select.multiple,
		placeholder: config.placeholder,
		search: config.search,
		searchNormalize: true,
	});

	updateDisplay(select, toggle, config);

	// items of a remote search are not known on the server - the label of the selected one is submitted with the form,
	// so the server can render the option again when it redraws the form (validation error)
	let labelInput = null;

	const syncLabel = () =>
	{
		if (!select.dataset.whisperer || select.multiple)
		{
			return;
		}

		if (!labelInput)
		{
			labelInput = document.createElement('input');
			labelInput.type = 'hidden';
			labelInput.name = getLabelName(select.name);
			labelInput.setAttribute('data-mis-whisperer-label', '');
			select.before(labelInput);
		}

		labelInput.value = select.value === '' ? '' : (select.selectedOptions[0]?.text ?? '');
		labelInput.disabled = select.disabled || select.value === '';
	};

	syncLabel();

	toggle.addEventListener('change.bs.combobox', (event) =>
	{
		const values = [event.value].flat().map(String);

		for (const option of select.options)
		{
			option.selected = values.includes(option.value);
		}

		// the combobox highlights the clicked item - the one which deselects the value stays plain
		menu.querySelectorAll(':scope > .menu-item[data-bs-value=""]').forEach((item) => setSelected(item, false));

		updateDisplay(select, toggle, config);
		syncLabel();
		select.dispatchEvent(new Event('change', { bubbles: true }));

		if (select.dataset.whispererOnselect)
		{
			request('GET', select.dataset.whispererOnselect, { selected: select.value, formdata: serializeForm(select.form) })
				.catch((error) => console.error(error));
		}
	});

	// the menu is at least as wide as the field
	toggle.addEventListener('show.bs.combobox', () =>
	{
		menu.style.minWidth = `${toggle.offsetWidth}px`;
	});

	select.addEventListener('mis:options', refresh);

	if (select.dataset.whisperer)
	{
		enableRemoteSearch(select, menu, config, refresh);
	}
});

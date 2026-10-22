import { getParentElements, getParentValues, request } from './request.js';

/**
 * Dependent select (addDependentSelect(), addDependentMultiSelect(), Whisperer with a dependent callback) -
 * items are loaded from the server whenever a parent changes. Text parents are waited for while typing.
 *
 * After the options are replaced the select fires "mis:options" (combobox rebuilds its menu) and "change"
 * (chained dependent selects load their items too).
 *
 * Listeners are delegated to the document - a redrawn snippet never leaves stale bindings on parents outside of it.
 */
const TypingDelay = 350;

const timeouts = new Map;


function createOption(item, selectedValue)
{
	const option = new Option(item.value, item.key);

	for (const [name, value] of Object.entries(item.attributes ?? {}))
	{
		option.setAttribute(name, value === true ? '' : value);
	}

	if (selectedValue !== null && String(item.key) === String(selectedValue))
	{
		option.selected = true;
	}

	return option;
}


export function fillSelect(select, data)
{
	const options = [];

	// a multiselect has no prompt - an empty option would be a selectable item
	if (data.prompt && !select.multiple)
	{
		options.push(new Option(data.prompt, ''));
	}

	for (const item of data.items ?? [])
	{
		if (typeof item.value === 'object')
		{
			const group = document.createElement('optgroup');
			group.label = item.key;
			group.append(...Object.values(item.value).map((groupItem) => createOption(groupItem, data.value)));
			options.push(group);
		}
		else
		{
			options.push(createOption(item, data.value));
		}
	}

	select.replaceChildren(...options);

	if (data.disabledWhenEmpty)
	{
		select.disabled = (data.items ?? []).length === 0;
	}

	select.dispatchEvent(new CustomEvent('mis:options', { bubbles: true }));
}


function load(select)
{
	const parents = JSON.parse(select.dataset.dependentselectboxParents ?? '{}');

	request('GET', select.dataset.dependentselectbox, getParentValues(parents), { unique: `dependentSelect-${select.id}` })
		.then((payload) =>
		{
			const data = payload?.dependentselectbox;
			const target = data ? document.getElementById(data.id) : null;

			if (!target)
			{
				return;
			}

			fillSelect(target, data);

			// chained dependents listen to the change of their parent - a whisperer keeps its own value
			if (!target.hasAttribute('data-whisperer'))
			{
				target.dispatchEvent(new Event('change', { bubbles: true }));
			}
		})
		.catch((error) => console.error(error));
}


function findDependents(element)
{
	return [...document.querySelectorAll('select[data-dependentselectbox]')].filter((select) =>
		getParentElements(JSON.parse(select.dataset.dependentselectboxParents ?? '{}')).includes(element));
}


document.addEventListener('change', (event) =>
{
	if (event.target.matches?.('select, input[type="checkbox"], input[type="radio"]'))
	{
		findDependents(event.target).forEach(load);
	}
});

document.addEventListener('input', (event) =>
{
	if (!event.target.matches?.('textarea, input:not([type="checkbox"], [type="radio"])'))
	{
		return;
	}

	for (const select of findDependents(event.target))
	{
		clearTimeout(timeouts.get(select.id));
		timeouts.set(select.id, setTimeout(() => load(select), TypingDelay));
	}
});

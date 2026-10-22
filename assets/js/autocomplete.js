import { register } from './registry.js';
import { getParentValues, request, serializeForm } from './request.js';

/**
 * Autocomplete (addAutocomplete()) - suggestions are a native <datalist>, with a search callback (data-autocomplete)
 * they are loaded from the server while typing. With a select callback (data-autocomplete-onselect) the server gets
 * the key of the suggestion the user picked.
 */
const SearchDelay = 200;


function findSuggestion(input)
{
	return [...(input.list?.options ?? [])].find((option) => option.value === input.value) ?? null;
}


register('input[data-autocomplete]', (input) =>
{
	const parents = JSON.parse(input.dataset.autocompleteParents ?? '{}');
	const delay = Number.parseInt(input.dataset.autocompleteDelay ?? '', 10) || SearchDelay;
	let timeout = null;
	let controller = null;

	input.addEventListener('input', (event) =>
	{
		// picking a suggestion is not typing - the list stays
		if (event.inputType === 'insertReplacementText' || !input.list)
		{
			return;
		}

		clearTimeout(timeout);

		const query = input.value.trim();

		if (query === '')
		{
			return;
		}

		timeout = setTimeout(() =>
		{
			controller?.abort();
			controller = new AbortController();

			request('POST', input.dataset.autocomplete, { param: query, parent: getParentValues(parents) }, { signal: controller.signal })
				.then((payload) =>
				{
					input.list.replaceChildren(...(payload?.suggestions ?? []).map((suggestion) =>
					{
						const option = new Option(suggestion.value);
						option.value = suggestion.value;
						option.dataset.key = suggestion.data;

						return option;
					}));
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
});


register('input[data-autocomplete-onselect]', (input) =>
{
	let lastKey = null;

	input.addEventListener('input', () =>
	{
		const suggestion = findSuggestion(input);

		if (!suggestion || suggestion.dataset.key === lastKey)
		{
			return;
		}

		lastKey = suggestion.dataset.key;

		request('GET', input.dataset.autocompleteOnselect, { selected: suggestion.dataset.key, formdata: serializeForm(input.form) })
			.catch((error) => console.error(error));
	});
});

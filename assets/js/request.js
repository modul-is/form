/**
 * AJAX requests of the form (signals, dependent selects, whisperers) - sent by naja when it is passed to initForms(),
 * so snippets redrawn by the callbacks are updated, otherwise by fetch.
 */
let najaInstance = null;


export function useNaja(naja)
{
	najaInstance = naja;
}


/**
 * @param {'GET'|'POST'} method
 * @param {string} url
 * @param {object} data
 * @param {{unique?: string|false, signal?: AbortSignal}} options
 * @returns {Promise<object>} payload
 */
export function request(method, url, data = {}, options = {})
{
	if (najaInstance)
	{
		return najaInstance.makeRequest(method, url, data, { history: false, unique: false, ...options });
	}

	const body = toSearchParams(data);
	const target = new URL(url, document.baseURI);

	if (method === 'GET')
	{
		body.forEach((value, key) => target.searchParams.append(key, value));
	}

	return fetch(target, {
		method,
		body: method === 'GET' ? null : body,
		signal: options.signal,
		headers: { 'X-Requested-With': 'XMLHttpRequest' },
	}).then((response) =>
	{
		if (!response.ok)
		{
			throw new Error(`Request failed: ${response.status}`);
		}

		return response.headers.get('Content-Type')?.includes('json') ? response.json() : {};
	});
}


/**
 * Form values as a query string - the format the PHP side parses (Helper\FormData), files are left out
 */
export function serializeForm(form)
{
	const params = new URLSearchParams;

	for (const [key, value] of new FormData(form))
	{
		if (typeof value === 'string')
		{
			params.append(key, value);
		}
	}

	return params.toString();
}


/**
 * Values of parent controls by their normalized names (JSON in data-*-parents: name => html id) - radio lists
 * are looked up by the id prefix of their items, unchecked checkbox is 0
 */
export function getParentValues(parents)
{
	const values = {};

	for (const [name, id] of Object.entries(parents ?? {}))
	{
		const element = document.getElementById(id);

		if (element)
		{
			if (element.type === 'checkbox')
			{
				values[name] = element.checked ? 1 : 0;
			}
			else if (element.value !== '')
			{
				values[name] = element.value;
			}

			continue;
		}

		const checked = document.querySelector(`[id^="${CSS.escape(id)}-"]:checked`);

		if (checked)
		{
			values[name] = checked.value;
		}
	}

	return values;
}


/**
 * Elements of the parents - single control or items of a list
 */
export function getParentElements(parents)
{
	return Object.values(parents ?? {}).flatMap((id) =>
	{
		const element = document.getElementById(id);

		return element ? [element] : [...document.querySelectorAll(`[id^="${CSS.escape(id)}-"]`)];
	});
}


function toSearchParams(data, params = new URLSearchParams, prefix = null)
{
	for (const [key, value] of Object.entries(data))
	{
		const name = prefix ? `${prefix}[${key}]` : key;

		if (value !== null && typeof value === 'object')
		{
			toSearchParams(value, params, name);
		}
		else if (value !== undefined && value !== null)
		{
			params.append(name, value);
		}
	}

	return params;
}

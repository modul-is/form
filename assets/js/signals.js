import { register } from './registry.js';
import { request, serializeForm } from './request.js';

/**
 * AJAX callbacks of controls - setOnChangeCallback(), setOnFocusOutCallback() (data-on-change, data-on-focusout)
 * and of buttons (data-on-click). The label of the field shows a spinner while the request runs.
 */

function getSignalValue(input)
{
	if (input.type === 'checkbox')
	{
		return input.checked ? 1 : 0;
	}

	if (input.multiple)
	{
		return [...input.selectedOptions].map((option) => option.value);
	}

	return input.value;
}


/**
 * Items of a checkbox list share the name - the item is identified by its value
 */
function getSignalInputName(input)
{
	return input.type === 'checkbox' && input.name.endsWith('[]') ? input.name + input.value : input.name;
}


function showProgress(input)
{
	const label = input.closest('[data-mis-field]')?.querySelector('label, .form-label, .col-form-label');

	if (!label)
	{
		return () => {};
	}

	label.querySelector(':scope > .mis-signal')?.remove();

	const indicator = document.createElement('span');
	indicator.className = 'mis-signal spinner-border spinner-border-sm ms-2';
	indicator.setAttribute('role', 'status');
	label.append(indicator);

	return (error = null) =>
	{
		if (!error)
		{
			indicator.remove();
			return;
		}

		indicator.className = 'mis-signal fg-danger ms-2';
		indicator.textContent = '!';
		indicator.title = String(error);
	};
}


function sendSignal(input, url, event)
{
	const relatedId = event.relatedTarget?.id;
	const done = showProgress(input);

	request('POST', url, {
		value: getSignalValue(input),
		input: getSignalInputName(input),
		formdata: serializeForm(input.form),
	})
		.then(() =>
		{
			done();

			// a redrawn snippet loses the focus - give it back to the element the user went to
			if (relatedId && document.activeElement === document.body)
			{
				document.getElementById(relatedId)?.focus();
			}
		})
		.catch((error) =>
		{
			done(error);
			console.error(error);
		});
}


register('[data-on-change]', (input) =>
{
	input.addEventListener('change', (event) => sendSignal(input, input.dataset.onChange, event));
});

register('[data-on-focusout]', (input) =>
{
	input.addEventListener('focusout', (event) =>
	{
		if (input.value !== input.defaultValue)
		{
			sendSignal(input, input.dataset.onFocusout, event);
		}
	});
});

register('[data-on-click]', (button) =>
{
	button.addEventListener('click', () =>
	{
		request('POST', button.dataset.onClick, { formdata: serializeForm(button.form) })
			.catch((error) => console.error(error));
	});
});

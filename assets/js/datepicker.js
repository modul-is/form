import { register } from './registry.js';

/**
 * Date with Bootstrap datepicker (addDate()->setPicker()) - the picker runs on a visible text input, which shows
 * the date in the format of the browser locale. The native date input next to it stays the source of truth
 * (Y-m-d, name, Nette rules), it is only hidden.
 *
 * The datepicker (with Vanilla Calendar Pro) is loaded only when a form uses it.
 */
register('[data-mis-datepicker]', async (proxy) =>
{
	const { default: Datepicker } = await import('bootstrap/js/dist/datepicker.js');
	const input = document.getElementById(proxy.dataset.misDatepicker);

	if (!input)
	{
		return;
	}

	const picker = Datepicker.getOrCreateInstance(proxy);

	const setValue = (value) =>
	{
		if (input.value === value)
		{
			return;
		}

		input.value = value;
		input.dispatchEvent(new Event('change', { bubbles: true }));
	};

	proxy.addEventListener('change.bs.datepicker', (event) => setValue(event.dates?.[0] ?? ''));

	// the text is read-only - a not required date is cleared by Backspace / Delete
	proxy.addEventListener('keydown', (event) =>
	{
		if (['Backspace', 'Delete'].includes(event.key) && !input.required)
		{
			event.preventDefault();
			picker.setSelectedDates([]);
			proxy.value = '';
			setValue('');
		}
	});
});

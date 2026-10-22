import Chips from 'bootstrap/js/dist/chips.js';
import { register } from './registry.js';

/**
 * Chips keep their values in the DOM only - the hidden select next to them (ChipsInput) is what the form
 * submits and validates, so it is rebuilt on every change.
 */
register('[data-mis-chips]', (element) =>
{
	const select = document.getElementById(element.dataset.misChips);
	const chips = Chips.getOrCreateInstance(element);

	if (!select)
	{
		return;
	}

	element.addEventListener('change.bs.chips', () =>
	{
		select.replaceChildren(...chips.getValues().map((value) => new Option(value, value, true, true)));
		select.dispatchEvent(new Event('change', { bubbles: true }));
	});
});

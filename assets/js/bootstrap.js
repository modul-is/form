/**
 * Bootstrap plugins used by the form. Their own data-API initializes elements on page load only,
 * here they are initialized also in snippets redrawn by naja.
 */
import OtpInput from 'bootstrap/js/dist/otp-input.js';
import Range from 'bootstrap/js/dist/range.js';
import Strength from 'bootstrap/js/dist/strength.js';
import Tooltip from 'bootstrap/js/dist/tooltip.js';
import { register } from './registry.js';

// help icons next to captions (setTooltip())
register('.mis-tooltip[data-bs-toggle="tooltip"]', (element) => Tooltip.getOrCreateInstance(element));

register('[data-bs-otp]', (element) => OtpInput.getOrCreateInstance(element));

register('[data-bs-strength]', (element) => Strength.getOrCreateInstance(element));

register('.form-range', (element) =>
{
	const items = element.dataset.misRangeItems ? JSON.parse(element.dataset.misRangeItems) : null;

	if (!items)
	{
		Range.getOrCreateInstance(element);
		return;
	}

	// slider with items holds their indexes - the bubble shows the item; data-API of Bootstrap may have
	// created the instance without the formatter already
	Range.getInstance(element)?.dispose();
	new Range(element, { formatter: (value) => items[value] ?? value });
});

// mixed state of a checkbox (setIndeterminate()) has no HTML attribute
register('[data-mis-indeterminate]', (element) =>
{
	element.indeterminate = true;
});

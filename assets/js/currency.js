import { register } from './registry.js';

/**
 * Currency input (addCurrency()) - thousands separators while typing, decimal comma.
 * Both decimal comma and decimal point are accepted, the last one of them is the decimal separator
 * (so pasted "1,234.56" and "1.234,56" both work). With pad = true (leaving the input) the decimal part
 * is filled with zeros up to the set number of decimals.
 */
function format(input, pad = false)
{
	const decimals = Number.parseInt(input.dataset.currencyDecimals ?? '0', 10) || 0;
	const value = input.value;
	let integerPart = value;
	let decimalPart = null;

	if (decimals > 0)
	{
		const separator = Math.max(value.lastIndexOf(','), value.lastIndexOf('.'));

		if (separator !== -1)
		{
			integerPart = value.slice(0, separator);
			decimalPart = value.slice(separator + 1).replace(/[^0-9]/g, '').slice(0, decimals);
		}
	}

	integerPart = integerPart.replace(/[^0-9]/g, '');

	if (integerPart === '' && decimalPart === null)
	{
		input.value = '';
		return;
	}

	if (pad && decimals > 0)
	{
		decimalPart = (decimalPart ?? '').padEnd(decimals, '0');
	}

	const formatted = Number.parseInt(integerPart || '0', 10).toLocaleString('cs-CZ').replace(/ /g, ' ')
		+ (decimalPart !== null ? ',' + decimalPart : '');

	// the caret keeps its distance from the end, separators added in front of it would move it otherwise
	const fromEnd = input.value.length - (input.selectionEnd ?? input.value.length);

	input.value = formatted;

	if (!pad && document.activeElement === input)
	{
		const position = Math.max(0, formatted.length - fromEnd);
		input.setSelectionRange(position, position);
	}
}


register('[data-currency-input]', (input) =>
{
	input.addEventListener('input', () => format(input));
	input.addEventListener('change', () => format(input, true));
});


/**
 * Client side rules (min, max, range) get the plain number - parseFloat would stop at the separators
 * ("10 000" -> 10, "10,50" -> 10)
 */
export function installCurrency(netteForms)
{
	const getValue = netteForms.getValue.bind(netteForms);

	netteForms.getValue = function(element)
	{
		const value = getValue(element);

		return element instanceof HTMLInputElement && element.hasAttribute('data-currency-input') && typeof value === 'string'
			? value.replace(/[\s  ]/g, '').replace(',', '.')
			: value;
	};
}

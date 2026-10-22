/**
 * Client side of modul-is/form - no jQuery, only Bootstrap 6 and nette-forms.
 *
 * Forms are mounted on page load and after every snippet update (naja).
 */
import netteForms from 'nette-forms';
import { installCurrency } from './currency.js';
import { mount } from './registry.js';
import { useNaja } from './request.js';
import { installValidation } from './validation.js';
import './autocomplete.js';
import './bootstrap.js';
import './chips.js';
import './combobox.js';
import './datepicker.js';
import './dependent.js';
import './quickCopy.js';
import './rangeDual.js';
import './signals.js';

export { register, mount } from './registry.js';


/**
 * Naja extension - mounts forms in redrawn snippets
 */
export const najaExtension = {
	initialize(naja)
	{
		naja.snippetHandler.addEventListener('afterUpdate', (event) => mount(event.detail.snippet));
	},
};


/**
 * Mounts all forms once the document is ready. Pass naja to mount also forms in redrawn snippets and to send
 * AJAX requests of the form through it (call it before naja.initialize()).
 */
export function initForms(naja = null)
{
	installValidation(netteForms);
	installCurrency(netteForms);

	if (naja)
	{
		useNaja(naja);
		naja.registerExtension(najaExtension);
	}

	if (document.readyState === 'loading')
	{
		document.addEventListener('DOMContentLoaded', () => mount(document), { once: true });
	}
	else
	{
		mount(document);
	}
}

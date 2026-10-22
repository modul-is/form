/**
 * Client side validation errors shown the same way as server side ones - is-invalid on the control
 * and .invalid-feedback in the field, instead of the modal window of nette-forms.
 *
 * The field is the element with data-mis-field rendered around every control, the message goes to its
 * [data-mis-feedback] child when there is one (checkbox), otherwise to the field itself before .form-text.
 * Errors of controls rendered outside of such a field (custom templates) fall back to nette-forms.
 */

const ClientFeedback = 'data-mis-client-feedback';


function findField(element)
{
	return element.closest('[data-mis-field]');
}


function getControls(field, element)
{
	return element.name
		? field.querySelectorAll(`[name="${CSS.escape(element.name)}"]`)
		: [element];
}


function clearField(field)
{
	field.querySelectorAll('.is-invalid, .is-valid').forEach((el) => el.classList.remove('is-invalid', 'is-valid'));
	field.querySelectorAll('.invalid-feedback, .valid-feedback').forEach((el) => el.remove());
}


function showError(field, element, messages)
{
	getControls(field, element).forEach((control) =>
	{
		control.classList.add('is-invalid');
		control.closest('.form-floating, .otp')?.classList.add('is-invalid');
	});

	// hidden controls (chips) are shown by another element, which gets the state too
	field.querySelectorAll('[data-mis-proxy]').forEach((proxy) => proxy.classList.add('is-invalid'));

	const target = field.querySelector(':scope > [data-mis-feedback]') ?? field;
	const feedback = document.createElement('div');

	feedback.className = 'invalid-feedback';
	feedback.setAttribute(ClientFeedback, '');
	feedback.textContent = messages.join(' ');

	target.insertBefore(feedback, target.querySelector(':scope > .form-text'));
}


/**
 * @param {import('nette-forms').default} netteForms
 */
export function installValidation(netteForms)
{
	// client side of the rules in ModulIS\Form\FormValidator
	netteForms.validators.ModulISFormFormValidator_greater = (element, arg, value) => Number.parseFloat(value) > Number.parseFloat(arg);
	netteForms.validators.ModulISFormFormValidator_less = (element, arg, value) => Number.parseFloat(value) < Number.parseFloat(arg);
	netteForms.validators.ModulISFormFormValidator_sameLength = (element, arg, value) => String(arg).length === String(value).length;

	const showFormErrors = netteForms.showFormErrors.bind(netteForms);

	netteForms.showFormErrors = function(form, errors)
	{
		// every validation run starts from scratch - also server side state of the previous submit is dropped
		form.querySelectorAll('[data-mis-field]').forEach(clearField);

		const fields = new Map;
		const unhandled = [];

		for (const error of errors)
		{
			const field = findField(error.element);

			if (!field)
			{
				unhandled.push(error);
				continue;
			}

			const entry = fields.get(field) ?? { element: error.element, messages: [] };

			if (!entry.messages.includes(error.message))
			{
				entry.messages.push(error.message);
			}

			fields.set(field, entry);
		}

		fields.forEach(({ element, messages }, field) => showError(field, element, messages));

		const first = errors[0]?.element;

		(first?.hidden ? findField(first)?.querySelector('[data-mis-proxy]') : first)?.focus();

		if (unhandled.length)
		{
			showFormErrors(form, unhandled);
		}
	};

	// once the user fixes an invalid control its error disappears, without waiting for the next submit
	const revalidate = (event) =>
	{
		const element = event.target;
		const field = element.form && element.matches('[data-nette-rules]') ? findField(element) : null;

		if (field?.querySelector('.is-invalid') && netteForms.validateControl(element, undefined, true))
		{
			clearField(field);
		}
	};

	document.addEventListener('change', revalidate);
	document.addEventListener('focusout', revalidate);
}

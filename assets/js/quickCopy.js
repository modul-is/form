import Tooltip from 'bootstrap/js/dist/tooltip.js';
import { register } from './registry.js';

/**
 * Button copying the value of the input to the clipboard (setQuickCopy())
 */
register('[data-mis-copy]', (button) =>
{
	button.addEventListener('click', async () =>
	{
		const input = button.closest('.input-group')?.querySelector('input:not([type="hidden"]), textarea');
		const value = input?.value.trim();

		if (!value || !navigator.clipboard)
		{
			return;
		}

		await navigator.clipboard.writeText(value);

		const tooltip = new Tooltip(button, { title: button.dataset.misCopied ?? 'Zkopírováno', trigger: 'manual' });

		tooltip.show();

		setTimeout(async () =>
		{
			await tooltip.hide();
			tooltip.dispose();
		}, 1200);
	});
});

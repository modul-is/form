import { register } from './registry.js';

/**
 * Slider with two handles (addSlider()->setRange()) - two range inputs over each other, the hidden input
 * carries the value "from,to" (items of setItems() instead of their indexes). Tick marks of the first input
 * are drawn by the Bootstrap range plugin.
 */
function createBubble(wrapper)
{
	const bubble = document.createElement('output');
	bubble.className = 'form-range-bubble tooltip bs-tooltip-top show';
	bubble.setAttribute('aria-hidden', 'true');
	bubble.innerHTML = '<div class="tooltip-arrow"></div><div class="tooltip-inner"></div>';
	wrapper.append(bubble);

	return bubble;
}


function ratio(input)
{
	const min = Number.parseFloat(input.min || '0');
	const span = Number.parseFloat(input.max || '100') - min;

	return span > 0 ? (Number.parseFloat(input.value) - min) / span : 0;
}


register('[data-mis-range-dual]', (wrapper) =>
{
	const from = wrapper.querySelector('[data-mis-range-part="from"]');
	const to = wrapper.querySelector('[data-mis-range-part="to"]');
	const hidden = wrapper.querySelector('input[type="hidden"]');
	const items = wrapper.dataset.misRangeItems ? JSON.parse(wrapper.dataset.misRangeItems) : null;
	const bubbles = wrapper.hasAttribute('data-mis-bubble') ? [createBubble(wrapper), createBubble(wrapper)] : [];
	const label = (input) => (items ? items[input.value] : input.value);

	const update = (moved = null) =>
	{
		// handles never cross - the moved one stops at the other
		if (Number.parseFloat(from.value) > Number.parseFloat(to.value))
		{
			if (moved === from)
			{
				from.value = to.value;
			}
			else
			{
				to.value = from.value;
			}
		}

		wrapper.style.setProperty('--mis-range-from', String(ratio(from)));
		wrapper.style.setProperty('--mis-range-to', String(ratio(to)));

		// at the end of the track the "from" handle has to be on top, otherwise it could not be moved back
		from.style.zIndex = ratio(from) > 0.5 ? '2' : '';

		hidden.value = `${label(from)},${label(to)}`;

		bubbles.forEach((bubble, index) =>
		{
			const input = index === 0 ? from : to;

			bubble.style.setProperty('--bs-range-fill', String(ratio(input)));
			bubble.querySelector('.tooltip-inner').textContent = label(input);
		});
	};

	for (const input of [from, to])
	{
		input.addEventListener('input', () => update(input));
		input.addEventListener('change', () => hidden.dispatchEvent(new Event('change', { bubbles: true })));
	}

	update();
});

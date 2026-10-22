/**
 * Every feature registers an init function for a selector. mount() initializes each element
 * by each function only once, so it may be called repeatedly on the same part of the page.
 */
const registry = [];
const mounted = (new WeakMap);


/**
 * @param {string} selector
 * @param {(element: Element) => void} init
 */
export function register(selector, init)
{
	registry.push({ selector, init });
}


/**
 * @param {Document|Element} root
 */
export function mount(root = document)
{
	for (const { selector, init } of registry)
	{
		const elements = [...root.querySelectorAll(selector)];

		if (root instanceof Element && root.matches(selector))
		{
			elements.unshift(root);
		}

		for (const element of elements)
		{
			const done = mounted.get(element) ?? (new Set);

			if (done.has(init))
			{
				continue;
			}

			done.add(init);
			mounted.set(element, done);
			init(element);
		}
	}
}

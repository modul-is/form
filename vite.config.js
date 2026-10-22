import { defineConfig } from 'vite';

// Builds the demo page assets with fixed names, so the layout can link them without a manifest
export default defineConfig({
	base: './',
	publicDir: false,
	build: {
		outDir: 'demo/www/dist',
		emptyOutDir: true,
		sourcemap: true,
		rollupOptions: {
			input: 'demo/assets/demo.js',
			output: {
				entryFileNames: 'demo.js',
				chunkFileNames: '[name].js',
				assetFileNames: 'demo[extname]',
			},
		},
	},
});

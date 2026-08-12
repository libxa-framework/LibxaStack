/*
 | Application entry point
 |
 | Referenced by layouts/app.blade.php through @vite(). Vite compiles this into
 | src/public/build/ and writes a manifest the framework reads to emit
 | cache-busted <script>/<link> tags.
 |
 | The Vite config already registers the React, Vue and Svelte plugins, so you
 | can import components from any of them here, or delete the plugins you do
 | not need from vite.config.js.
 */

import '../css/app.css';

// Example: mount a React island rendered by the @react Blade directive.
//
// import { createRoot } from 'react-dom/client';
// document.querySelectorAll('[data-Libxa-react]').forEach(async (el) => {
//     const name  = el.dataset.libxaReact;
//     const props = JSON.parse(el.dataset.props || '{}');
//     const { default: Component } = await import(`./components/${name}.jsx`);
//     createRoot(el).render(<Component {...props} />);
// });

console.info('LibxaFrame app booted');

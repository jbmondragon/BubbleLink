// Boot the shared frontend runtime: Axios defaults, Alpine components, and DOM helpers.
import './bootstrap';
import { registerAlpineComponents, registerDomHandlers } from './alpine-components';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Register reusable Alpine factories before Alpine scans the page markup.
registerAlpineComponents(Alpine);

// Attach plain DOM listeners used outside of Alpine components.
registerDomHandlers();

Alpine.start();

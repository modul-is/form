import Tooltip from 'bootstrap/js/dist/tooltip.js';
import { register } from './registry.js';

// help icons next to captions (setTooltip())
register('.mis-tooltip[data-bs-toggle="tooltip"]', (element) => Tooltip.getOrCreateInstance(element));

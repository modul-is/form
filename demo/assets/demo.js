import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../../assets/css/form.css';
import './demo.css';

// all Bootstrap plugins register their data-API on import
import 'bootstrap';
import naja from 'naja';
import netteForms from 'nette-forms';
import { initForms } from '../../assets/js/index.js';

netteForms.initOnLoad();
naja.formsHandler.netteForms = netteForms;

initForms(naja);

document.addEventListener('DOMContentLoaded', () => naja.initialize({ history: false }));

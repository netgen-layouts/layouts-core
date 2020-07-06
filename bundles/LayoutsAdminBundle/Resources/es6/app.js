import 'whatwg-fetch';
import './plugins/closest_polyfill';
import NlLayouts from './components/layouts';
import NlRules from './components/rules';
import dropdownInit from './plugins/dropdown';
import formsInit from './components/forms';
import '@netgen/content-browser-ui/bundle/Resources/public/css/main.css';
import '../sass/style.scss';

const ngLayoutsInit = () => {
    const layoutsEl = document.getElementById('layouts');
    const rulesEl = document.getElementById('rules');
    const nlLayouts = layoutsEl ? new NlLayouts(layoutsEl) : null; // eslint-disable-line no-unused-vars
    const nlRules = rulesEl ? new NlRules(rulesEl) : null; // eslint-disable-line no-unused-vars

    dropdownInit();
    formsInit();

    [...document.getElementsByClassName('js-open-ngl')].forEach(btn => btn.addEventListener('click', () => {
        localStorage.setItem('ngl_referrer', window.location.href);
    }));
};

window.addEventListener('load', () => {
    ngLayoutsInit();
});

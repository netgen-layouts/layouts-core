import 'whatwg-fetch';
import './helpers/closest_polyfill';
import NlLayouts from './components/layouts';
import NlRules from './components/rules';
import dropdownInit from './helpers/dropdown';

var ngLayoutsInit = () => {
    const layoutsEl = document.getElementById('layouts');
    const rulesEl = document.getElementById('rules');
    const nlLayouts = layoutsEl ? new NlLayouts(layoutsEl) : null;
    const nlRules = rulesEl ? new NlRules(rulesEl) : null;

    dropdownInit();

    [...document.getElementsByClassName('js-open-ngl')].forEach(btn => btn.addEventListener('click', () => {
        localStorage.setItem('ngl_referrer', window.location.href);
    }));
};

window.addEventListener('load', () => {
    ngLayoutsInit();
});

import 'whatwg-fetch';
import './helpers/closest_polyfill';
import NetgenCore from '@netgen/layouts-ui-core';
import NlLayouts from './components/layouts';
import NlRules from './components/rules';
import dropdownInit from './helpers/dropdown';

NetgenCore.ngLayoutsInit = () => {
    const layoutsEl = document.getElementById('layouts');
    const rulesEl = document.getElementById('rules');
    NetgenCore.nlLayouts = layoutsEl ? new NlLayouts(layoutsEl) : null;
    NetgenCore.nlRules = rulesEl ? new NlRules(rulesEl) : null;

    dropdownInit();

    [...document.getElementsByClassName('js-open-ngl')].forEach(btn => btn.addEventListener('click', () => {
        localStorage.setItem('bm_referrer', window.location.href);
    }));
};

window.addEventListener('load', () => {
    NetgenCore.ngLayoutsInit();
});

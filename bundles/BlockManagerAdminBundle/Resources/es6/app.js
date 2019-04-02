import './helpers/closestPolyfill';
import NetgenCore from '@netgen/layouts-ui-core';
import NlLayouts from './components/layouts';
import NlRules from './components/rules';
import dropdownInit from './helpers/dropdown';

const { $ } = NetgenCore;

NetgenCore.ngLayoutsInit = () => {
    const layoutsEl = document.getElementById('layouts');
    const rulesEl = document.getElementById('rules');
    NetgenCore.nlLayouts = layoutsEl ? new NlLayouts(layoutsEl) : null;
    NetgenCore.nlRules = rulesEl ? new NlRules(rulesEl) : null;

    dropdownInit();

    $(document).on('click', '.js-open-bm', () => {
        localStorage.setItem('bm_referrer', window.location.href);
    });
};

$(document).ready(() => {
    NetgenCore.ngLayoutsInit();
});

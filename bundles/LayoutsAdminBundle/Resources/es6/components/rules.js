import NetgenCore from '@netgen/layouts-ui-core';
import NlRule from './rule';

const { $ } = NetgenCore;

/* nl rules app plugin */
export default class NlRules {
    constructor(el) {
        this.el = el;
        [this.rulesContainer] = this.el.getElementsByClassName('nl-rules');
        this.rules = [];
        [this.rulesHeader] = this.el.getElementsByClassName('nl-rules-head');
        [this.noRulesMsg] = this.el.getElementsByClassName('nl-no-items');
        [this.sortBtn] = this.el.getElementsByClassName('js-sort-start');
        [this.appContainer] = document.getElementsByClassName('ng-layouts-app');
        this.csrf = document.querySelector('meta[name=nglayouts-admin-csrf-token]').getAttribute('content');
        this.baseUrl = `${window.location.origin}${document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content')}/mappings/`;
        this.filter = JSON.parse(localStorage.getItem('ngMappingFilters')) || [];

        this.initialize();
    }

    initialize() {
        this.initializeFilters();
        this.initializeRulePlugin();
        this.setupEvents();
        this.setRulesTop();
        this.toggleUI();
        this.el.style.visibility = 'visible';
    }

    initializeRulePlugin() {
        [...this.el.getElementsByClassName('nl-rule')].forEach((el) => {
            const newRule = new NlRule(el, this);
            this.rules.push(newRule);
        });
        this.filterMappings();
    }

    toggleUI() {
        if (!this.rules.length) {
            this.rulesHeader.style.display = 'none';
            this.noRulesMsg.style.display = 'block';
        } else {
            this.rulesHeader.style.display = 'flex';
            this.noRulesMsg.style.display = 'none';
        }
        this.sortBtn.style.display = this.rules.length < 2 ? 'none' : 'inline-block';
    }

    setRulesTop() {
        this.rulesContainer.style.top = `${this.rulesHeader.offsetTop + this.rulesHeader.offsetHeight}px`;
    }

    setupEvents() {
        this.el.getElementsByClassName('js-add-rule')[0].addEventListener('click', this.addRule.bind(this));
        this.el.getElementsByClassName('js-sort-start')[0].addEventListener('click', this.sortStart.bind(this));
        this.el.getElementsByClassName('js-sort-save')[0].addEventListener('click', this.sortSave.bind(this));
        this.el.getElementsByClassName('js-sort-cancel')[0].addEventListener('click', this.sortCancel.bind(this));
    }

    addRule(e) {
        e.preventDefault();
        this.appContainer.classList.add('ajax-loading');
        fetch(`${this.baseUrl}rules`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': this.csrf,
            },
        }).then((response) => {
            this.appContainer.classList.remove('ajax-loading');
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then((data) => {
            const newRuleEl = document.createElement('div');
            newRuleEl.className = 'nl-rule show-body';
            newRuleEl.innerHTML = data;
            this.rulesContainer.appendChild(newRuleEl);
            const newRule = new NlRule(newRuleEl, this);
            this.rules.push(newRule);
            this.toggleUI();
            newRuleEl.scrollIntoView({
                behavior: 'smooth',
            });
        }).catch((error) => {
            console.log(error);
        });
    }

    sortStart() {
        this.appContainer.classList.add('sorting');
        [...document.getElementsByClassName('nl-rule-between')].forEach(el => el.parentElement.removeChild(el));
        $(this.rulesContainer).sortable({
            items: '> .nl-rule',
            axis: 'y',
            update: () => {
                this.sorted = true;
            },
        });
    }

    sortSave() {
        this.appContainer.classList.add('ajax-loading');
        const sorted = $(this.rulesContainer).sortable('toArray', { attribute: 'data-id' });
        const rules = sorted.map(rule => `rule_ids[]=${rule}`);
        const body = new URLSearchParams(rules.join('&'));
        fetch(`${this.baseUrl}rules/priorities`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': this.csrf,
            },
            body,
        }).then((response) => {
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then(() => {
            window.location.reload(); /* reload to set new priority numbers */
        }).catch((error) => {
            this.appContainer.classList.remove('ajax-loading');
            console.log(error);
        });
    }

    sortCancel() {
        if (this.sorted) this.rules.forEach(rule => this.rulesContainer.appendChild(rule.el));
        this.sorted = false;

        this.appContainer.classList.remove('sorting');
        $(this.rulesContainer).sortable('destroy');
        this.filterMappings();
    }

    /* mapping filtering */
    initializeFilters() {
        this.updateFilterInputs();
        [...this.el.querySelectorAll('input[name=filter-mappings]')].forEach(el => el.addEventListener('change', this.updateFilter.bind(this)));
        [...this.el.getElementsByClassName('js-check-all')].forEach(el => el.addEventListener('click', this.checkAllFilters.bind(this)));
        [...this.el.getElementsByClassName('js-check-none')].forEach(el => el.addEventListener('click', this.checkNoneFilters.bind(this)));
    }

    updateFilterInputs() {
        [...this.el.querySelectorAll('input[name=filter-mappings]')].forEach((el) => {
            el.checked = this.filter.includes(el.value); // eslint-disable-line no-param-reassign
        });
    }

    updateFilter(e) {
        e && e.preventDefault();
        e.target.checked ? this.filter.push(e.target.value) : (this.filter = this.filter.filter(item => item !== e.target.value));
        this.saveFilterToStorage();
        this.filterMappings();
    }

    filterMappings() {
        let hiddenItems = 0;
        [...document.getElementsByClassName('nl-rule-between')].forEach(el => el.parentElement.removeChild(el));
        const filterAmountEl = this.el.getElementsByClassName('filter-checked-amount')[0];
        const addRuleBetween = (rule, amount) => {
            const newBetweenEl = document.createElement('div');
            newBetweenEl.className = 'nl-rule-between';
            newBetweenEl.innerHTML = `<i class="material-icons">more_vert</i><span class="hidden-amount">${amount}</span>`;
            rule.el.parentElement.insertBefore(newBetweenEl, rule.el);
        };
        if (this.filter.length) {
            filterAmountEl.innerHTML = this.filter.length;
            filterAmountEl.style.display = 'block';
        } else {
            filterAmountEl.style.display = 'none';
        }
        this.rules.forEach((rule, i) => {
            const isHidden = !!this.filter.length && !this.filter.includes(rule.attributes.targetType);
            rule.isHidden = isHidden; // eslint-disable-line no-param-reassign
            rule.el.classList.toggle('nl-rule-hidden', isHidden);
            if (isHidden) {
                hiddenItems++;
                if (i === this.rules.length - 1) {
                    addRuleBetween(rule, hiddenItems);
                }
            } else if (hiddenItems) {
                addRuleBetween(rule, hiddenItems);
                hiddenItems = 0;
            }
        });
        this.el.classList.toggle('no-filtered-items', !!this.rules.length && !this.rules.some(rule => !rule.isHidden));
    }

    saveFilterToStorage() {
        localStorage.setItem('ngMappingFilters', JSON.stringify(this.filter));
    }

    checkAllFilters(e) {
        e && e.preventDefault();
        const newFilter = [];
        [...this.el.querySelectorAll('input[name=filter-mappings]')].forEach(el => newFilter.push(el.value));
        this.filter = newFilter;
        this.updateFilterInputs();
        this.saveFilterToStorage();
        this.filterMappings();
    }

    checkNoneFilters(e) {
        e && e.preventDefault();
        this.filter = [];
        this.updateFilterInputs();
        this.saveFilterToStorage();
        this.filterMappings();
    }
    /* /mapping filtering */
}

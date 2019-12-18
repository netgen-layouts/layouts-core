import Sortable from 'sortablejs';
import NlRule from './rule';

/* nl rules app plugin */
export default class NlRules {
    constructor(el) {
        this.el = el;
        [this.rulesContainer] = this.el.getElementsByClassName('nl-rules');
        this.rules = {
          byId: {},
          ids: [],
        };
        [this.rulesHeader] = this.el.getElementsByClassName('nl-rules-head');
        [this.noRulesMsg] = this.el.getElementsByClassName('nl-no-items');
        [this.sortBtn] = this.el.getElementsByClassName('js-sort-start');
        [this.appContainer] = document.getElementsByClassName('ng-layouts-app');
        this.csrf = document.querySelector('meta[name=nglayouts-admin-csrf-token]').getAttribute('content');
        this.apiUrl = `${window.location.origin}${document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content')}`;
        this.baseUrl = `${this.apiUrl}/mappings/`;
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
        [...this.el.getElementsByClassName('nl-rule')].forEach((el, i) => {
            const newRule = new NlRule(el, i, this);
            this.rules.byId[newRule.id] = newRule;
            this.rules.ids.push(newRule.id);
        });
        this.filterMappings();
    }

    toggleUI() {
        if (!this.rules.ids.length) {
            this.rulesHeader.style.display = 'none';
            this.noRulesMsg.style.display = 'block';
        } else {
            this.rulesHeader.style.display = 'flex';
            this.noRulesMsg.style.display = 'none';
        }
        this.sortBtn.style.display = this.rules.ids.length < 2 ? 'none' : 'inline-block';
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
            const newRule = new NlRule(newRuleEl, this.rules.ids.length, this);
            this.rules.byId[newRule.id] = newRule;
            this.rules.ids.push(newRule.id);
            this.toggleUI();
            newRuleEl.scrollIntoView({
                behavior: 'smooth',
            });
        }).catch((error) => {
            console.log(error); // eslint-disable-line no-console
        });
    }

    sortStart() {
        this.rules.ids.forEach(id => this.rules.byId[id].onSortingStart());
        this.appContainer.classList.add('sorting');
        [...document.getElementsByClassName('nl-rule-between')].forEach(el => el.parentElement.removeChild(el));
        this.sortable = new Sortable(this.rulesContainer, {
            draggable: '> .nl-rule',
            direction: 'vertical',
            animation: 150,
            onEnd: (e) => {
              this.moveRule(e.oldIndex, e.newIndex);
            },
        });
        this.initialOrder = this.sortable.toArray();
    }

    sortSave() {
        this.appContainer.classList.add('ajax-loading');
        const sorted = this.sortable.toArray();
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
            this.sortable.destroy();
            this.appContainer.classList.remove('ajax-loading');
            this.appContainer.classList.remove('sorting');
            this.rules.ids.forEach(id => this.rules.byId[id].onSortingEnd());
            this.filterMappings();
        }).catch((error) => {
            this.appContainer.classList.remove('ajax-loading');
            console.log(error); // eslint-disable-line no-console
        });
    }

    sortCancel() {
        this.rules.ids = this.initialOrder;
        this.rules.ids.forEach((id, i) => this.rules.byId[id].onSortingCancel(i));
        this.sortable.sort(this.initialOrder);
        this.sortable.destroy();
        this.appContainer.classList.remove('sorting');
        this.filterMappings();
    }

    moveRule(oldIndex, newIndex, shouldSort) {
      const newOrder = [];
      this.rules.ids.splice(newIndex, 0, this.rules.ids.splice(oldIndex, 1)[0]);
      this.rules.ids.forEach((id, i) => {
        this.rules.byId[id].onSortingChange(i);
        newOrder.push(id);
      });
      shouldSort && this.sortable.sort(newOrder);
    }

    deleteRule(id) {
      this.rules.ids.splice(this.rules.ids.indexOf(id), 1);
      delete this.rules.byId[id];
      this.rules.ids.forEach((ruleId, i) => {
        const rule = this.rules.byId[ruleId];
        rule.priority = i;
        rule.renderPriority();
      });
      this.toggleUI();
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
        this.rules.ids.forEach((id, i) => {
            const rule = this.rules.byId[id];
            const isHidden = !!this.filter.length && !this.filter.includes(rule.attributes.targetType);
            rule.isHidden = isHidden; // eslint-disable-line no-param-reassign
            rule.el.classList.toggle('nl-rule-hidden', isHidden);
            if (isHidden) {
                hiddenItems++;
                if (i === this.rules.ids.length - 1) {
                    addRuleBetween(rule, hiddenItems);
                }
            } else if (hiddenItems) {
                addRuleBetween(rule, hiddenItems);
                hiddenItems = 0;
            }
        });
        this.el.classList.toggle('no-filtered-items', !!this.rules.ids.length && !this.rules.ids.some(id => !this.rules.byId[id].isHidden));
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

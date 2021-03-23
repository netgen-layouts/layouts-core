import Sortable from 'sortablejs';
import NlRule from './rule';
import NlExport from './export';
import NlModal from './modal';


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
        [this.deleteButton] = document.getElementsByClassName('js-multiple-delete');
        [this.exportButton] = document.getElementsByClassName('js-export');
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
        this.export = new NlExport(this.el, this.rules.byId, this);
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
        this.el.getElementsByClassName('js-add-rule')[0] && this.el.getElementsByClassName('js-add-rule')[0].addEventListener('click', this.addRule.bind(this));
        this.el.getElementsByClassName('js-sort-start')[0] && this.el.getElementsByClassName('js-sort-start')[0].addEventListener('click', this.sortStart.bind(this));
        this.el.getElementsByClassName('js-sort-save')[0] && this.el.getElementsByClassName('js-sort-save')[0].addEventListener('click', this.sortSave.bind(this));
        this.el.getElementsByClassName('js-sort-cancel')[0] && this.el.getElementsByClassName('js-sort-cancel')[0].addEventListener('click', this.sortCancel.bind(this));
    }

    addRule(e) {
        e.preventDefault();
        this.showLoader();
        fetch(`${this.baseUrl}groups/00000000-0000-0000-0000-000000000000/new_rule`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': this.csrf,
            },
        }).then((response) => {
            this.hideLoader();
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then((data) => {
          this.createRule(data, this.rules.ids.length, true);
        }).catch((error) => {
            console.log(error); // eslint-disable-line no-console
        });
    }

    createRule(html, priority, shouldScroll) {
      const newRuleEl = document.createElement('div');
      newRuleEl.className = 'nl-rule';
      newRuleEl.setAttribute('tabindex', '0');
      newRuleEl.innerHTML = html;
      priority >= this.rules.ids.length
        ? this.rulesContainer.appendChild(newRuleEl)
        : this.rulesContainer.insertBefore(newRuleEl, this.rules.byId[this.rules.ids[priority]].el);
      const newRule = new NlRule(newRuleEl, priority, this);
      this.rules.byId[newRule.id] = newRule;
      this.rules.ids.splice(priority, 0, newRule.id);
      this.renderRulesPriorities();
      this.toggleUI();
      shouldScroll && newRuleEl.scrollIntoView({
          behavior: 'smooth',
      });
    }

    setSelectingId(id) {
        this.selectingId = id;
        if (this.deleteButton) {
            id === '00000000-0000-0000-0000-000000000000' ? this.deleteButton.style.display = 'inline-block' : this.deleteButton.style.display = 'none';
        }
        if (this.exportButton) {
            id === '00000000-0000-0000-0000-000000000000' ? this.exportButton.style.display = 'inline-block' : this.exportButton.style.display = 'none';
        }
        Object.keys(this.rules.byId).forEach((key) => {
            this.rules.byId[key].handleCheckboxDisable(id);
        });
    }

    checkboxLoop() {
        let checkBoxCount = 0;
        Object.keys(this.rules.byId).forEach((key) => {
            this.rules.byId[key].selected ? checkBoxCount++ : null;
        });
        checkBoxCount ? null : this.setSelectingId(null);
    }

    deleteMultipleElements() {
        const url = `${this.baseUrl}groups/00000000-0000-0000-0000-000000000000/delete_items`;
        const rulesForBody = this.rules.ids.filter(id => this.rules.byId[id].selected).map(id => `ids[${id}]=${this.rules.byId[id].type}`);
        const rules = this.rules.ids.filter(id => this.rules.byId[id].selected).map(id => this.rules.byId[id]);
        const body = new URLSearchParams(rulesForBody.join('&'));
        const modal = new NlModal({
            preload: true,
            autoClose: false,
        });
        const formAction = (ev) => {
            ev.preventDefault();
            modal.loadingStart();
            fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': this.csrf,
                },
                body,
            }).then((response) => {
                if (!response.ok) {
                    return response.text().then((data) => {
                        modal.insertModalHtml(data);
                        throw new Error(`HTTP error, status ${response.status}`);
                    });
                }
                return response.text();
            }).then(() => {
                modal.close();
                rules.forEach((element) => {
                    element.el.parentElement.removeChild(element.el);
                    this.deleteRule(element.id);
                });
                return true;
            }).catch((error) => {
                console.log(error); // eslint-disable-line no-console
            });
        };
        fetch(url, {
            method: 'GET',
        }).then((response) => {
            if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
            return response.text();
        }).then((data) => {
            modal.insertModalHtml(data);
            modal.el.addEventListener('apply', formAction);
        }).catch((error) => {
            console.log(error); // eslint-disable-line no-console
        });
        this.setSelectingId(null);
        this.rules.ids.forEach((id) => {
            this.rules.byId[id].selected = false;
            this.rules.byId[id].selectElement.checked = false;
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
        this.showLoader();
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
            this.hideLoader();
            this.appContainer.classList.remove('sorting');
            this.rules.ids.forEach(id => this.rules.byId[id].onSortingEnd());
            this.filterMappings();
        }).catch((error) => {
            this.hideLoader();
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
      this.renderRulesPriorities();
      this.toggleUI();
    }

    renderRulesPriorities() {
      this.rules.ids.forEach((ruleId, i) => {
        const rule = this.rules.byId[ruleId];
        rule.priority = i;
        rule.renderPriority();
      });
    }

    /* mapping filtering */
    initializeFilters() {
        this.updateFilterInputs();
        [...this.el.querySelectorAll('input[name=filter-mappings]')].forEach(el => el.addEventListener('change', this.updateFilter.bind(this)));
        [...this.el.getElementsByClassName('js-check-all')].forEach(el => el.addEventListener('click', this.checkAllFilters.bind(this)));
        [...this.el.getElementsByClassName('js-check-none')].forEach(el => el.addEventListener('click', this.checkNoneFilters.bind(this)));
        [...this.el.getElementsByClassName('js-multiple-delete')].forEach(el => el.addEventListener('click', this.deleteMultipleElements.bind(this)));
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

    showLoader() {
      this.appContainer.classList.add('ajax-loading');
    }

    hideLoader() {
      this.appContainer.classList.remove('ajax-loading');
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

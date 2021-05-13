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
        [this.addRuleButton] = document.getElementsByClassName('js-add-rule');
        [this.reorderButton] = document.getElementsByClassName('js-sort-start');
        [this.appContainer] = document.getElementsByClassName('ng-layouts-app');
        [this.floatingControls] = document.getElementsByClassName('floating-controls');
        [this.clearSelectionButton] = document.getElementsByClassName('js-clear-selection');
        [this.checkAllBoxesWrapper] = document.getElementsByClassName('js-check-all');
        this.selectElement = document.getElementById('check-all');
        this.csrf = document.querySelector('meta[name=nglayouts-admin-csrf-token]').getAttribute('content');
        this.apiUrl = `${window.location.origin}${document.querySelector('meta[name=nglayouts-admin-base-path]').getAttribute('content')}`;
        this.baseUrl = `${this.apiUrl}/mappings/`;
        this.filter = JSON.parse(localStorage.getItem('ngMappingFilters')) || [];

        [this.selectedItemsText] = this.el.getElementsByClassName('selected-items');

        this.id = '00000000-0000-0000-0000-000000000000';
        this.selectingId = null;
        this.focusedId = null;

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
        this.el.getElementsByClassName('js-clear-selection')[0] && this.el.getElementsByClassName('js-clear-selection')[0].addEventListener('click', this.clearCheckboxes.bind(this));

        this.selectElement.addEventListener('change', () => {
            this.selected = this.selectElement.checked;
            if (this.selected) {
                this.selectAllCheckboxes();
                this.checkboxLoop();
            } else {
                this.clearCheckboxes();
            }
        });
    }

    addRule(e) {
        document.querySelectorAll('.nl-dropdown-active').forEach((el) => {
            el.classList.remove('nl-dropdown-active');
        });
        e.preventDefault();
        this.showLoader();
        fetch(`${this.baseUrl}groups/${this.id}/new_rule`, {
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

    focusCheck() {
        if (this.selectingId === null) {
            this.appContainer.querySelectorAll('.focused').forEach(el => el.classList.remove('focused'));
            return true;
        }
        return false;
    }

    setSelectingId(id) {
        this.selectingId = id;
        id === this.id ? this.floatingControls.style.display = 'flex' : this.floatingControls.style.display = 'none';
        id === this.id ? this.rulesContainer.style.top = '175px' : this.rulesContainer.style.top = '159px';
        id === this.id ? this.rulesHeader.style.display = 'none' : this.rulesHeader.style.display = 'flex';
        id === this.id ? this.checkAllBoxesWrapper.style.visibility = 'visible' : this.checkAllBoxesWrapper.style.visibility = 'hidden';
        this.addRuleButton && id === this.id ? this.addRuleButton.style.display = 'none' : this.addRuleButton.style.display = 'inline-block';
        this.reorderButton && id === this.id ? this.reorderButton.style.display = 'none' : this.reorderButton.style.display = 'inline-block';
        Object.keys(this.rules.byId).forEach((key) => {
            this.rules.byId[key].handleCheckboxDisable(id);
        });
        this.appContainer.querySelectorAll('.focused').forEach(el => el.classList.remove('focused'));
    }

    clearCheckboxes() {
        Object.keys(this.rules.byId).forEach((key) => {
            this.rules.byId[key].selected = false;
            this.rules.byId[key].selectElement.checked = false;
            this.rules.byId[key].el.classList.remove('selected');
        });

        this.checkboxLoop();
    }

    selectAllCheckboxes() {
        Object.keys(this.rules.byId).forEach((key) => {
            this.rules.byId[key].selected = true;
            this.rules.byId[key].selectElement.checked = true;
            this.rules.byId[key].el.classList.add('selected');
        });

        this.checkboxLoop();
    }

    checkboxLoop() {
        let checkBoxCount = 0;
        Object.keys(this.rules.byId).forEach((key) => {
            this.rules.byId[key].selected ? checkBoxCount++ : null;
        });
        checkBoxCount !== Object.keys(this.rules.byId).length ? this.selectElement.checked = false : this.selectElement.checked = true;
        checkBoxCount ? this.setItemNumber(checkBoxCount) : this.setSelectingId(null);
    }

    setItemNumber(checkboxCount) {
        this.selectedItemsText.innerHTML = `${checkboxCount}&nbsp`;
    }

    deleteMultipleElements() {
        const url = `${this.baseUrl}groups/${this.id}/delete_items`;
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
        modal.el.addEventListener('apply', formAction);
        modal.el.addEventListener('cancel', () => this.clearCheckboxes());
    }

    sortStart() {
        document.querySelectorAll('.nl-dropdown-active').forEach((el) => {
            el.classList.remove('nl-dropdown-active');
        });
        this.rules.ids.forEach((id) => {
            this.rules.byId[id].onSortingStart();
            this.rules.byId[id].el.classList.add('disable-checkbox');
        });
        this.appContainer.classList.add('sorting');
        [...document.getElementsByClassName('nl-rule-between')].forEach(el => el.parentElement.removeChild(el));
        this.sortable = new Sortable(this.rulesContainer, {
            draggable: '> .nl-rule',
            direction: 'vertical',
            animation: 150,
            forceFallback: true,
            onEnd: (e) => {
              this.moveRule(e.oldIndex, e.newIndex);
            },
        });
        this.initialOrder = this.sortable.toArray();
    }

    sortSave() {
        this.showLoader();
        this.rules.ids.forEach((id) => {
            this.rules.byId[id].el.classList.remove('disable-checkbox');
        });
        const sorted = this.sortable.toArray();
        const rules = sorted.map(rule => `ids[${rule}]=rule`);
        const body = new URLSearchParams(rules.join('&'));
        fetch(`${this.baseUrl}groups/${this.id}/priorities`, {
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
        }).catch((error) => {
            this.hideLoader();
            console.log(error); // eslint-disable-line no-console
        });
    }

    sortCancel() {
        this.rules.ids.forEach((id) => {
            this.rules.byId[id].el.classList.remove('disable-checkbox');
        });
        this.rules.ids = this.initialOrder;
        this.rules.ids.forEach((id, i) => this.rules.byId[id].onSortingCancel(i));
        this.sortable.sort(this.initialOrder);
        this.sortable.destroy();
        this.appContainer.classList.remove('sorting');
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
    }

    showLoader() {
      this.appContainer.classList.add('ajax-loading');
    }

    hideLoader() {
      this.appContainer.classList.remove('ajax-loading');
    }
}

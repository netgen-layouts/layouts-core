import flatpickr from 'flatpickr';

export default class DateTimePicker {
    constructor(el, opt = {}) {
        this.el = el;
        [this.input] = el.getElementsByClassName('datetime-input-locale');
        [this.formattedEl] = el.getElementsByClassName('datetime-formatted');
        this.options = Object.assign({
            enableTime: true,
            time_24hr: true,
            dateFormat: 'D, j M Y H:i',
            onChange: (selectedDates) => {
                this.onChange(selectedDates);
            },
        }, opt);

        this.init();
    }

    init() {
        this.picker = flatpickr(this.input, this.options);
        !!this.formattedEl.value && this.picker.setDate(this.formattedEl.value, false, 'Y-m-dTH:i');
        this.toggleClearBtn();

        [...this.el.getElementsByClassName('js-clear-input')].forEach(el => el.addEventListener('click', this.clear.bind(this)));
    }

    onChange(date) {
        this.formattedEl.value = date.length ? flatpickr.formatDate(date[0], 'Y-m-dTH:i') : '';
        this.toggleClearBtn();
    }

    toggleClearBtn() {
        this.input.value ? this.el.classList.add('date-entered') : this.el.classList.remove('date-entered');
    }

    clear(e) {
        e.preventDefault();
        this.picker.clear();
    }
}

export const parser = (domstring) => {
    const html = new DOMParser().parseFromString(domstring, 'text/html');
    return Array.from(html.body.childNodes);
};

export const indeterminateCheckboxes = (form) => {
    const checkboxes = [];
    const submit = form.querySelector('button[type="submit"]');
    const changeState = (arr) => {
        let checkedNr = 0;
        arr.forEach(el => el.checked && checkedNr++);
        const toggleAllEl = document.querySelector('input[type="checkbox"]#toggle-all-cache');
        if (toggleAllEl) {
            toggleAllEl.indeterminate = checkedNr > 0 && checkedNr < arr.length;
            toggleAllEl.checked = checkedNr === arr.length;
        }
        if (submit) submit.disabled = checkedNr === 0;
    };
    const allCheckboxes = [...form.querySelectorAll('input[type="checkbox"]')];
    allCheckboxes.forEach((el) => {
        el.id !== 'toggle-all-cache' && checkboxes.push(el);
        el.addEventListener('change', (e) => {
            if (e.currentTarget.id === 'toggle-all-cache') {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = e.currentTarget.checked; // eslint-disable-line no-param-reassign
                });
                if (submit) submit.disabled = !e.currentTarget.checked;
            } else {
                changeState(checkboxes);
            }
        });
    });
    changeState(checkboxes);
};

export const fetchModal = (url, modal, formAction, afterSuccess) => {
    fetch(url, {
        method: 'GET',
    }).then((response) => {
        if (!response.ok) throw new Error(`HTTP error, status ${response.status}`);
        return response.text();
    }).then((data) => {
        modal.insertModalHtml(data);
        modal.el.addEventListener('apply', formAction);
        if (afterSuccess) afterSuccess();
    }).catch((error) => {
        console.log(error); // eslint-disable-line no-console
    });
};

export const submitModal = (url, modal, method, csrf, body, afterSuccess, afterError) => {
    fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            'X-CSRF-Token': csrf,
        },
        body,
    }).then((response) => {
        if (!response.ok) {
            return response.text().then((data) => {
                modal.insertModalHtml(data);
                if (afterError) afterError();
                throw new Error(`HTTP error, status ${response.status}`);
            });
        }
        return response.text();
    }).then((data) => {
        modal.close();
        if (afterSuccess) afterSuccess(data);
        return true;
    }).catch((error) => {
        console.log(error); // eslint-disable-line no-console
    });
};

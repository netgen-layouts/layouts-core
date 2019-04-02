/* dropdown plugin */
const dropdownInit = () => {
    let openedMenu = false;
    const closeLastOpened = () => {
        if (!openedMenu) return;
        openedMenu.classList.remove('nl-dropdown-active');
        openedMenu = false;
    };
    const toggleMenu = (e) => {
        if (e.target.closest('.nl-dropdown-menu')) return;
        const btn = e.target.closest('.nl-dropdown-toggle');
        if (!btn) {
            closeLastOpened();
            return;
        }
        btn.blur();
        const dropdownEl = e.target.closest('.nl-dropdown');
        if (!dropdownEl) return;
        if (dropdownEl.classList.contains('nl-dropdown-active')) {
            dropdownEl.classList.remove('nl-dropdown-active');
            closeLastOpened();
        } else {
            closeLastOpened();
            dropdownEl.classList.add('nl-dropdown-active');
            openedMenu = dropdownEl;
        }
    };

    document.addEventListener('click', toggleMenu, true);
};

export default dropdownInit;

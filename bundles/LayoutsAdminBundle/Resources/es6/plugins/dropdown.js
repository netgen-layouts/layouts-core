/* dropdown plugin */
const dropdownInit = () => {
    let openedMenu = false;
    let menuParentEl = false;
    const closeLastOpened = () => {
        if (!openedMenu) return;
        openedMenu.classList.remove('nl-dropdown-active');
        menuParentEl.classList.remove('nl-dropdown-active');
        openedMenu = false;
        menuParentEl = false;
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
        let parentEl = e.target.closest('.nl-element');
        parentEl ? null : parentEl = e.target.closest('.nl-layout');
        parentEl ? null : parentEl = e.target.closest('.nl-role');
        if (!dropdownEl) return;
        if (dropdownEl.classList.contains('nl-dropdown-active')) {
            dropdownEl.classList.remove('nl-dropdown-active');
            parentEl.classList.remove('nl-dropdown-active');
            closeLastOpened();
        } else {
            closeLastOpened();
            dropdownEl.classList.add('nl-dropdown-active');
            parentEl.classList.add('nl-dropdown-active');
            openedMenu = dropdownEl;
            menuParentEl = parentEl;
        }
    };

    document.addEventListener('click', toggleMenu, true);
};

export default dropdownInit;

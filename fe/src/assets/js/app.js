document.addEventListener("DOMContentLoaded", function() {
    "use strict";

    function qs(sel) {
        return document.querySelector(sel);
    }

    function hasClass(el, cls) {
        return el && (' ' + el.className + ' ').indexOf(' ' + cls + ' ') > -1;
    }

    function addClass(el, cls) {
        if (el && !hasClass(el, cls)) el.className += ' ' + cls;
    }

    function removeClass(el, cls) {
        if (el && hasClass(el, cls)) {
            el.className = el.className.replace(new RegExp('(\\s|^)' + cls + '(\\s|$)'), ' ').trim();
        }
    }

    function initializePerfectScrollbar(selector) {
        const container = qs(selector);
        if (container && window.PerfectScrollbar) {
            new PerfectScrollbar(container);
        }
    }

    initializePerfectScrollbar('.header-message-list');
    initializePerfectScrollbar('.header-notifications-list');

    const searchIcon = qs(".mobile-search-icon");
    const searchBar = qs(".search-bar");

    if (searchIcon && searchBar) {
        searchIcon.addEventListener("click", () => addClass(searchBar, "full-search-bar"));
    }

    const searchClose = qs(".search-close");
    if (searchClose && searchBar) {
        searchClose.addEventListener("click", () => removeClass(searchBar, "full-search-bar"));
    }

    const mobileToggleMenu = qs(".mobile-toggle-menu");
    const wrapper = qs(".wrapper");

    if (mobileToggleMenu && wrapper) {
        mobileToggleMenu.addEventListener("click", () => addClass(wrapper, "toggled"));
    }

    const toggleIcon = qs(".toggle-icon");
    if (toggleIcon && wrapper) {
        toggleIcon.addEventListener("click", function () {
            const sidebar = qs(".sidebar-wrapper");
            if (!wrapper || !sidebar) return;

            if (hasClass(wrapper, "toggled")) {
                removeClass(wrapper, "toggled");
            } else {
                addClass(wrapper, "toggled");
            }
        });
    }

    window.addEventListener("scroll", function () {
        const backBtn = qs('.back-to-top');
        if (!backBtn) return;
        backBtn.style.display = window.pageYOffset > 300 ? "block" : "none";
    });

    const backToTop = qs('.back-to-top');
    if (backToTop) {
        backToTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
    }

    const chatToggleBtn = qs(".chat-toggle-btn");
    const chatWrapper = qs(".chat-wrapper");

    if (chatToggleBtn && chatWrapper) {
        chatToggleBtn.addEventListener("click", () => chatWrapper.classList.toggle("chat-toggled"));
    }

    const chatToggleBtnMobile = qs(".chat-toggle-btn-mobile");
    if (chatToggleBtnMobile && chatWrapper) {
        chatToggleBtnMobile.addEventListener("click", () => chatWrapper.classList.remove("chat-toggled"));
    }

    const emailToggleBtn = qs(".email-toggle-btn");
    const emailWrapper = qs(".email-wrapper");

    if (emailToggleBtn && emailWrapper) {
        emailToggleBtn.addEventListener("click", () => emailWrapper.classList.toggle("email-toggled"));
    }

    const emailToggleBtnMobile = qs(".email-toggle-btn-mobile");
    if (emailToggleBtnMobile && emailWrapper) {
        emailToggleBtnMobile.addEventListener("click", () => emailWrapper.classList.remove("email-toggled"));
    }

    const composeMailBtn = qs(".compose-mail-btn");
    const composeMailPopup = qs(".compose-mail-popup");

    if (composeMailBtn && composeMailPopup) {
        composeMailBtn.addEventListener("click", () => composeMailPopup.style.display = "block");
    }

    const composeMailClose = qs(".compose-mail-close");
    if (composeMailClose && composeMailPopup) {
        composeMailClose.addEventListener("click", () => composeMailPopup.style.display = "none");
    }

    const switcherBtn = qs(".switcher-btn");
    const switcherWrapper = qs(".switcher-wrapper");

    if (switcherBtn && switcherWrapper) {
        switcherBtn.addEventListener("click", () => switcherWrapper.classList.toggle("switcher-toggled"));
    }

    const closeSwitcher = qs(".close-switcher");
    if (closeSwitcher && switcherWrapper) {
        closeSwitcher.addEventListener("click", () => switcherWrapper.classList.remove("switcher-toggled"));
    }
});

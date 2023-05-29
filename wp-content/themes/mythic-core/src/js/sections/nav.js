/**
 * Toggles header navigation on mobile
 */
function toggleHeaderNav() {
    let headerNav = $('#header-nav > div'),
        headerControlHamburger = $('#header-control-hamburger > div');
    headerControlHamburger.toggleClass('is-active');
    headerNav.slideToggle();
}

/**
 * Toggles header navigation on mobile
 */
function toggleNavSubMenus( parent ) {
    if( parent === undefined ) return;
    let subMenu = parent.find('.sub-menu');
    subMenu.slideToggle();
}
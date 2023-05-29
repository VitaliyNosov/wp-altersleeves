$(function() {

    let headerControlHamburger = $('#header-control-hamburger > div'),
        navItems = $('.nav a '),
        menuItems = $('.menu a '),
        navItemsWithSubMenu = $('.nav .menu-item-has-children ');

    // Adds hover animation to nav items
    navItems.addClass('hvr-push');
    menuItems.addClass('hvr-push');

    // Shows Sub Menus on Mobile
    navItemsWithSubMenu.click(function() {
        let subMenu = $(this).find('.sub-menu');
        subMenu.slideToggle();
    });

    // Hamburger Control
    headerControlHamburger.click(function() {
        let headerNav = $('#header-nav > div');
        headerControlHamburger.toggleClass('is-active');
        headerNav.slideToggle();
    });

});

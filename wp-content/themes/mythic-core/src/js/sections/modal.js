/**
 *
 */
function setModalFullContentHeight() {
    if( !$('div.modal-full') ) return;
    let max_height = $(window).height() - 60 - 42 - 70 - 30;
    $('div.modal-full .tab-content').css('max-height', max_height + 'px');
    $('div.modal-full #info-block').css('max-height', max_height + 'px');
}
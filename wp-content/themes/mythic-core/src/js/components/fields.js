$(function() {

    $(document).ready(function() {
        InitSelect2();
    });

    $(document).on('InitSelect2', function() {
        InitSelect2();
    });

})

function InitSelect2() {
    let Select2 = $('._select2');
    if( Select2.length ) {
        Select2.select2();
    }

    let Select2Multiple = $('._select2_multiple');
    if( Select2Multiple.length ) {
        Select2Multiple.each(function() {
            let currentElement = $(this);
            InitSelect2Multiple(currentElement, currentElement.attr('data-mc-placeholder'));
        })
    }
}

function InitSelect2Multiple( currentElement, placeholder ) {
    currentElement.select2({
        multiple: true,
        closeOnSelect: false,
        placeholder: {
            id: '',
            text: placeholder
        },
        allowClear: true
    })
        .val('')
        .trigger('change');
}
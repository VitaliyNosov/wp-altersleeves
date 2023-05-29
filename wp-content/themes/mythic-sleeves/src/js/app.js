$(document).ready(function() {

    let idArtistId = 'artist_id',
        idBrowseType = 'browse_type',
        idSearchTerm = 'search_term',
        idCardId = 'card_id',
        idCardName = 'card_name',
        idCardSearch = 'card_search',
        idFramecode = 'framecode_id',
        idPage = 'target_page',
        idPrintingId = 'printing_id',
        idSetId = 'set_id';

    $(document).on('click', '.results-chevron', function() {
        let targetPage = $(this).data('page-number');
        if( targetPage < 1 ) return;
        browseResults(targetPage);
    })

    $(document).on('change', '#filter-card-printing', function() {
        selectInput(idSetId).val($(this).val());
        browseResults();
    });

    $(document).on('change', '#input-' + idCardId, function() {
        if( !$(this).hasClass('mc_select2') ) return;
        selectInput(idCardName).val($(this).find(':selected').text());
        browseResults();
    });

    $(document).on('change', '.input-results-page-select', function() {
        browseResults($(this).val());
    })

    if ( window.location.href.search("browse_type=cards") !== -1) {
        if ( $('div').is('#results-wrapper #results') ) {
            generateResultsUrl();
        }
    }

    let lastUrl = location.href;
    new MutationObserver(() => {
      const url = location.href;
      if (url !== lastUrl) {
        lastUrl = url;
        onUrlChange();
      }
    }).observe(document, {subtree: true, childList: true});

    function onUrlChange() {
        if ( window.location.href.search("browse_type=cards") !== -1) {
            if ( $('div').is('#results-wrapper #results') ) {
                generateResultsUrl();
            }
        }
    }

    function generateResultsUrl() {
        let resultSpecifics = document.querySelectorAll('.result-link.specific');
        for (const resultSpecific of resultSpecifics.entries()) {
            buildAndPlaceUrl(resultSpecific[1]);
        };

        let resultGenerics = document.querySelectorAll('.result-link.generic');
        for (const resultGeneric of resultGenerics.entries()) {
            buildAndPlaceUrl(resultGeneric[1]);
        };
    }

    function buildAndPlaceUrl( result, targetPage ) {
        let data = {
            [idArtistId]: safeInputVal(idArtistId, 0),
            [idBrowseType]: 'designs',
            [idCardId]: $('#input-card_id').val(),
            [idCardName]: safeInputVal(idCardName, ''),
            [idCardSearch]: safeInputVal(idCardSearch, ''),
            [idFramecode]: result.getAttribute('data-framecode-id') === null ? '' : result.getAttribute('data-framecode-id'),
            [idPage]: targetPage === undefined ? 1 : targetPage,
            [idPrintingId]: result.getAttribute('data-printing-id'),
            [idSetId]: safeInputVal(idSetId, 0),
            [idSearchTerm]: safeInputVal(idSearchTerm),
            mc_nonce: getNonceValByAction('browse-results')
        };
        let url = window.location.href;

        $.each(data, function( key, value ) {
            value = isNumeric(value) ? parseInt(value) : value;
            if(
                value === 0 ||
                value === ''
            ) {
                url = removeURLParam(key, url);
                return;
            }
            url = updateURLParameter(url, key, value);
        });

        result.removeAttribute("href");
        result.setAttribute("href", url);
    }

    function browseResults( targetPage ) {
        let browseType = safeInputVal(idBrowseType);
        let data = {
            [idArtistId]: safeInputVal(idArtistId, 0),
            [idBrowseType]: browseType,
            [idCardId]: safeInputVal(idCardId, 0),
            [idCardName]: safeInputVal(idCardName, ''),
            [idCardSearch]: safeInputVal(idCardSearch, ''),
            [idFramecode]: safeInputVal(idFramecode, ''),
            [idPage]: targetPage === undefined ? 1 : targetPage,
            [idPrintingId]: safeInputVal(idPrintingId, 0),
            [idSetId]: safeInputVal(idSetId, 0),
            [idSearchTerm]: safeInputVal(idSearchTerm),
            mc_nonce: getNonceValByAction('browse-results')
        };
        let url = window.location.href;

        $.each(data, function( key, value ) {
            value = isNumeric(value) ? parseInt(value) : value;
            if(
                value === 0 ||
                value === ''
            ) {
                url = removeURLParam(key, url);
                return;
            }
            url = updateURLParameter(url, key, value);
        });

        ajaxPost('browse-results', data, function( response ) {
            let resultsWrapper = $('#results-wrapper');
            resultsWrapper.fadeOut("slow", function() {
                $(this).replaceWith(response.output);
                resultsWrapper.fadeIn("slow");
                $(document).trigger('mcInitSelect2');
            });
            updateUrl(url);
        })
    }
});

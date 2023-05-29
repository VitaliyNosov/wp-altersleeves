$(function() {
    let alreadyDownloadedFiles = {},
        lastUsedLetter = null,
        flexSearchObject = {},
        searchGroupList = {
            cards: { single: 'Card', limit: 9 },
            // sets: { single: 'Set', limit: 4 },
            artists: { single: 'Alterist', limit: 4 },
            content_creators: { single: 'Partner', limit: 4 },
            tags: { single: 'Tag', limit: 25 }
        };

    $(document).on('click', 'body', function( e ) {
        if( $(e.target).hasClass('search-input') ) return;
        $(".header .results").removeClass("results-active");
    });

    $(document).on('click', '.results', function( e ) {
        e.stopPropagation();
    });

    $('#header-search-input').keydown(function( e ) {
        let searchValue = $('#header-search-input').val();
        if( e.keyCode === 13 ) {
            if( isNaN(searchValue) ) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
    });

    $('#header-search-input').keyup(function( e ) {
        if( e.keyCode === 13 ) {
            if( $('#header-search-results ul li a').first().html() ) {
                window.location.assign($('#header-search-results ul li a').first().attr('href'));
            }
        }
    });

    $(document).on('focus', '#header-search-input', function() {
        if( $('.header .results li').length ) $(".header .results").addClass("results-active");
    });

    $(document).on('click', '.header .search .clear', function( e ) {
        e.preventDefault();
        let currentElement = $(this);
        currentElement.removeClass('search-clear-active').siblings('#header-search-input').val('');

        hideSearchResultsContainer();
    });

    $(document).on('keyup', "#header-search-input", delayKeyup(function() {
        let currentElement = $(this);
        let searchValue = currentElement.val();

        if( searchValue == '' ) {
            hideSearchResultsContainer();
            activateOrDeactivateElements(currentElement, 'search-clear', false);
            return;
        }

        searchAutocompleteJsonLoop(searchValue, currentElement);
    }, 200));

    function searchAddContent( cat, content ) {
        ( (
            flexSearchObject[cat] = new FlexSearch({
                encode: "simple",
                tokenize: "full",
                threshold: 1,
                resolution: 3,
                cache: true,
                profile: 'memory',
                doc: {
                    id: "i",
                    field: ["n", "r"],
                    sort: 'o'
                }
            })
        ) ).add(content);
    }

    function mcSearch( cat, query, limit ) {
        return flexSearchObject[cat] ?
            flexSearchObject[cat].search(
                query,
                {
                    limit: limit,
                    sort: function( a, b ) {
                        return (
                            a['o'] < b['o'] ? -1 : (
                                a['o'] > b['o'] ? 1 : 0
                            ) );
                    }
                }
            ) : [];
    }

    function searchAutocompleteJsonLoop( searchValue, searchInput ) {
        // If searchValue was changed while file with first letter is loading
        if( searchValue != searchInput.val() ) return;

        let firstLetter = searchValue.charAt(0);
        if( isNumeric(firstLetter) ) {
            firstLetter = 'a' + firstLetter;
        } else {
            firstLetter = firstLetter.toLowerCase();
        }

        if( lastUsedLetter != firstLetter && lastUsedLetter !== 0 ) {
            // If file with first letter was not loaded before
            activateOrDeactivateElements(searchInput, 'search-clear', false);
            activateOrDeactivateElements(searchInput, 'search-loader');
            lastUsedLetter = 0;
            if( typeof alreadyDownloadedFiles[firstLetter] == 'undefined' ) {
                $.getJSON('/files/search_indexing/' + firstLetter + '.json', function( data ) {
                    for( let dataKey in data ) {
                        searchAddContent(dataKey, data[dataKey]);
                    }
                    lastUsedLetter = firstLetter;
                    alreadyDownloadedFiles[firstLetter] = data;
                    activateOrDeactivateElements(searchInput, 'search-loader', false);
                    searchAutocompleteJsonLoop(searchValue, searchInput);
                }).fail(function() {
                    activateOrDeactivateElements(searchInput, 'search-loader', false);
                    lastUsedLetter = firstLetter;
                    alreadyDownloadedFiles[firstLetter] = [];
                    searchAutocompleteJson(searchValue)
                });
            } else {
                for( let dataKey in alreadyDownloadedFiles[firstLetter] ) {
                    searchAddContent(dataKey, alreadyDownloadedFiles[firstLetter][dataKey]);
                    lastUsedLetter = firstLetter;
                    activateOrDeactivateElements(searchInput, 'search-loader', false);
                    searchAutocompleteJsonLoop(searchValue, searchInput);
                }
            }

            return;
        } else if( lastUsedLetter === 0 ) {
            // If file with first letter is loading
            setTimeout(() => {
                searchAutocompleteJsonLoop(searchValue, searchInput);
            }, 200);
            return;
        }
        // If file with first letter was loaded - just search
        activateOrDeactivateElements(searchInput, 'search-clear');

        searchAutocompleteJson(searchValue)
    }

    function searchAutocompleteJson( searchValue ) {
        let results = [];
        for( let groupDataKey in searchGroupList ) {
            let searchResults = mcSearch(groupDataKey, searchValue, parseInt(searchGroupList[groupDataKey]['limit']) + 1);
            if( searchResults.length ) {
                results[groupDataKey] = searchResults
            }
        }

        checkAndFillSearchAutocompleteResults(results, searchValue);
    }

    function activateOrDeactivateElements( siblingElement, elementCLasses = null, needsActivation = true ) {
        if( elementCLasses == null ) {
            elementCLasses = ['search-loader', 'search-clear'];
            elementCLasses.forEach(function( currentClass ) {
                activateOrDeactivateElementSingle(siblingElement, currentClass, needsActivation);
            });

            return;
        }

        activateOrDeactivateElementSingle(siblingElement, elementCLasses, needsActivation);
    }

    function activateOrDeactivateElementSingle( searchInput, elementCLass, needsActivation ) {
        let currentSiblings = searchInput.siblings('.' + elementCLass);
        if( needsActivation ) {
            currentSiblings.addClass(elementCLass + '-active');
        } else {
            currentSiblings.removeClass(elementCLass + '-active');
        }
    }

    function checkAndFillSearchAutocompleteResults( response, searchValue ) {
        if( checkIfResultsAreHidden() ) {
            showSearchResultsContainer();
        } else {
            clearSearchResultsContainer();
        }

        let noAnyResult = true;

        for( let groupDataKey in searchGroupList ) {
            if( response[groupDataKey] && response[groupDataKey].length ) {
                noAnyResult = false;
                fillSearchAutocompleteUl(groupDataKey, response[groupDataKey], searchValue);
            }
        }

        if( noAnyResult ) {
            $('.results-group').hide();
            $('.header .no-results').show();
        } else {
            $('.results-group').show();
            $('.header .no-results').hide();
        }

    }

    function fillSearchAutocompleteUl( groupDataKey, groupResults, searchValue ) {
        let ulElement = $('[data-search-group="' + groupDataKey + '"]');

        groupResults.forEach(function( liElement ) {
            ulElement.append(createSearchResultsLi(searchGroupList[groupDataKey]['single'], liElement, groupDataKey));
        });

        if( groupDataKey == 'cards' && groupResults.length > searchGroupList[groupDataKey]['limit'] ) {
            ulElement.append(createShowMoreResultsLi(groupDataKey, searchValue));
        }

    }

    function createSearchResultsLi( groupSingle, LiElement, groupDataKey ) {
        let currentLink = createSearchResultsLiLink(LiElement, groupDataKey);
        if( groupSingle === 'Card' && LiElement.r === LiElement.r.toLowerCase() ) return '';
        return "<li><a href='" + currentLink + "'>" + groupSingle + ": " + LiElement.r + "</a></li>";
    }

    function createSearchResultsLiLink( LiElement, type ) {
        switch( type ) {
            case 'artists':
                return '/alterist/' + LiElement.l;
            case 'content_creators':
                return '/content-creator/' + LiElement.l;
            case 'cards':
                return '/browse?browse_type=cards&card_id=' + LiElement.i;
            case 'sets':
                return '/browse?browse_type=sets&set_id=' + LiElement.i;
        }
    }

    function createShowMoreResultsLi( groupDataKey, searchValue ) {
        return;
        return "<li class='show-more'><a href='/browse?browse_type=" + groupDataKey + "&search_term=" +
            encodeURI(searchValue) + "'>Show" + " more " + groupDataKey + "</a></li>";
    }

    function hideSearchResultsContainer() {
        if( checkIfResultsAreHidden() ) return;
        $('.header .results').removeClass('results-active');
        clearSearchResultsContainer();
    }

    function clearSearchResultsContainer() {
        $('.header .results-group').html('');
    }

    function showSearchResultsContainer() {
        $('.header .results').addClass('results-active');
    }

    function checkIfResultsAreHidden() {
        return !$('.header .results').hasClass('results-active');
    }

});

$(function () {
	let $request = null;
	let $requestFilter = null;

	$(document).on('submit', '.mc_search_form', function (e) {
		e.preventDefault();
		updateSearchResults(0, $(this));
	});

	$(document).on('click', '.pagination.as-ajax-pagination a', function (e) {
		e.preventDefault();
		let currentElement = $(this);
		updateSearchResults(currentElement.text(), currentElement);
	});

	$(document).on('change', '.as_printings_sort_form select', function () {
		updateSearchResults();
	});

	$(document).on('change', '.mc-table-flex-filter select', function (e) {
		updateSearchResults(0, $(this));
	});

	// Start of design filters actions
	$(document).on('keyup', '.as_designs_filter_input[type="text"]', function () {
		if (!$('.as_designs_filter_input').length) return;
		let currentElement = $(this);
		if (currentElement.attr('data-as-filter-id') != 0) {
			currentElement.attr('data-as-filter-id', 0);
			updateSearchResults();
		}
		if (currentElement.hasClass('as_designs_filter_input_card')) {
			let printingsSelect = $('#as_designs_filter_input_printing').addClass('as_disabled_element').html('').attr('data-as-filter-id', 0);
			addEmptyOption(printingsSelect, 3);
		}
		asGetDesignsFilterValues(currentElement);
	});

	$(document).on('click', '.as_designs_filter_options li', function () {
		let currentElement = $(this);
		let currentElementParent = currentElement.parent().removeClass('as_designs_filter_options_active');
		let currentId = currentElement.attr('data-as-filter-id');
		let currentInput = currentElementParent.siblings('.as_designs_filter_input').attr('data-as-filter-id', currentId).val(currentElement.text());
		if (currentInput.hasClass('as_designs_filter_input_card')) {
			let printingsSelect = $('#as_designs_filter_input_printing').attr('data-as-filter-id', currentId);
			asGetDesignsFilterValues(printingsSelect);
		}
		updateSearchResults();
	});

	$(document).on('change', 'input[name="as_designs_filter_radio"]', function () {
		let currentElement = $(this);
		if (currentElement.val() == 'alterists') {
			$('#as_designs_filter_input_alterist').removeClass('as_disabled_element');
			$('#as_designs_filter_input_card').addClass('as_disabled_element');
			$('#as_designs_filter_input_printing').addClass('as_disabled_element');
		} else {
			$('#as_designs_filter_input_alterist').addClass('as_disabled_element');
			$('#as_designs_filter_input_card').removeClass('as_disabled_element');
			if ($('#as_designs_filter_input_printing option').length > 1) {
				$('#as_designs_filter_input_printing').removeClass('as_disabled_element');
			}
		}
	});

	$(document).on('click', '.as_designs_filter_tags span', function () {
		$(this).toggleClass('as_designs_filter_tag_active');
		updateSearchResults();
	});

	$(document).on('click', '.as_designs_filter_toggle_title', function () {
		let currentElement = $(this);
		currentElement.toggleClass('as_designs_filter_toggle_title_active');
		if (currentElement.hasClass('as_designs_filter_toggle_title_global')) {
			$('.as_designs_filter_container').toggleClass('as_hidden');
		} else {
			$('.as_designs_filter_tags').toggleClass('as_hidden');
		}
	});

	$(document).on('change', '#as_designs_filter_input_printing', function () {
		$(this).attr('data-as-filter-id', $(this).val());
		updateSearchResults();
	});

	$(document).on('click', 'body', function (e) {
		if ($(e.target).hasClass('as_designs_filter_input')) return;
		$(".as_designs_filter_options_active").removeClass("as_designs_filter_options_active");
	});

	$(document).on('click', '.as_designs_filter_options_active', function (e) {
		e.stopPropagation();
	});

	$(document).on('focus', '.as_designs_filter_input', function () {
		let currentElement = $(this);
		let currentOptionsUl = currentElement.siblings('.as_designs_filter_options');
		if (currentOptionsUl.find('li').length) currentOptionsUl.addClass("as_designs_filter_options_active");
	});

	// End of design filters actions

	function updateSearchResults(newPageNumber = 0, currentButton = '') {
		checkAndMaybeAbortRequest();
		let currentPageType = '';
		let currentTabPane = [];
		let type = getUrlParams('type');
		if (type == null && currentButton != '') {
			let currentFinanceContainer = currentButton.closest('.mc-finance-transactions');
			if (currentFinanceContainer.length) {
				currentPageType = 'financeControl';
				type = 'financeControl';
			} else {
				currentPageType = 'affiliatesControl';
				currentTabPane = currentButton.closest('.tab-pane');
				if (currentTabPane.length) {
					if (currentTabPane.hasClass('all_affiliates')) {
						type = 'allAffiliates';
					} else if (currentTabPane.hasClass('all_promotions')) {
						type = 'allPromotions';
					} else if (currentTabPane.hasClass('edit_existing_promotion')) {
						type = 'promotionCodes';
					} else if (currentTabPane.hasClass('artist_all_shares')) {
						type = 'artistAllShares';
					} else if (currentTabPane.hasClass('not_accepted_shares')) {
						type = 'publisherNotAccepted';
					} else if (currentTabPane.hasClass('publisher_all_shares')) {
						type = 'publisherAllShares';
					}
				}
			}
		}
		if (type == null) return;

		let data = {
			action: "asSearch",
			type: type,
			currentPageType: currentPageType,
			page: newPageNumber,
			currentPageUrl: location.protocol + '//' + location.host + location.pathname,
			mc_nonce: getNonceValByAction('asSearch')
		};
		let resultsContainer = '.results.browsing-results';
		let lastUrlPart = '';
		if (currentPageType == 'affiliatesControl') {
			let hasShareFilters = 0;
			if (type == 'allAffiliates') {
				data.search = $('#all_affiliates .mc_sets_search_input').val();
				resultsContainer = '#all_affiliates .mc-table-flex-container';
			} else if (type == 'allPromotions') {
				data.search = $('#all_promotions .mc_sets_search_input').val();
				resultsContainer = '#all_promotions .mc-table-flex-container';
			} else if (type == 'promotionCodes') {
				data.search = $('#edit_existing_promotion .mc_sets_search_input').val();
				data.promotionId = $('#mc-af-edit-existing-coupons-list').val();
				resultsContainer = '#edit_existing_promotion .mc-table-flex-container';
			} else if (type == 'artistAllShares') {
				data.userId = $('#mc-prod-share-artist-id').val();
				resultsContainer = '#artist_all_shares .mc-table-flex-container';
				hasShareFilters = 1;
			} else if (type == 'publisherNotAccepted') {
				data.userId = $('#mc-prod-share-current-publisher-id').val();
				resultsContainer = '#not_accepted_shares .mc-table-flex-container';
				hasShareFilters = 1;
			} else if (type == 'publisherAllShares') {
				data.userId = $('#mc-prod-share-current-publisher-id').val();
				resultsContainer = '#publisher_all_shares .mc-table-flex-container';
				hasShareFilters = 1;
			}
			if (hasShareFilters) {
				data.skipFiltersHtml = 1;
				data.filterByProductId = currentTabPane.find('.mc-table-flex-filter-product').val();
				data.filterByPublisher = currentTabPane.find('.mc-table-flex-filter-publisher').val();
				data.filterByArtist = currentTabPane.find('.mc-table-flex-filter-artist').val();
				data.filterByStatus = currentTabPane.find('.mc-table-flex-filter-status').val();
			}
		} else if (currentPageType = 'financeControl') {
			resultsContainer = '.mc-table-flex-container.mc-finance-transactions';
			data.skipFiltersHtml = 1;
			data.filterByType = $('.mc-table-flex-filter-type').val();
			data.userId = $('.mc-table-flex-filter-user').val();
			data.filterByOrderId = $('.mc-table-flex-filter-order').val();
			data.filterByProductId = $('.mc-table-flex-filter-product').val();
		} else {
			if (type == 'cards' || type == 'sets') {
				data.search = getUrlParams('search');
				lastUrlPart = data.search;
			} else if (type == 'card') {
				data.element_id = getUrlParams('card_id');
				data.order = $('.as_printings_order_select').val();
				data.orderby = $('.as_printings_order_by_select').val();
			} else if (type == 'set') {
				data.element_id = getUrlParams('set_id');
				data.search = $('.mc_sets_search_input').val()
			} else if (type == 'designs') {
				resultsContainer = '.as_designs_search_results_container';
				data.form_data = {};
				let activeFilters = '';
				let cardInput = $('#as_designs_filter_input_card');
				let printingInput = $('#as_designs_filter_input_printing');
				let alteristInput = $('#as_designs_filter_input_alterist');
				let tagsElements = $('.as_designs_filter_tag_active');
				if (!cardInput.hasClass('as_disabled_element')) {
					data.form_data.card_id = cardInput.attr('data-as-filter-id');
					if (data.form_data.card_id != 0) {
						activeFilters = 'Filtered by Card: ' + cardInput.val() + '. ';
						lastUrlPart = '&card_id=' + data.form_data.card_id;
					}
					if (!printingInput.hasClass('as_disabled_element')) {
						data.form_data.printing_id = printingInput.attr('data-as-filter-id');
						if (data.form_data.printing_id != 0) {
							activeFilters += 'Filtered by Printing: ' + $('#as_designs_filter_input_printing option[value="' + data.form_data.printing_id + '"]') + '. ';
							lastUrlPart += '&set_id=' + data.form_data.printing_id;
						}
					}
				} else {
					data.form_data.alterist_id = alteristInput.attr('data-as-filter-id');
					lastUrlPart = '&alterist_id=' + data.form_data.alterist_id;
					activeFilters = 'Filtered by Alterist: ' + $('.as_designs_filter_options_alterists li[data-as-filter-id="' + data.form_data.alterist_id + '"]').text();
				}
				if (tagsElements.length) {
					activeFilters += 'Filtered by Tags: ';
					data.form_data.tags = [];
					tagsElements.each(function (index) {
						data.form_data.tags[index] = $(this).attr('data-as-filter-id');
						lastUrlPart += '&tag_id[]=' + data.form_data.tags[index];
						if (index != 0) {
							activeFilters += ', ';
						}
						activeFilters += $(this).text();
					})
				}
				if (activeFilters == '') {
					activeFilters = 'No active filters';
				}
				$('.as_designs_filter_active_filters').text(activeFilters);
			}
		}

		if (currentPageType == '') {
			generateAndUpdateLink(type, newPageNumber, lastUrlPart);
		}

		let resultsContainerElement = $(resultsContainer);
		resultsContainerElement.addClass('as_disabled_element');
		$request = $.ajax({
			type: "post",
			dataType: "json",
			url: vars.ajaxurl,
			data: data,
			success: function (response) {
				if (!response || !response.data) return;

				resultsContainerElement.replaceWith(response.data)
			}
		});
	}

	function generateAndUpdateLink(type, newPageNumber, lastUrlPart = '') {
		let newUrl = '/browse';

		if (newPageNumber == 0) newPageNumber = getUrlParams('page');
		if (newPageNumber != null) {
			newUrl += '/page/' + newPageNumber;
		}

		if (type == 'cards' || type == 'sets') {
			newUrl += '?type=' + type;
			if (lastUrlPart != '' && lastUrlPart != null) {
				newUrl += '&search=' + lastUrlPart;
			}
		} else if (type == 'set') {
			let additionalSearchInput = $('.mc_sets_search_input');
			let set_id = getUrlParams('set_id');

			newUrl += '?type=set&set_id=' + set_id;

			if (additionalSearchInput.length) {
				let search = encodeURI(additionalSearchInput.val());
				if (search != '') newUrl += '&search=' + search;
			}
		} else if (type == 'card') {
			let orderBy = $('.as_printings_order_by_select').val();
			let order = $('.as_printings_order_select').val();
			let card_id = getUrlParams('card_id');

			newUrl += '?type=card&card_id=' + card_id + '&orderby=' + orderBy + '&order=' + order;
		} else if (type == 'designs') {
			newUrl += '?type=designs';
			newUrl += lastUrlPart;
		}

		window.history.pushState('', '', newUrl);
	}

	function generateAndUpdateAffiliateLink(type, newPageNumber, lastUrlPart = '') {
		let newUrl = '/as_affiliates_control';

		if (newPageNumber == 0) newPageNumber = getUrlParams('page');
		if (newPageNumber != null) {
			newUrl += '/page/' + newPageNumber;
		}

		newUrl += lastUrlPart;

		let additionalSearchInput = $('.mc_sets_search_input');
		if (additionalSearchInput.length) {
			let search = encodeURI(additionalSearchInput.val());
			if (search != '') newUrl += '&search=' + search;
		}

		window.history.pushState('', '', newUrl);
	}

	function getUrlParams(parameter) {
		var url_string = window.location.href;
		var url = new URL(url_string);
		return url.searchParams.get(parameter);
	}

	function checkAndMaybeAbortRequest() {
		if ($request != null) {
			$request.abort();
			$request = null;
		}
	}

	function checkAndMaybeAbortRequestFilter() {
		if ($requestFilter != null) {
			$requestFilter.abort();
			$requestFilter = null;
		}
		$('.sa-loader-animation-active').removeClass('sa-loader-animation-active');
	}

	function asGetDesignsFilterValues(currentInput) {
		checkAndMaybeAbortRequestFilter();
		let searchValue = currentInput.val();
		let searchType = 'cards';
		let currentLoader = currentInput.siblings('.sa-loader-animation').addClass('sa-loader-animation-active');

		if (currentInput.hasClass('as_designs_filter_input_alterist')) {
			searchType = 'alterists';
		} else if (currentInput.hasClass('as_designs_filter_input_printing')) {
			searchType = 'printings';
		}

		let data = {
			action: "designsAutocompleteFilterValues",
			type: searchType
		};

		if (searchType == 'printings') {
			data.search_term = currentInput.attr('data-as-filter-id')
		} else {
			data.search_term = JSON.stringify(searchValue);
		}

		$requestFilter = $.ajax({
			type: "post",
			dataType: "json",
			url: vars.ajaxurl,
			data: data,
			success: function (response) {
				if (!response) return;
				currentLoader.removeClass('sa-loader-animation-active');
				if (searchType == 'printings') {
					fillPrintingsSelect(response.data, currentInput);
				} else {
					checkAndFillSearchAutocompleteResultsFilter(response.data, currentInput);
				}
			}
		});
	}

	function fillPrintingsSelect(resultData, currentInput) {
		currentInput.html('');
		if (!resultData.length) {
			addEmptyOption(currentInput, 2);
			return;
		}

		addEmptyOption(currentInput);
		resultData.forEach(function (singleResult) {
			addSearchResultsOption(singleResult, currentInput);
		});
	}

	function addSearchResultsOption(singleResult, currentResultsContainer) {
		currentResultsContainer.append("<option value='" + singleResult.id + "'>" + singleResult.name + "</option>");
	}

	function addEmptyOption(currentResultsContainer, optionType = 1) {
		let currentText = 'Select Printing';
		currentResultsContainer.removeClass('as_disabled_element');

		if (optionType == 2) {
			currentText = 'There is no printings';
			currentResultsContainer.addClass('as_disabled_element');
		} else if (optionType == 3) {
			currentText = 'Search for a card';
			currentResultsContainer.addClass('as_disabled_element');
		}

		currentResultsContainer.append("<option value='0'>" + currentText + "</option>");
	}

	function checkAndFillSearchAutocompleteResultsFilter(resultData, currentInput) {
		let currentResultsContainer = currentInput.siblings('.as_designs_filter_options').html('').addClass('as_designs_filter_options_active');
		if (!resultData.length) {
			addNoResultsText(currentResultsContainer);
			return;
		}

		resultData.forEach(function (singleResult) {
			addSearchResultsLi(singleResult, currentResultsContainer);
		});
	}

	function addSearchResultsLi(singleResult, currentResultsContainer) {
		currentResultsContainer.append("<li data-as-filter-id='" + singleResult.id + "'>" + singleResult.name + "</li>");
	}

	function addNoResultsText(currentResultsContainer) {
		currentResultsContainer.append("<li>Nothing was found for your request</li>");
	}
});
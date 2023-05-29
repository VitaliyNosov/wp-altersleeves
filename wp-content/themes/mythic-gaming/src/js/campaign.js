$(document).ready(function() {

    let backerId = campaign.backer_id.length ? campaign.backer_id : 0,
        campaignGroup,
        campaignSetCredits,
        campaignNonCreatureCredits,
        campaignCreatureCredits,
        campaignLandCredits,
        campaignSuperLandCredits,
        creditType,
        numberSelector,
        numberValue,
        availableCreditsSet = campaign.set_credits.available,
        availableCreditsPack = campaign.pack_credits.available,
        availableCreditsLand = campaign.land_credits.available,
        initCreditsSet = campaign.set_credits.init,
        initCreditsPack = campaign.pack_credits.init,
        initCreditsLand = campaign.land_credits.init,
        spentCreditsSet = campaign.set_credits.spent,
        spentCreditsPack = campaign.pack_credits.spent,
        spentCreditsLand = campaign.land_credits.spent,
        teamPledged = {
            1: !!campaign.spent[1].timestamp.length,
            2: !!campaign.spent[2].timestamp.length,
            3: !!campaign.spent[3].timestamp.length,
            4: !!campaign.spent[4].timestamp.length,
            5: !!campaign.spent[5].timestamp.length,
            6: !!campaign.spent[6].timestamp.length,
            7: !!campaign.spent[7].timestamp.length,
            8: !!campaign.spent[8].timestamp.length,
            9: !!campaign.spent[9].timestamp.length,
            10: !!campaign.spent[10].timestamp.length,
            11: !!campaign.spent[11].timestamp.length,
            12: !!campaign.spent[12].timestamp.length,
        };

    function setAvailableSetCredits( value = 0 ) {
        let available = availableCreditsSet - value;
        available = available < 0 ? 0 : available;
        availableCreditsSet = available;
        $('.user-remaining-set-credits').text(availableCreditsSet);
    }

    function setAvailablePackCredits( value = 0 ) {
        let available = availableCreditsPack - value;
        available = available < 0 ? 0 : available;
        availableCreditsPack = available;
        $('.user-remaining-pack-credits').text(availableCreditsPack);
    }

    function setAvailableLandCredits( value = 0 ) {
        let available = availableCreditsLand - value
        available = available < 0 ? 0 : available;
        availableCreditsLand = available;
        $('.user-remaining-super-land-credits').text(availableCreditsLand);
    }

    $(document).on('click', '.action.plus', function() {

        campaignGroup = $(this).data('campaign-group');
        creditType = $(this).data('credit-type');
        numberSelector = $('#input-' + creditType + '-quantity-' + campaignGroup);
        if( !numberSelector.length ) return;
        numberValue = numberSelector.val();
        numberValue = parseInt(numberValue) + 1;
        if( numberValue > numberSelector.attr('max') ) return;
console.log(1);
        // Set
        if( creditType === 'set' ) {
            $('#pack-notice-' + campaignGroup).fadeOut();
            if( availableCreditsSet <= 0 ) return;
            setAvailableSetCredits(1);
        }

        // Pack
        else if( creditType === 'creature' || creditType === 'non-creature' || creditType === 'land' ) {
            if( availableCreditsPack <= 0 ) return;
            setAvailablePackCredits(1);
        }

        // Land
        if( creditType === 'super-land' ) {
            $('#pack-notice-' + campaignGroup).fadeOut();
            if( availableCreditsLand <= 0 ) return;
            setAvailableLandCredits(1);
        }

        numberSelector.val(numberValue);

    });

    $(document).on('click', '.action.minus', function() {
        campaignGroup = $(this).data('campaign-group');
        creditType = $(this).data('credit-type');
        numberSelector = $('#input-' + creditType + '-quantity-' + campaignGroup);
        if( !numberSelector.length ) return;
        numberValue = numberSelector.val();
        numberValue = parseInt(numberValue) - 1;
        numberValue = parseInt(numberValue);
        if( numberValue < 0 || numberValue < numberSelector.attr('min') ) return;

        // Set
        if( creditType === 'set' ) {
            setAvailableSetCredits(-1);
        }

        // Pack
        else if( creditType === 'creature' || creditType === 'non-creature' || creditType === 'land' ) {
            setAvailablePackCredits(-1);
        }

        // Land
        if( creditType === 'super-land' ) {
            setAvailableLandCredits(-1);
        }

        numberSelector.val(numberValue);
    });

    $(document).on('click', '.use-credits', function() {
        campaignGroup = $(this).data('campaign-group');

        setCampaignGroupAllCredits();

        if( campaignSetCredits <= 0 && campaignNonCreatureCredits <= 0 && campaignCreatureCredits <= 0 && campaignLandCredits <= 0 && campaignSuperLandCredits <= 0 ) return;

        $('#confirm-credits').attr('data-campaign-group', campaignGroup);
        $('#creditModal').modal('show');
    });

    function setCampaignGroupAllCredits() {
        campaignSetCredits = $('#input-set-quantity-' + campaignGroup).length ? $('#input-set-quantity-' + campaignGroup).val() : 0;
        campaignNonCreatureCredits = $('#input-non-creature-quantity-' + campaignGroup).length ? $('#input-non-creature-quantity-' + campaignGroup).val() : 0;
        campaignCreatureCredits = $('#input-creature-quantity-' + campaignGroup).length ? $('#input-creature-quantity-' + campaignGroup).val() : 0;
        campaignLandCredits = $('#input-land-quantity-' + campaignGroup).length ? $('#input-land-quantity-' + campaignGroup).val() : 0;
        campaignSuperLandCredits = $('#input-super-land-quantity-' + campaignGroup).length ? $('#input-super-land-quantity-' + campaignGroup).val() : 0;
    }

    $(document).on('click', '#confirm-credits', function() {
        campaignGroup = $(this).attr('data-campaign-group');
        if( teamPledged[campaignGroup] ) return;
        $('#confirm-credits').hide();
        $('#allocating-credits').show();
        setCampaignGroupAllCredits();

        let data = {
            'backer_id': backerId,
            'team_id': campaignGroup,
            'set': campaignSetCredits,
            'non-creature': campaignNonCreatureCredits,
            'creature': campaignCreatureCredits,
            'land': campaignLandCredits,
            'super-land': campaignSuperLandCredits
        };
        ajaxPost('allocate-campaign-credits', data, function( response ) {
            $('#confirm-credits').show();
            $('#allocating-credits').hide();
            console.log(response);
            if( response.status === 0 ) {
                return;
            }
            console.log(response);
            teamPledged[campaignGroup] = true;

            spentCreditsSet = response.spent.set;
            spentCreditsPack = response.spent.pack;
            spentCreditsLand = response.spent.land;

            // Update numbers for campaign
            let prexistingCampaignSetCredits = $('#campaign-group-credits-' + campaignGroup).text();
            let newCredits = parseInt(prexistingCampaignSetCredits) + parseInt(campaignSetCredits);
            $('.campaign-group-credits-' + campaignGroup).text(newCredits)

            let barPercentage = parseInt(newCredits) / 2,
                remainingPerctenage = 100 - parseInt(barPercentage);
            if( barPercentage > 100 ) {
                barPercentage = 100;
                remainingPerctenage = 0;
            }
            $('#pledge-bar-' + campaignGroup).css('background', 'linear-gradient(to right, #FC6A29 0% ' + barPercentage + '%, #f7f7f7 ' + barPercentage + '%  100%)');

            // Change appearance
            $('#field-set-quantity-' + campaignGroup).addClass('allocated');
            $('#field-non-creature-quantity-' + campaignGroup).addClass('allocated');
            $('#field-creature-quantity-' + campaignGroup).addClass('allocated');
            $('#field-land-quantity-' + campaignGroup).addClass('allocated');
            $('#field-super-land-quantity-' + campaignGroup).addClass('allocated');
            $('.credits-allocated-' + campaignGroup).fadeIn();
            $('#creditModal').modal('toggle');

            if( campaignSetCredits > 0 ) $('#spent-set-credits-' + campaignGroup).text(campaignSetCredits);
            if( campaignNonCreatureCredits > 0 ) $('#spent-non-creature-credits-' + campaignGroup).text(campaignNonCreatureCredits);
            if( campaignCreatureCredits > 0 ) $('#spent-creature-credits-' + campaignGroup).text(campaignCreatureCredits);
            if( campaignLandCredits > 0 ) $('#spent-land-credits-' + campaignGroup).text(campaignLandCredits);
            if( campaignSuperLandCredits > 0 ) $('#spent-super-land-credits-' + campaignGroup).text(campaignSuperLandCredits);
        });

    })

    $(document).on('click', '.reset-credits', function() {
        $(".quantity input[type='number']").each(function() {
            if( $(this).attr('min') !== undefined ) {
                $(this).val($(this).attr('min'));
            } else {
                $(this).val(0)
            }
        });
        availableCreditsSet = initCreditsSet - spentCreditsSet;
        availableCreditsPack = initCreditsPack - spentCreditsPack;
        availableCreditsLand = initCreditsLand - spentCreditsLand;
        $('.user-remaining-set-credits').text(availableCreditsSet);
        $('.user-remaining-pack-credits').text(availableCreditsPack);
        $('.user-remaining-super-land-credits').text(availableCreditsLand);
    })

});


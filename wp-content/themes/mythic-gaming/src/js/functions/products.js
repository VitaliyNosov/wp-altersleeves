let teamId = findGetParameter('attribute_pa_design-team');

function displayPreorderTeam(event, variation) {
    if( event === undefined || variation === undefined ) return;
    teamId = variation.attributes['attribute_pa_design-team'];
    $('.preorder-products-team-row').hide();
    $('#preorder-products-team-'+teamId).show();
    $('.mf-team-description').hide();
    $('#mf-team-description-'+teamId).show();
}
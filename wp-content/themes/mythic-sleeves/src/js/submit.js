$(document).ready(function() {
    if( typeof getCroppingType === "function" ) getCroppingType();
    if( typeof setModalFullContentHeight === "function" ) setModalFullContentHeight();

    /** Info texts **/
    let class_has_info = '.has-info',
        class_is_info = '.is-info',
        selector_has_info = $(class_has_info);

    selector_has_info.hover(function() {
        let _ = $(this),
            info_id;
        if( _.data('info-id') ) {
            info_id = _.data('info-id');
            $(class_is_info + ':not(#' + info_id + ')').addClass('d-none');
            $('#' + info_id).removeClass('d-none');
        }
    });

});

/** Field Resets **/
function check_assign_tab() {
    let ok = document.getElementById("assign_submit_name").value !== "";
    ok = ok && document.getElementById("assign_submit_crop_type").value !== "";
    return ok;
}

function check_select_tab() {
    return document.getElementById("assign_visible_printings").value !== "";
}

function check_cutter_tab() {
    return $(".config_resultPNGDataInput").val();
}

function resetFields_assign_tab() {
    //clear result
    let dsr = document.getElementById("design_search_results").lastElementChild;
    while( dsr.firstChild ) {
        dsr.removeChild(dsr.lastChild);
    }
    let radios = document.getElementById("assign_submit_availablity").nextElementSibling.querySelectorAll("input[type='radio']");

    for( let i = 0; i < radios.length; i++ ) {
        radios[i].checked = false;
    }
    document.getElementById("designSearch").value = "";
    document.getElementById("assign_design_id").value = 0;
    document.getElementById("assign_submit_name").value = "";
    document.getElementById("assign_submit_crop_type").selectedIndex = 0;
    document.getElementById("assign_submit_crop_type").dispatchEvent(new Event('change'));
    document.getElementById("assign_submit_design_generic").checked = false;

}

/** Tab Selection **/
if( $('.submit-tab').length ) {

    var next_submit = document.getElementById("next_submit");
    var prev_submit = document.getElementById("prev_submit");
    var active_tab = document.querySelector('#alter_submit_tabs .active');

    next_submit.disabled = active_tab.parentElement.nextElementSibling === null;
    prev_submit.disabled = active_tab.parentElement.previousElementSibling === null;

    next_submit.addEventListener("click", function() {
        showLoading();
        nextClicked();
    });

    function processNext() {
        if( active_tab.id === 'assign_tab' && check_assign_tab() === false ) {
            alert("Please fill out all fields");
            hideLoading();
            return false;
        }
        if( active_tab.id === 'select_tab' && check_select_tab() === false ) {
            alert("Please select at least one printing");
            hideLoading();
            return false;
        }

        if( active_tab.id === 'cutter_tab' && check_cutter_tab() === false ) {
            alert("Please upload a file");
            hideLoading();
            return false;
        }
        if( active_tab.parentElement.nextElementSibling ) {
            let next_tab = active_tab.parentElement.nextElementSibling.firstElementChild;
            if( next_tab.id === 'cutter2_tab' && !document.getElementById('upload_submit_file_crop').checked ) {
                next_tab = active_tab.parentElement.nextElementSibling.nextElementSibling.firstElementChild;
            }
            next_tab.classList.remove('disabled');
            $("#" + next_tab.id).tab('show');
            active_tab.parentElement.firstElementChild.classList.add('disabled');
            //set new active tab
            active_tab = document.querySelector('#alter_submit_tabs .active');
        }
        next_submit.disabled = active_tab.parentElement.nextElementSibling === null;
        prev_submit.disabled = active_tab.parentElement.previousElementSibling === null;
        hideLoading();
    }

    prev_submit.addEventListener("click", function() {
        if( active_tab.parentElement.previousElementSibling ) {
            let prev_tab = active_tab.parentElement.previousElementSibling.firstElementChild;
            if( prev_tab.id === 'cutter2_tab' && !document.getElementById('upload_submit_file_crop').checked ) {
                prev_tab = active_tab.parentElement.previousElementSibling.previousElementSibling.firstElementChild;
            }
            prev_tab.classList.remove('disabled');
            $("#" + prev_tab.id).tab('show');
            active_tab.parentElement.firstElementChild.classList.add('disabled');
            //set new active tab
            active_tab = document.querySelector('#alter_submit_tabs .active');
        }
        next_submit.disabled = active_tab.parentElement.nextElementSibling === null;
        prev_submit.disabled = active_tab.parentElement.previousElementSibling === null;
    });

    /** Tab 1 - Design setup **/
    let section_design_config   = document.getElementById("submit_design_config"),
        section_design_search   = document.getElementById("submit_design_search"),
        section_design_ids      = document.getElementById("display_design_ids"),
        section_design_imgs     = document.getElementById("design_search_results");

    $('input[type=radio][name=submit_setup_design]').change(function() {

        switch( this.value ) {
            case 'new' :
                section_design_config.classList.remove("d-none");
                section_design_search.classList.add("d-none");
                section_design_ids.classList.add("d-none");
                section_design_imgs.classList.add('d-none');
                resetFields_assign_tab();
                break;
            case 'add' :
                section_design_search.classList.remove("d-none");
                section_design_config.classList.add("d-none");
                break;
        }

    });

    /** Tab 1 - Design Search **/
    const designSearch = document.getElementById("designSearch");
    designSearch.addEventListener("keyup", function( event ) {
        if( event.keyCode === 13 ) {
            event.preventDefault();
            if( !document.getElementById("trigger_design_search").disabled ) {
                document.getElementById("trigger_design_search").click();
            }
        }
    });
    document.getElementById("trigger_design_search").onclick = function( event ) {
        event.target.disabled = false;
        searchForDesigns(designSearch.value);
    }

    /** Tab 1 - Design Search Results **/
    function searchForDesigns_response( res ) {
        document.getElementById("trigger_design_search").disabled = false;
        if( res.status === 1 ) {
            //clear result
            let dsr = document.getElementById("design_search_results").lastElementChild;
            while( dsr.firstChild ) {
                dsr.removeChild(dsr.lastChild);
            }
            if( res.results.length !== 0 ) {
                for( let i = 0; i < res.results.length; i++ ) {
                    let obj = res.results[i];
                    let div = document.createElement("div");
                    div.className = "col-auto";
                    let figure = document.createElement("figure");
                    div.appendChild(figure);
                    figure.className = "figure d-table px-2";
                    let img = document.createElement("img");
                    img.className = "figure-img card-display mb-2";
                    img.src = obj.preview;
                    img.style.width = "150px";
                    img.style.cursor = "pointer";
                    img.setAttribute("data-id", obj.id);
                    img.setAttribute("data-generic", obj.generic);
                    img.setAttribute("data-name", obj.name);
                    img.setAttribute("title", obj.name);
                    img.addEventListener("click", function( event ) {

                        const imgs = document.getElementById("design_search_results").getElementsByTagName("img");
                        for( let i = 0; i < imgs.length; i++ ) {
                            imgs[i].classList.remove("select-glow");
                        }
                        event.target.classList.add("select-glow");
                        document.getElementById("assign_design_id").value = event.target.getAttribute("data-id");
                        document.getElementById("assign_submit_name").value = event.target.getAttribute("data-name");

                        section_design_config.classList.remove("d-none");
                        section_design_ids.classList.remove("d-none");

                        //scroll down
                        setTimeout(function() {
                            section_design_config.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 0);

                        document.getElementById("assign_submit_design_generic").checked = event.target.getAttribute("data-generic") === 'true';

                    }, false);
                    figure.appendChild(img);
                    let figcaption = document.createElement("figcaption");
                    figcaption.className = "px-2 figure-caption";
                    figcaption.innerHTML = obj.id;
                    figure.appendChild(figcaption);
                    dsr.appendChild(div);
                }
                document.getElementById("design_search_results").classList.remove('d-none');
            }
        }
    }

    /** Tab 1 - Crop Type **/
    function getCroppingType_response( res ) {
        if( res.status === 1 ) {
            const assign_submit_crop_type = document.getElementById("assign_submit_crop_type");
            let option = document.createElement("option");
            option.text = '--- Please select crop type ---';
            option.value = "";
            option.setAttribute("data-generic", 0);
            assign_submit_crop_type.appendChild(option);
            for( let i = 0; i < res.crop_types.length; i++ ) {
                let crop_type = res.crop_types[i];
                option = document.createElement("option");
                option.text = crop_type.name;
                option.value = crop_type.id;
                option.setAttribute("data-generic", crop_type.generic);
                option.setAttribute("data-generic-selected", crop_type.generic_selected);
                option.setAttribute("data-transferable", crop_type.transferable);
                if( crop_type.selected === 1 ) {
                    option.setAttribute('selected', 'selected');
                }
                assign_submit_crop_type.appendChild(option);
            }
        }
    }

    /** Tab 1 - Design Generic */
    document.getElementById("assign_submit_crop_type").onchange = function( event ) {
        const asag = document.getElementById('assign_submit_design_generic');
        if( event.target.selectedIndex !== -1 ) {
            asag.disabled = event.target.selectedOptions[0].getAttribute('data-generic') != 1;
            if( asag.disabled && event.target.selectedOptions[0].getAttribute('data-generic-selected') == 1 ) {
                asag.checked = true
            } else {
                asag.checked = false;
            }
        }
    };

    /** Tab 2 - Card Search **/
    const sscs = document.getElementById("select_submit_card_search_txt");
    sscs.addEventListener("keyup", function( event ) {
        if( event.keyCode === 13 ) {
            event.preventDefault();
            if( !document.getElementById("select_submit_card_search_btn").disabled ) {
                document.getElementById("select_submit_card_search_btn").click();
            }
        }
    });
    document.getElementById("select_submit_card_search_btn").onclick = function( event ) {
        searchForCard(sscs.value);
    };

    /** Tab 2 - Card Search Results **/
    function searchForCard_response( res ) {
        const sscsr = document.querySelector("#select_submit_card_search_res nav");
        //remove results
        $('#select_submit_card_search_nav').empty();
        $('#select_submit_card_printings_results').addClass('d-none');
        $('#select_submit_card_printings_results div').empty();
        document.getElementById("assign_printings").value = '';
        document.getElementById("assign_visible_printings").value = '';
        document.getElementById("select_submit_card_selected").classList.add("d-none");
        if( res.status === 0 )
            return;

        if( res.results.length === 1 ) {
            document.getElementById("select_submit_card_selected").classList.remove("d-none");
            document.getElementById("select_submit_card_search_nav").classList.add("d-inline-block");

            let obj = res.results[0];
            let li = document.createElement("li");
            li.className = "nav-item position-relative d-inline-flex align-items-center";
            sscsr.appendChild(li);
            let ahref = document.createElement("a");
            ahref.className = "nav-link";
            ahref.innerHTML = obj.name;
            ahref.href = "#";
            ahref.href = "javascript:void(0)";
            ahref.setAttribute("data-id", obj.id);
            li.appendChild(ahref);
            getPrintingsForCard(obj.id);
        } else {
            for( let i = 0; i < res.results.length; i++ ) {
                let obj = res.results[i];
                let li = document.createElement("li");
                li.className = "nav-item position-relative d-inline-flex align-items-center";
                sscsr.appendChild(li);
                let ahref = document.createElement("a");
                ahref.className = "nav-link";
                ahref.innerHTML = obj.name;
                ahref.href = "#";
                ahref.setAttribute("data-id", obj.id);
                li.appendChild(ahref);
                ahref.addEventListener("click", function( event ) {
                    if( $(event.target).parent().hasClass('selected-card') )
                        return;
                    //remove printings
                    const sscpr = document.querySelector("#select_submit_card_printings_results div");
                    while( sscpr.firstChild ) {
                        sscpr.removeChild(sscpr.lastChild);
                    }
                    sscpr.parentElement.classList.add("d-none");
                    //hide all other results
                    const li = sscsr.querySelectorAll("li");
                    for( let i = 0; i < li.length; i++ ) {
                        li[i].classList.add("d-none");
                        li[i].classList.remove("d-inline-flex");
                    }
                    event.target.parentElement.classList.remove("d-none");
                    event.target.parentElement.classList.add("d-inline-flex");
                    event.target.parentElement.classList.add("selected-card");

                    //create close button
                    let span = document.createElement("span");
                    span.className = "text-danger font-weight-bold";
                    span.innerHTML = "x";
                    span.style.cursor = "pointer";
                    document.getElementById("select_submit_card_selected").classList.remove("d-none");
                    document.getElementById("select_submit_card_search_nav").classList.add("d-inline-block");
                    span.addEventListener("click", function( event ) {
                        const li = sscsr.querySelectorAll("li");
                        for( let i = 0; i < li.length; i++ ) {
                            li[i].classList.remove("d-none");
                            li[i].classList.remove("selected-card");
                            li[i].classList.remove("active");
                        }
                        //remove printings
                        const sscpr = document.querySelector("#select_submit_card_printings_results div");
                        while( sscpr.firstChild ) {
                            sscpr.removeChild(sscpr.lastChild);
                        }
                        sscpr.parentElement.classList.add("d-none");
                        //remove x
                        event.target.remove();
                        document.getElementById("select_submit_card_selected").classList.add("d-none");
                        document.getElementById("select_submit_card_search_nav").classList.remove("d-inline-block");
                        document.getElementById("assign_printings").value = '';
                        document.getElementById("assign_visible_printings").value = '';
                    }, false);
                    event.target.parentElement.appendChild(span);
                    //show printings
                    showLoading();
                    getPrintingsForCard(event.target.getAttribute("data-id"));
                }, false);
            }
        }
    }

    function getPrintingsForCard_response( res ) {

        const sscpr = document.querySelector("#select_submit_card_printings_results div");
        if( res.status === 1 ) {

            for( let i = 0; i < res.printings.length; i++ ) {
                let obj = res.printings[i],
                    is_land = obj.land;
                let div = document.createElement("div");
                div.className = is_land ? "col-6 col-md-4" : "col-auto";
                let figure = document.createElement("figure");
                figure.style.cursor = "pointer";
                if( !is_land )
                    figure.style.width = '150px';
                div.appendChild(figure);
                figure.className = "figure text-center px-2 position-relative mb-3";
                figure.setAttribute("data-id", obj.id);
                let img = document.createElement("img"),
                    className = "figure-img card-display mb-2";
                if( is_land )
                    className = className + " submit-img-land";
                img.className = className;
                img.style.width = "120px";
                img.src = obj.img;
                img.setAttribute("data-id", obj.id);
                img.setAttribute("data-framecode", obj.framecode);
                img.setAttribute("data-name", obj.name);
                img.setAttribute("title", obj.name);

                figure.addEventListener("click", function( event ) {
                    let _ = $(this).find(".card-display"),
                        framecode,
                        id,
                        printing_id = parseInt($(this).data('id')),
                        printings = document.getElementById("assign_printings").value,
                        master_framecode = parseInt(document.getElementById("assign_framecode").value),
                        selected,
                        transferable = $('#assign_submit_crop_type').find(':selected').data('transferable'),
                        figure = $(this).find(".figure-caption");

                    if( transferable === 1 ) {
                        printings = [];
                        document.getElementById("assign_printings").value = '';
                        document.getElementById("assign_visible_printings").value = '';
                        master_framecode = parseInt(event.target.getAttribute("data-framecode"))
                        $('img[data-framecode]').each(function( i, obj ) {
                            framecode = parseInt($(this).data('framecode'));
                            id = $(this).data('id');
                            if( framecode === master_framecode ) {
                                $(this).addClass('select-glow');
                                printings.push(id);
                            } else {
                                $(this).removeClass('select-glow');
                            }
                        });
                    } else {
                        printings = printings.length !== 0 ? JSON.parse(printings) : [];
                        selected = $('.select-glow');
                        // If first selected
                        if( selected.length === 0 ) {
                            master_framecode = _.data('framecode');
                            document.getElementById("assign_framecode").value = master_framecode;
                            _.addClass('select-glow');
                            figure.addClass('fw-bold');
                            printings = [printing_id];
                            $('img[data-framecode]').each(function( i, obj ) {
                                framecode = parseInt($(this).data('framecode'));
                                if( framecode !== master_framecode ) {
                                    $(this).addClass('select-grey');
                                    $(this).addClass('disabled');
                                }
                            });
                        }
                        // After first
                        else {
                            if( !_.hasClass('disabled') ) {
                                if( _.hasClass("select-glow") ) {
                                    _.removeClass('select-glow');
                                    figure.removeClass('fw-bold');
                                    const index = printings.indexOf(printing_id);
                                    if( index > -1 ) {
                                        printings.splice(index, 1);
                                    }
                                } else {
                                    _.addClass('select-glow');
                                    figure.addClass('fw-bold');
                                    printings.push(printing_id);
                                }
                            }
                        }

                        selected = $('.select-glow');
                        if( selected.length === 0 ) {
                            $('img[data-framecode]').each(function() {
                                $(this).removeClass('select-grey');
                                $(this).removeClass('disabled');
                                $(this).removeClass('select-glow');
                            });
                        }

                    }

                    document.getElementById("assign_printings").value = JSON.stringify(printings);
                    document.getElementById("assign_visible_printings").value = printings.toString();
                }, false);

                figure.appendChild(img);
                let figcaption = document.createElement("div");
                figcaption.className = "figure-caption";
                figcaption.innerHTML = obj.name;
                figure.appendChild(figcaption);
                sscpr.appendChild(div);
            }
            sscpr.parentElement.classList.remove("d-none");
            if( res.printings.length === 1 ) {
                let result = $('#select_submit_card_printings_results figure img');
                result.click();
                let printings = [result.data('id')];
                document.getElementById("assign_printings").value = JSON.stringify(printings);
                document.getElementById("assign_visible_printings").value = printings.toString();

            }
        }

    }

    function showLoading() {
        let loadingIcon = $('.loading-icon');
        loadingIcon.show();
    }

    function hideLoading() {
        let loadingIcon = $('.loading-icon');
        loadingIcon.hide();
    }

    function callLoadExistingDesign(designUrl, intervalTimer) {
        if ( tool && tool.loadExistingDesign && designUrl ) {
            clearInterval(intervalTimer);
            tool.loadExistingDesign(designUrl);
        }
    }

    /** Ajax functions **/

    function nextClicked() {

        let tab = $('.submit-tab.active'),
            tabCurrent = tab.length ? tab.data('tab') : 0;

        switch( tabCurrent ) {
            case 2 :
                let printings = document.getElementById("assign_printings").value;
                if( printings === '' ) processNext();
                printings = JSON.parse(printings);
                if( !printings.length ) processNext();
                let alter_id = 0;
                if( typeof ( vars ) != 'undefined' && typeof ( vars.alter_id ) != 'undefined' ) {
                    alter_id = vars.alter_id;
                } else {
                    let alter_id_from_get = getParameterByName('alter_id');
                    if( alter_id_from_get !== false ) alter_id = alter_id_from_get;
                }
                loadPrintingIntoCutter(printings[0], alter_id);
                if ( alter_id ) {
                    var designUrl = '/files/prints/png/' + alter_id + '.png';
                    if ( tool === undefined ) {
                        var intervalTimer = setInterval(() => callLoadExistingDesign(designUrl, intervalTimer), 500);
                    }
                }
                break;
            case 3 :
                tool.renderResultCanvas();
                let ar = tool.getResultPNGData();
                postFromCutter(ar);
                break;
            default :
                processNext();
                break;
        }

    }

    function getCroppingType() {
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "getCropType",
                alter_id: findGetParameter('alter_id', 0)
            },
            success: function( response ) {
                if( typeof getCroppingType_response !== 'undefined' ) {
                    getCroppingType_response(response);
                }
            }
        });

    }

    function searchForDesigns( search_term ) {

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                'action': "searchForDesigns",
                'search_term': search_term
            },
            success: function( response ) {
                if( typeof searchForDesigns_response !== 'undefined' ) {
                    searchForDesigns_response(response);
                }

            }
        });

    }

    function searchForCard( search_term ) {

        if( search_term === '' )
            return;
        document.getElementById("assign_printings").value = '';
        document.getElementById("assign_visible_printings").value = '';
        showLoading();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                'action': "searchForCard",
                'search_term': JSON.stringify(search_term)

            },
            success: function( response ) {
                if( typeof searchForCard_response !== 'undefined' ) {
                    searchForCard_response(response);
                }
                hideLoading();
            }
        });

    }

    function getPrintingsForCard( card_id ) {

        showLoading();
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                'action': "getPrintingsForCard",
                'card': card_id
            },
            success: function( response ) {
                if( typeof getPrintingsForCard_response !== 'undefined' ) {
                    getPrintingsForCard_response(response);
                }
                hideLoading();
            }
        });

    }

    function postFromCutter( body ) {
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                'action': "postFromCutter",
                'data': body
            },
            success: function( response ) {
                $('#assign_submit_file').val(response.path);
                processNext();
            }
        });

    }
}

function loadPrintingIntoCutter( printing_id, alter_id = 0 ) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: vars.ajaxurl,
        async: true,
        data: {
            'action': "loadPrintingInCutter",
            'printing_id': printing_id,
            'alter_id': alter_id
        },
        success: function( response ) {
            if( response.status === 0 )
                return;
            $('#wrapper-cutter').replaceWith(response.refresh);
            loadCutter(response.data);
            if( typeof processNext === 'function' ) processNext();
        }
    });
}

function getParameterByName( name, url = window.location.href ) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if( !results ) return null;
    if( !results[2] ) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

$(document).ready(function() {
    $(document).on('click', '.mc_file_input_button', function( e ) {
        e.preventDefault();
        $(this).siblings('input[type="file"]').first().trigger('click')
    });

    $(document).on('change', '.mc_file_input', function( event ) {
        let currentLabel = 'No file chosen';
        let nextButtonDisabled = true;
        if( event.target.files.length ) {
            currentLabel = event.target.files[0]['name'];
            nextButtonDisabled = false;
        }
        document.querySelector('button[id=next_submit]').disabled = nextButtonDisabled;
        $(this).siblings('.mc_file_input_label').first().text(currentLabel);
    });

    let printing_id = getParameterByName('printing_id');
    if( printing_id === null ) return;
    if( !printing_id.length ) return;
    let alter_id = getParameterByName('alter_id');
    if( $('.preview__canvas').length ) {
        loadPrintingIntoCutter(printing_id, alter_id)
    }
});
/**
 * @param fnstring
 */
function runFunction(fnstring = '') {
    let fn = window[fnstring];
    if (typeof fn === "function") fn();
}

/**
 *
 * Cleaner AJAX calls
 *
 * @param action
 * @param data
 * @param callback
 * @param error
 */
function ajaxPost( action, data, callback, error ) {
    if( !action.length ) return;
    data['action'] = action;
    $.post({
        url: vars.ajaxurl,
        data: data,
        success: callback,
        error: error
    })
}

/**
 *
 * @param callback
 * @param ms
 * @returns {function(): void}
 */
function delayKeyup( callback, ms ) {
    var timer = 0;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function() {
            callback.apply(context, args);
        }, ms || 0);
    };
}

/**
 *
 * @param str
 * @returns {boolean}
 */
function isNumeric( str ) {
    if( typeof str != "string" ) return false
    return !isNaN(str) && !isNaN(parseFloat(str))
}

/**
 *
 * Get desired parameter from URL strings
 *
 * @param parameterName
 * @param result
 * @returns {string}
 */
function findGetParameter( parameterName, result ) {
    if( result === undefined ) result = '';
    let tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function( item ) {
            tmp = item.split("=");
            if( tmp[0] === parameterName ) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

/**
 *
 * @param url
 * @param param
 * @param paramVal
 * @returns {string}
 *
 * Adds val to parameter in url
 */
function updateURLParameter( url, param, paramVal ) {
    let TheAnchor = null,
        newAdditionalURL = "",
        tempArray = url.split("?"),
        baseURL = tempArray[0],
        additionalURL = tempArray[1],
        temp = "",
        tmpAnchor,
        TheParams;

    if( additionalURL ) {
        tmpAnchor = additionalURL.split("#");
        TheParams = tmpAnchor[0];
        TheAnchor = tmpAnchor[1];
        if( TheAnchor )
            additionalURL = TheParams;

        tempArray = additionalURL.split("&");

        for( var i = 0; i < tempArray.length; i++ ) {
            if( tempArray[i].split('=')[0] !== param ) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    } else {
        tmpAnchor = baseURL.split("#");
        TheParams = tmpAnchor[0];
        TheAnchor = tmpAnchor[1];

        if( TheParams )
            baseURL = TheParams;
    }

    if( TheAnchor )
        paramVal += "#" + TheAnchor;

    const rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

/**
 *
 * @param newUrl
 */
function updateUrl( newUrl ) {
    history.pushState({}, null, newUrl);
}

/**
 *
 * @param key
 * @param sourceURL
 * @returns {string}
 */
function removeURLParam( key, sourceURL ) {
    let rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = ( sourceURL.indexOf("?") !== -1 ) ? sourceURL.split("?")[1] : "";
    if( queryString !== "" ) {
        params_arr = queryString.split("&");
        for( var i = params_arr.length - 1; i >= 0; i -= 1 ) {
            param = params_arr[i].split("=")[0];
            if( param === key ) {
                params_arr.splice(i, 1);
            }
        }
        if( params_arr.length ) rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

/**
 *
 * Redirect to location
 *
 * @param url
 */
function redirect( url ) {
    window.location.replace(url);
}

/**
 *
 * @param actionName
 * @returns {boolean|*|string|jQuery}
 */
function getNonceValByAction( actionName ) {
    if( actionName === undefined ) return false;
    let selector = $('#' + actionName + '_action_mc');
    if( !selector.length ) return false;
    return selector.val();
}

/**
 *
 * @param name
 * @param value
 * @param days
 */
function createCookie( name, value, days ) {
    var date, expires;
    if( days ) {
        date = new Date();
        date.setTime(date.getTime() + ( days * 24 * 60 * 60 * 1000 ));
        expires = "; expires=" + date.toUTCString();
    } else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 *
 * @param name
 * @param path
 * @param domain
 */
function deleteCookie( name, path, domain ) {
    if( getCookie(name) ) {
        document.cookie = name + "=" +
            ( ( path ) ? ";path=" + path : "" ) +
            ( ( domain ) ? ";domain=" + domain : "" ) +
            ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
    }
}

/**
 *
 * @param name
 * @returns {boolean}
 */
function getCookie( name ) {
    return document.cookie.split(';').some(c => {
        return c.trim().startsWith(name + '=');
    });
}

/**
 *
 * @param data
 * @param fileName
 * @returns {(function(*, *): void)|*}
 */
function downloadFileFromData( data, fileName ) {
    if( data === undefined || fileName === undefined ) return;
    const a = document.createElement("a");
    document.body.appendChild(a);
    a.style = "display: none";
    return function( data, fileName ) {
        const blob = new Blob([data], { type: "octet/stream" }),
            url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
    };
}

/**
 *
 * @param currentElement
 */
function scrollToElement( currentElement ) {
    $([document.documentElement, document.body]).animate({
        scrollTop: currentElement.offset().top - 200
    }, 1000);
}

/**
 *
 * @param id
 * @param defaultVal
 * @returns {*|string}
 *
 * Gets a value safely from an input presuming ID with #input-xyz
 */
function safeInputVal( id, defaultVal = '' ) {
    return selectInput(id).length ? selectInput(id).val() : defaultVal;
}

/**
 * @param id
 * @returns {*|jQuery|HTMLElement}
 *
 * Selects input assuming it's IDed with #input-xyz
 */
function selectInput( id ) {
    return $('#input-' + id);
}

/**
 *
 * @param countDownDate
 * @param finalText
 * @param elementId
 */
function countdown( countDownDate, finalText, elementId ) {
    if( countDownDate === undefined ) return;
    if( countDownDate.toString().length === 10 ) countDownDate = countDownDate * 1000;
    elementId = elementId === undefined ? 'countdown' : elementId;
    if( document.getElementById(elementId) === null ) return;
    finalText = finalText === undefined ? 'The countdown has ended' : finalText;
    let x = setInterval(function() {
        // Get today's date and timeg
        let now = new Date().getTime();
        // Find the distance between now and the count down date
        let distance = countDownDate - now;
        // Time calculations for days, hours, minutes and seconds
        let days = Math.floor(distance / ( 1000 * 60 * 60 * 24 )),
            hours = Math.floor(( distance % ( 1000 * 60 * 60 * 24 ) ) / ( 1000 * 60 * 60 )),
            minutes = Math.floor(( distance % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 )),
            seconds = Math.floor(( distance % ( 1000 * 60 ) ) / 1000);
        // Display the result in the element with id="demo"
        let text = hours + "h " + minutes + "m " + seconds + "s ";
        if( days > 0 ) days + "d " + text;
        document.getElementById(elementId).innerHTML = text;
        // If the count down is finished, write some text
        if( distance < 0 ) {
            clearInterval(x);
            document.getElementById(elementId).innerHTML = finalText;
        }
    }, 1000);
}
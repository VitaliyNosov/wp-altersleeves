var tool;

function loadCutter( data ) {
    if( data === undefined && input === undefined )
        return;
    data = data !== undefined ? data : input;

    jQuery(".preview__canvas").remove();
    jQuery(".preview__canvasWrapper").prepend('<canvas class="preview__canvas"></canvas>');

    jQuery(".preview__outOfBoundsCanvas").remove();
    jQuery(".preview__canvasWrapper").prepend('<canvas class="preview__outOfBoundsCanvas"></canvas>');

    jQuery(".resultCanvas").remove();
    jQuery(".preview__canvasWrapper").prepend('<canvas class="resultCanvas" style="display: none;"></canvas>');

    jQuery(".resultCardCanvas").remove();
    jQuery(".preview__canvasWrapper").prepend('<canvas class="resultCardCanvas" style="display: none;"></canvas>');

    // load the helper functions
    loadScript("helper.js", function() {

        // load the refs to all html elements
        loadScript("refs.js", function() {

            // load the tool class
            loadScript("tool.js", function() {
                console.log(data);
                tool = new Tool(data, $refs);

            });

        });
    });
}

function loadScript( url, callback ) {
    // Adding the script tag to the head as suggested before
    var head = document.head;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = input.js_dir + url;

    // Then bind the event to the callback function.
    // There are several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Fire the loading
    head.appendChild(script);
}
<canvas class="resultCanvas" style="display: none;"></canvas>
<canvas class="resultCardCanvas" style="display: none;"></canvas>
<div class="row h-100 align-items-start">
    <div class="preview p-3 col-6 bg-light">
        <div class="preview__canvasWrapper text-center">
            <canvas class="preview__outOfBoundsCanvas"></canvas>
            <canvas class="preview__canvas"></canvas>
            <div class="preview__canvasToolbar text-center my-3">
                <select class="preview__chooseImage"></select>
                <button type="button" class="preview__toggleLock btn btn-danger text-white"><i class="fas fa-lock"></i> <span>Locked</span></button>
                <button type="button" class="preview__toggleZoom btn btn-warning"><i class="fas fa-search-plus"></i> Toggle Zoom</button>
                <button type="button" class="preview__reset btn btn-danger text-white">Reset</button>
                <button type="button" class="preview__download btn btn-success text-white">Download</button>
                <!--<button type="button" class="preview__submit btn btn-success text-white">Submit</button>
                <br>

                  <button type="button" class="preview__download_card btn btn-success text-white"><i class="fas fa-download"></i> card</button>-->
            </div>
        </div>
    </div>
    <div class="configWrapper col p-0">
        <form class="config" method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="resultPNGData" class="config_resultPNGDataInput">
            <input type="hidden" name="resultPNGData" class="config_resultCombinedPNGDataInput">
            <div class="config__frameCode config__frameCode--bordered card p-3 m-0" style="display: none;">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Choose Frame Code</span>
                    </div>
                    <select class="config__selectFrameCode form-control" name="frameCode">
                        <option>--- Please Choose a Frame Code ---</option>
                    </select>
                </div>
            </div>
            <div class="config__upload config__upload--bordered card p-4 m-0">
                <div class="input-group mb-3 align-items-center">
                    <div class="input-group-prepend">
                        <button title="Remove design image" type="button" class="config__removeImage m-0  input-group-text btn btn-danger">&times;
                        </button>
                    </div>
                    <div class="px-2">
                        <small>Design</small>
                    </div>
                    <input type="file" class="config__uploadDesign form-control" accept=".png,.jpg,.jpeg" name="design" />
                    <div class="input-group-append px-2">
                        <input type="checkbox" class="config__uploadDesignMaskTransparent" aria-label="Use transparent mask of the design image"
                               title="Use transparent mask of the design image">
                    </div>
                    <div class="input-group-append" style="width: 50px">
                        <input title="Pick the background color here" class="config__uploadDesignColor" type="color" value="#ffffff" />
                    </div>
                </div>
                <div class="input-group align-items-center">
                    <div class="input-group-prepend">
                        <button title="Remove secondary image" type="button" class="config__removeSecondary m-0 input-group-text btn btn-danger">
                            &times;
                        </button>
                    </div>
                    <div class="input-group-prepend">
                        <div class="px-2">
                            <small>Signature</small>
                        </div>
                    </div>
                    <input type="file" class="config__uploadDesignSecondary form-control" accept=".png,.jpg,.jpeg" name="design" />
                    <div class="input-group-append px-2">
                        <input checked type="checkbox" class="config__uploadSecondaryMaskTransparent"
                               aria-label="Use transparent mask of the secondary image" title="Use transparent mask of the secondary image">
                    </div>
                    <div class="input-group-append" style="width: 50px">
                        <input title="Pick the background color here" class="config__uploadDesignSecondaryColor" type="color"
                               value="#ffffff" />
                    </div>
                </div>
            </div>
            <div class="config__options card p-3 m-0">
                <div class="row p-3 h-100">
                    <div class="col card config__scrollPane config__preconfigurations">
                        <h5>Preconfigurations</h5>
                        <template class="config__preconfigurationsTemplate">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" name="preconfiguration[]">
                                <label class="form-check-label" data-bs-toggle="tooltip" data-placement="top" title="Tooltip">Preconfiguration</label>
                            </div>
                        </template>
                        <div class="config__preconfigurationsOptions">
                        </div>
                    </div>
                    <div class="col card config__scrollPane config__maskElements">
                        <h5>Elements</h5>
                        <template class="config__maskElementsTemplate">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="maskElement[]">
                                <label class="form-check-label" data-bs-toggle="tooltip" data-placement="top" title="Tooltip">Preconfiguration</label>
                            </div>
                        </template>
                        <div class="config__maskElementsOptions">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    function loadPrintingIntoCutter( printing_id ) {

        $.ajax({
            type: "post",
            dataType: "json",
            url: ajax.ajaxurl,
            async: true,
            data: {
                'action': "loadPrintingInCutter",
                'printing_id': printing_id
            },
            success: function( response ) {
                if( response.status === 0 )
                    return;
                $('#wrapper-cutter').replaceWith(response.refresh);
                loadCutter(response.data);
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
        let printing_id = getParameterByName('printing_id');
        if( !printing_id.length ) return;
        if( $('.preview__canvas').length ) {
            loadPrintingIntoCutter(printing_id)
        }
    })
</script>
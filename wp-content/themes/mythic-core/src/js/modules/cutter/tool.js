"use strict";

function getResultPNGData() {
    return this.$refs.resultPNGDataInput.val();
}

function Tool( input, $refs ) {

    console.log(input);

    this.input = input;
    this.$refs = $refs;

    this.allImgsLength = 0;
    this.allImgsLoaded = 0;

    this.getResultPNGData = getResultPNGData;
    this.chosenFrameCodeKey = 0;
    this.zoomMode = false;
    this.lockMode = true;
    this.previewCanvasScaledown = -1;

    this.uploadedImage = null;
    this.uploadedImageColor = $refs.uploadDesignColor.val();
    this.uploadedImageIsValid = false;
	this.uploadedImageInInit = false;
    this.uploadedImageOffsetX = 0;
    this.uploadedImageOffsetY = 0;
    this.uploadedImageScaledown = 1;
    this.uploadedImageMaxScaledown = 1;

    // load all images from the input data
    this.loadAllImages();

    this.attachUploadDesignListener();

    this.attachPreviewCanvasListener();

    this.attachLockBtnListener();
    this.attachZoomBtnListener();
    this.attachResetBtnListener();
    this.attachSubmitBtnListener();
    this.attachDownloadBtnListener();
    this.attachDownloadBtnCardListener();

    //secondary image
    this.attachUploadSecondaryListener();
    this.uploadedImageSecondaryIsValid = false;
    this.uploadedImageSecondary = null;
    this.uploadedImageSecondaryColor = $refs.uploadDesignSecondaryColor.val();
    this.uploadedImageSecondaryOffsetX = 0;
    this.uploadedImageSecondaryOffsetY = 0;
    this.uploadedImageSecondaryScaledown = 1;
    this.uploadedImageSecondaryMaxScaledown = 1;

    this.attachUploadDesignSecondaryColorListener();
    this.attachRemoveSecondaryListener();

    this.attachUploadDesignColorListener();
    this.attachRemoveImageListener();
    this.attachUploadDesignMaskTransparentListener();
    this.attachUploadSecondaryMaskTransparent();

}

/** ################################ DATA FUNCTIONS ################################ */

/**
 * Called when a frame code is chosen to fill in all the mask elements and preconfigurations for it
 */
Tool.prototype.populateMaskElementsAndPreconfigurations = function() {
    // there is a chosen frameCode
    var _this = this;

    if( _this.chosenFrameCodeKey !== -1 ) {
        // empty the preconfiguration options holder
        _this.$refs.maskElementsOptions.html(""); // render new ones

        for( var i in _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements ) {
            var maskElementsTemplate = jQuery(_this.$refs.maskElementsTemplate.html());
            maskElementsTemplate.find("label").text(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[i].name);
            maskElementsTemplate.find("label").attr("title", _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[i].desc);
            maskElementsTemplate.find("label").attr("for", "maskElement" + i);
            maskElementsTemplate.find("input").attr("data-key", i);
            maskElementsTemplate.find("input").attr("id", "maskElement" + i);
            maskElementsTemplate.find("input").val(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[i].id);
            if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[i].isLocked ) {
                maskElementsTemplate.find("input").attr("disabled", "disabled");
            }

            _this.$refs.maskElementsOptions.append(maskElementsTemplate);
        } // empty the mask elements options holder

        _this.$refs.preconfigurationsOptions.html(""); // render new ones

        for( var _i in _this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations ) {
            var preconfigurationsTemplate = jQuery(_this.$refs.preconfigurationsTemplate.html());
            preconfigurationsTemplate.find("label").text(_this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations[_i].name);
            preconfigurationsTemplate.find("label").attr("title", _this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations[_i].desc);
            preconfigurationsTemplate.find("label").attr("for", "preconfiguration" + _i);
            preconfigurationsTemplate.find("input").attr("data-key", _i);
            preconfigurationsTemplate.find("input").attr("id", "preconfiguration" + _i);
            preconfigurationsTemplate.find("input").val(_this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations[_i].id);

            _this.$refs.preconfigurationsOptions.append(preconfigurationsTemplate);

            if( _this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations[_i].isDefault ) {
                jQuery("input[name='preconfiguration[]'][data-key='" + _i + "']").prop("checked", true).change();
            }
        }

    }
}

/**
 * Loads all imageURLs so images can later be drawn in the canvas
 */
Tool.prototype.loadAllImages = function() {

    var _this = this;

    _this.chosenFrameCodeKey = 0;

    // populate the correct elements and preconfigurations for that frameCode
    _this.enableDisableSteps();

    _this.attachGlobalPreconfigurationAndMaskElementsListeners();

    _this.populateMaskElementsAndPreconfigurations();

    _this.allImgsLength = 1 + _this.input.maskMaps[0].maskElements.length;

    // load example card image
    var image = _this.loadImage(_this.input.maskMaps[0].exampleCardPNGURL, true); // modify the input structure and store it in a new var
    _this.input.maskMaps[0].exampleCardIMG = image; // loop all mask elements

    for( var j in _this.input.maskMaps[0].maskElements ) {
        // load the mask element mask image
        var _image = _this.loadImage(_this.input.maskMaps[0].maskElements[j].maskPNGURL, true); // modify the input structure and store it in a new var

        _this.input.maskMaps[0].maskElements[j].maskIMG = _image;
    }

    if(
    	typeof(_this.input.maskMaps.product_image_data) != 'undefined'
		&& typeof(_this.input.maskMaps.product_image_data.image) != 'undefined'
	){
		// reset the validity of the inpotImage
		_this.uploadedImageIsValid = false;
		// load the design image and store a pointer in a class variable for later drawing in canvas
		_this.uploadedImage = _this.loadImage(_this.input.maskMaps.product_image_data.image);

		// identifier for disable bg drawing
		_this.uploadedImageInInit = true;

		// when the design image is loaded
		_this.uploadedImage.onload = function( event ) {
			// clear any previoua stored design in the hidden input form field (and re-disable the download and submit buttons)
			_this.clearPreviousResultData();
			var canvasWidth = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth;
			var canvasHeight = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight;

			// check if the design image is above the minimum dimentions requirements
			if( _this.uploadedImage.width >= canvasWidth && _this.uploadedImage.height >= canvasHeight ) {

				// mark the design image as valid
				_this.uploadedImageIsValid = true;

				//add this image to the list of images
				if( _this.$refs.chooseImage.find("option[value='design']").length === 0 ) {
					_this.$refs.chooseImage.append("<option value='design' selected>Design</option>");
				} else {
					_this.$refs.chooseImage.find("option[value='design']").prop("selected", "true");
				}

				// reset any offsets and zooms
				_this.uploadedImageOffsetX = 0;
				_this.uploadedImageOffsetY = 0;
				_this.uploadedImageScaledown = 1;

				// design images are always starting in the preview frame in the biggest scale possible
				// so just calculate the maximum scaledown factor of the design image so the image is always bigger then the canvas
				var canvasCoef = canvasWidth / canvasHeight;
				var imageCoef = _this.uploadedImage.width / _this.uploadedImage.height;

				if( canvasCoef > imageCoef ) {
					_this.uploadedImageMaxScaledown = _this.uploadedImage.width / canvasWidth;
					_this.uploadedImageOffsetY = -( _this.uploadedImage.height / _this.uploadedImageMaxScaledown - canvasHeight ) / 2;
				} else {
					_this.uploadedImageMaxScaledown = _this.uploadedImage.height / canvasHeight;
					_this.uploadedImageOffsetX = -( _this.uploadedImage.width / _this.uploadedImageMaxScaledown - canvasWidth ) / 2;
				}

				//re-scale image
				_this.uploadedImageScaledown = _this.uploadedImageMaxScaledown;

				_this.renderPreviewCanvas();
			} else {
				// mark the image as invalid (if previously was mark valid)
				_this.uploadedImageIsValid = false;
				// show a message to the user
				alert("Image dimensions should be at least " + canvasWidth + "x" + canvasHeight)

				// reset the design image file input
				_this.$refs.uploadDesignInput.val('');

				_this.renderPreviewCanvas();
			}

			_this.enableDisableSteps();
		};
	}

};
/**
 * Creates an image object and assigns src so the image can be loaded
 * @param {*} src - the URL of the image
 * @param {*} count - this will count the number of images that must be loaded in order this to work
 */
Tool.prototype.loadImage = function( src, count ) {
    var _this = this;
    var image = new Image();
    if( count ) {
        image.addEventListener('load', function() {
            _this.allImgsLoaded++;

            if( _this.allImgsLoaded === _this.allImgsLength ) {

                _this.renderPreviewCanvas();

                _this.enableDisableSteps();

                return false;
            }
        });
    }
    image.src = src;
    return image;
};

Tool.prototype.loadExistingDesign = function( url ) {
    var _this = this;
    if (document.querySelector('button[id=next_submit]')){
      document.querySelector('button[id=next_submit]').disabled = false;
    }
    // reset the validity of the inpotImage
    _this.uploadedImageIsValid = false;
    // load the design image and store a pointer in a class variable for later drawing in canvas
    _this.uploadedImage = _this.loadImage(url);
    // when the design image is loaded
    _this.uploadedImage.onload = function( event ) {
        // clear any previoua stored design in the hidden input form field (and re-disable the download and submit buttons)
        _this.clearPreviousResultData();
        var canvasWidth = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth;
        var canvasHeight = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight;

        // check if the design image is above the minimum dimentions requirements
        if( _this.uploadedImage.width >= canvasWidth && _this.uploadedImage.height >= canvasHeight ) {

            // mark the design image as valid
            _this.uploadedImageIsValid = true;

            //add this image to the list of images
            if( _this.$refs.chooseImage.find("option[value='design']").length === 0 ) {
                _this.$refs.chooseImage.append("<option value='design' selected>Design</option>");
            } else {
                _this.$refs.chooseImage.find("option[value='design']").prop("selected", "true");
            }

            // reset any offsets and zooms
            _this.uploadedImageOffsetX = 0;
            _this.uploadedImageOffsetY = 0;
            _this.uploadedImageScaledown = 1;

            // design images are always starting in the preview frame in the biggest scale possible
            // so just calculate the maximum scaledown factor of the design image so the image is always bigger then the canvas
            var canvasCoef = canvasWidth / canvasHeight;
            var imageCoef = _this.uploadedImage.width / _this.uploadedImage.height;

            if( canvasCoef > imageCoef ) {
                _this.uploadedImageMaxScaledown = _this.uploadedImage.width / canvasWidth;
                _this.uploadedImageOffsetY = -( _this.uploadedImage.height / _this.uploadedImageMaxScaledown - canvasHeight ) / 2;
            } else {
                _this.uploadedImageMaxScaledown = _this.uploadedImage.height / canvasHeight;
                _this.uploadedImageOffsetX = -( _this.uploadedImage.width / _this.uploadedImageMaxScaledown - canvasWidth ) / 2;
            }

            //re-scale image
            _this.uploadedImageScaledown = _this.uploadedImageMaxScaledown;

            // draw all masks
            _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
                if( $(el).is(":checked") && !$(el).is(":disabled") ) {
                    var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                    if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG ) {
                        ctx.drawImage(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG, 0, 0, _this.$refs.previewCanvas.get(0).width, _this.$refs.previewCanvas.get(0).height);
                    } else {
                        console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                    }
                }
            });

            _this.renderPreviewCanvas();
        } else {
            // mark the image as invalid (if previously was mark valid)
            _this.uploadedImageIsValid = false;
            // show a message to the user
            alert("Image dimensions should be at least " + canvasWidth + "x" + canvasHeight)

            // reset the design image file input
            _this.$refs.uploadDesignInput.val('');

            _this.renderPreviewCanvas();
        }
        _this.enableDisableSteps();
    };
}

/** ################################ LISTENER FUNCTIONS ################################ */

/**
 * Listener for when the user attaches a file in the upload design file input
 */
Tool.prototype.attachUploadDesignListener = function() {
    var _this = this;
    if (document.querySelector('button[id=next_submit]')){
      document.querySelector('button[id=next_submit]').disabled = true;
    }
    // off from earlier binds in case the function is called multiple times
    _this.$refs.uploadDesignInput.off();
    _this.$refs.uploadDesignInput.on("change", function( event ) {
        // if no files are attached stop
        if( event.target.files.length === 0 )
            return;

        // reset the validity of the inpotImage
        _this.uploadedImageIsValid = false;
        // load the design image and store a pointer in a class variable for later drawing in canvas
        _this.uploadedImage = _this.loadImage(URL.createObjectURL(event.target.files[0]));
        var fileName = event.target.files[0]['name'];
        // when the design image is loaded
        _this.uploadedImage.onload = function( event ) {
            // clear any previoua stored design in the hidden input form field (and re-disable the download and submit buttons)
            _this.clearPreviousResultData();
            var canvasWidth = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth;
            var canvasHeight = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight;

            // check if the design image is above the minimum dimentions requirements
            if( _this.uploadedImage.width >= canvasWidth && _this.uploadedImage.height >= canvasHeight ) {

                // mark the design image as valid
                _this.uploadedImageIsValid = true;

                //add this image to the list of images
                if( _this.$refs.chooseImage.find("option[value='design']").length === 0 ) {
                    _this.$refs.chooseImage.append("<option value='design' selected>Design</option>");
                } else {
                    _this.$refs.chooseImage.find("option[value='design']").prop("selected", "true");
                }

                // reset any offsets and zooms
                _this.uploadedImageOffsetX = 0;
                _this.uploadedImageOffsetY = 0;
                _this.uploadedImageScaledown = 1;

                // design images are always starting in the preview frame in the biggest scale possible
                // so just calculate the maximum scaledown factor of the design image so the image is always bigger then the canvas
                var canvasCoef = canvasWidth / canvasHeight;
                var imageCoef = _this.uploadedImage.width / _this.uploadedImage.height;

                if( canvasCoef > imageCoef ) {
                    _this.uploadedImageMaxScaledown = _this.uploadedImage.width / canvasWidth;
                    _this.uploadedImageOffsetY = -( _this.uploadedImage.height / _this.uploadedImageMaxScaledown - canvasHeight ) / 2;
                } else {
                    _this.uploadedImageMaxScaledown = _this.uploadedImage.height / canvasHeight;
                    _this.uploadedImageOffsetX = -( _this.uploadedImage.width / _this.uploadedImageMaxScaledown - canvasWidth ) / 2;
                }

                //re-scale image
                _this.uploadedImageScaledown = _this.uploadedImageMaxScaledown;

                if (document.querySelector('.mc_file_input_label')){
                  document.querySelector('.mc_file_input_label').innerHTML = fileName;
                }
                if (document.querySelector('button[id=next_submit]')){
                  document.querySelector('button[id=next_submit]').disabled = false;
                }

                _this.renderPreviewCanvas();
            } else {
                // mark the image as invalid (if previously was mark valid)
                _this.uploadedImageIsValid = false;
                // show a message to the user
                if (document.querySelector('.mc_file_input_label')){
                  document.querySelector('.mc_file_input_label').innerHTML = "No file chosen";
                }
                if (document.querySelector('button[id=next_submit]')){
                  document.querySelector('button[id=next_submit]').disabled = true;
                }
                alert("Image dimensions should be at least " + canvasWidth + "x" + canvasHeight)

                // reset the design image file input
                _this.$refs.uploadDesignInput.val('');

                _this.renderPreviewCanvas();
            }

            _this.enableDisableSteps();
        };

    });
}

Tool.prototype.enableDisableSteps = function() {
    ( this.chosenFrameCodeKey === -1 ) ?
        this.$refs.configUpload.addClass("config__upload--disabled") :
        this.$refs.configUpload.removeClass("config__upload--disabled");

    ( this.chosenFrameCodeKey === -1 ) ?
        this.$refs.previewCanvasToolbar.addClass("preview__canvasToolbar--disabled") :
        this.$refs.previewCanvasToolbar.removeClass("preview__canvasToolbar--disabled");

};

/**
 * Listener for when user changes preconfiguration or toggles mask elements
 */
Tool.prototype.attachGlobalPreconfigurationAndMaskElementsListeners = function() {
    var _this = this;
    jQuery(_this.$refs.preconfigurationsOptions).on("change", "input", function( event ) {
        // callback when a preconfiguration is chosen
        _this.onToggledPreconfiguration(jQuery(event.target).attr("data-key"));
    });
    jQuery(_this.$refs.maskElementsOptions).on("change", "input", function( event ) {
        // callback when a frame element is toggled on or off
        _this.renderPreviewCanvas();
    });
    jQuery(_this.$refs.maskElementsOptions).on("mouseover", "input", function( event ) {
        // callback when a frame element is toggled on or off
        _this.renderPreviewCanvas();
    });
    jQuery(_this.$refs.maskElementsOptions).on("mouseover", "label", function( event ) {
        // callback when a frame element is toggled on or off
        _this.renderPreviewCanvas();
    });
    jQuery(_this.$refs.maskElementsOptions).on("mouseout", "input", function( event ) {
        // callback when a frame element is toggled on or off
        _this.renderPreviewCanvas();
    });
    jQuery(_this.$refs.maskElementsOptions).on("mouseout", "label", function( event ) {
        // callback when a frame element is toggled on or off
        _this.renderPreviewCanvas();
    });
};
/**
 * callback when a preconfiguration is selected
 */
Tool.prototype.onToggledPreconfiguration = function( preconfigurationKey ) {
    var _this = this;
    // if a framecode is selected
    if( _this.chosenFrameCodeKey !== -1 ) {
        jQuery("input[name='maskElement[]']").prop("checked", false);
        for( var i in _this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations[preconfigurationKey].maskElementsKeys ) {
            var maskElementKey = _this.input.maskMaps[_this.chosenFrameCodeKey].preconfigurations[preconfigurationKey].maskElementsKeys[i];
            jQuery("input[name='maskElement[]'][data-key='" + maskElementKey + "']").prop("checked", true);
        }
        _this.renderPreviewCanvas();
    }

}

/**
 * Listener for when mouse drags in canvas
 */
Tool.prototype.attachPreviewCanvasListener = function() {
    var _this = this;

    // capture the current state of the mouse button
    $(document).mousedown(function() {
        window.mouseDown = true;
    }).mouseup(function() {
        window.mouseDown = false;
    });

    // off from previous events in case the function is called multiple times
    _this.$refs.previewCanvas.off();
    _this.$refs.previewCanvas.on("mousemove", function( event ) {

        // if mouse is down and moved - e.g. dragged
        if( window.mouseDown ) {
            if( window.lastPreviewCanvasMouseMoveX && window.lastPreviewCanvasMouseMoveY ) {

                // calculate the delta movement
                var deltaX = event.pageX - window.lastPreviewCanvasMouseMoveX;
                var deltaY = event.pageY - window.lastPreviewCanvasMouseMoveY;

                // invoke callback
                _this.onMouseMoveInPreviewCanvas(deltaX, deltaY);
            }
            window.lastPreviewCanvasMouseMoveX = event.pageX;
            window.lastPreviewCanvasMouseMoveY = event.pageY;
        } else {
            window.lastPreviewCanvasMouseMoveX = 0;
            window.lastPreviewCanvasMouseMoveY = 0;
        }

    });
}
/**
 * Callback when the mouse iss moved within the preview canvas
 * @param {*} deltaX - movement amount on X coordinate
 * @param {*} deltaY - movement amount on Y coordinate
 */
Tool.prototype.onMouseMoveInPreviewCanvas = function( deltaX, deltaY ) {
    var _this = this;
    if( _this.$refs.chooseImage.val() === "design" ) {
        // translate mode
        if( _this.zoomMode === false ) {
            // increase x and y offsets of the design (uploaded) image
            _this.uploadedImageOffsetX += deltaX * _this.previewCanvasScaledown;
            _this.uploadedImageOffsetY += deltaY * _this.previewCanvasScaledown;

        }
        // zoom mode - zoom always happens wih upper left point as a pivot
        else {
            // increase the scale down factor - multiply by 0.01 because you need to minify the effect of the movement in the case of zoom
            _this.uploadedImageScaledown += 0.01 * ( ( -deltaX - deltaY ) / 2 );
        }

        // if lock mode is on - enforce constrains
        if( _this.lockMode === true ) {
            // the bigger the number - the smaller the image

            // enforce that the maximum zoom is 1 e.g. the starting zoom of the image (which is calculated to be the maximum possible with regards to the resolution constraint)
            if( _this.uploadedImageScaledown < 1 ) {
                _this.uploadedImageScaledown = 1;
            }
            // enforce that the minimum zoom to be bigger then the maximum possible scaledown, calculated when image is uploaded
            if( _this.uploadedImageScaledown > _this.uploadedImageMaxScaledown ) {
                _this.uploadedImageScaledown = _this.uploadedImageMaxScaledown;
            }

            if( _this.uploadedImageOffsetX > 0 ) {
                _this.uploadedImageOffsetX = 0;
            }
            if( _this.uploadedImageOffsetY > 0 ) {
                _this.uploadedImageOffsetY = 0;
            }

            var maxNegativeOffsetX = ( _this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth - ( _this.uploadedImage.width / _this.uploadedImageScaledown ) );
            if( _this.uploadedImageOffsetX < maxNegativeOffsetX ) {
                _this.uploadedImageOffsetX = maxNegativeOffsetX;
            }

            var maxNegativeOffsetY = ( _this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight - ( _this.uploadedImage.height / _this.uploadedImageScaledown ) );
            if( _this.uploadedImageOffsetY < maxNegativeOffsetY ) {
                _this.uploadedImageOffsetY = maxNegativeOffsetY;
            }
        }
    } else {
        // translate mode
        if( _this.zoomMode === false ) {
            // increase x and y offsets of the design (uploaded) image
            _this.uploadedImageSecondaryOffsetX += deltaX * _this.previewCanvasScaledown;
            _this.uploadedImageSecondaryOffsetY += deltaY * _this.previewCanvasScaledown;

        }
        // zoom mode - zoom always happens wih upper left point as a pivot
        else {
            // increase the scale down factor - multiply by 0.01 because you need to minify the effect of the movement in the case of zoom
            _this.uploadedImageSecondaryScaledown += 0.01 * ( ( -deltaX - deltaY ) / 2 );
        }
        // enforce that the maximum zoom is 1 e.g. the starting zoom of the image (which is calculated to be the maximum possible with regards to the resolution constraint)
        if( _this.uploadedImageSecondaryScaledown < 1 ) {
            _this.uploadedImageSecondaryScaledown = 1;
        }
        // enforce that the minimum zoom to be bigger then the maximum possible scaledown, calculated when image is uploaded
        //if (this.uploadedImageSecondaryScaledown > this.uploadedImageSecondaryMaxScaledown) {
        //  this.uploadedImageSecondaryScaledown = this.uploadedImageSecondaryMaxScaledown;
        //}

        if( _this.uploadedImageSecondaryOffsetX < 0 ) {
            _this.uploadedImageSecondaryOffsetX = 0;
        }
        if( _this.uploadedImageSecondaryOffsetY < 0 ) {
            _this.uploadedImageSecondaryOffsetY = 0;
        }

        if(_this.uploadedImageSecondary) {
			maxNegativeOffsetX = (_this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth - (_this.uploadedImageSecondary.width / _this.uploadedImageSecondaryScaledown));
			if (_this.uploadedImageSecondaryOffsetX > maxNegativeOffsetX) {
				_this.uploadedImageSecondaryOffsetX = maxNegativeOffsetX;
			}

			maxNegativeOffsetY = (_this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight - (_this.uploadedImageSecondary.height / _this.uploadedImageSecondaryScaledown));
			if (_this.uploadedImageSecondaryOffsetY > maxNegativeOffsetY) {
				_this.uploadedImageSecondaryOffsetY = maxNegativeOffsetY;
			}
		}
    }

    _this.renderPreviewCanvas();

}

/**
 * Listener that toggles the lockMode when the lock button is toggled - also change visual bootstrap classes
 */
Tool.prototype.attachLockBtnListener = function() {
    var _this = this;
    _this.$refs.lockBtn.off();
    _this.$refs.lockBtn.on("click", function( event ) {
        _this.lockMode = !_this.lockMode;
        $(event.target).removeClass(( _this.lockMode ) ? "btn-success" : "btn-danger");
        $(event.target).addClass(( _this.lockMode ) ? "btn-danger" : "btn-success");
        $(event.target).find("span").html(( _this.lockMode ) ? "Locked" : "Unlocked");

        // if the constrained are switched on after an off state - recalculate the position and scale of the design image to fit within them
        _this.onMouseMoveInPreviewCanvas(0, 0);
    });
}

/**
 * Listener that toggles the zoomMode when the lock button is toggled - also change visual bootstrap classes
 */
Tool.prototype.attachZoomBtnListener = function() {
    var _this = this;
    _this.$refs.zoomBtn.off();
    _this.$refs.zoomBtn.on("click", function( event ) {
        _this.zoomMode = !_this.zoomMode;
        $(event.target).removeClass(( _this.zoomMode === true ) ? "btn-warning" : "btn-success");
        $(event.target).addClass(( _this.zoomMode === true ) ? "btn-success" : "btn-warning");
    });
}

/**
 * Listener that resets the zoom and translate offsets of the design image when button is pressed
 */
Tool.prototype.attachResetBtnListener = function() {
    var _this = this;
    _this.$refs.resetBtn.off();
    _this.$refs.resetBtn.on("click", function( event ) {
        _this.uploadedImageOffsetX = 0;
        _this.uploadedImageOffsetY = 0;
        _this.uploadedImageScaledown = 1;
        _this.renderPreviewCanvas();
    });
}
/**
 * Render in an offscreen canvas and submit the form
 */
Tool.prototype.attachSubmitBtnListener = function() {
    var _this = this;
    _this.$refs.submitBtn.off();
    _this.$refs.submitBtn.on("click", function( event ) {
        _this.renderResultCanvas();
        if( location.hostname !== "localhost" && location.hostname !== "127.0.0.1" )
            _this.$refs.configForm.submit();

        /* Added by James for combined out */
        var canvas = document.getElementById("canvas");
        el.href = canvas.toDataURL("image/jpg");
    });
}

/**
 * Render in an offscreen canvas and save the result (the way it does it is by creating virtual A tag and setting is href to the base64encoded result image)
 */
Tool.prototype.attachDownloadBtnListener = function() {
    var _this = this;
    _this.$refs.downloadBtn.off();
    _this.$refs.downloadBtn.on("click", function( event ) {

        _this.renderResultCanvas();

        var downloadLink = document.createElement('a');
        downloadLink.setAttribute('download', 'result_' + ( new Date() ).toISOString().replace(/\D/g, '').substr(0, 14) + '.png');
        var url = _this.$refs.resultPNGDataInput.val().replace(/^data:image\/png/, 'data:application/octet-stream');
        downloadLink.setAttribute('href', url);
        downloadLink.click();

        event.preventDefault();
        return false;

    });

}

Tool.prototype.attachDownloadBtnCardListener = function() {
    var _this = this;
    _this.$refs.downloadBtnCard.off();
    _this.$refs.downloadBtnCard.on("click", function( event ) {
        _this.renderCardCanvas();
        var downloadLink = document.createElement('a');
        downloadLink.setAttribute('download', 'result_' + ( new Date() ).toISOString().replace(/\D/g, '').substr(0, 14) + '.png');
        var url = _this.$refs.resultPNGDataInput.val().replace(/^data:image\/png/, 'data:application/octet-stream');
        downloadLink.setAttribute('href', url);
        downloadLink.click();
        downloadLink.remove();
        event.preventDefault();
        return false;
    });
}

/** ############################ ACTION FUNCTIONS  ############################ */

/**
 * Clears the hidden input with the base64 encoded result image, disables the download and submit buttons
 */
Tool.prototype.clearPreviousResultData = function() {
    this.$refs.resultPNGDataInput.val("");
    this.$refs.downloadBtn.attr("href", "#");
    //this.$refs.downloadBtn.addClass("disabled");
    this.$refs.submitBtn.addClass("disabled");
}

Tool.prototype.clearResultCanvas = function() {
    var ctx = this.$refs.resultCanvas.get(0).getContext("2d");
    ctx.globalCompositeOperation = 'source-over';
    // clear canvas

    ctx.clearRect(0, 0,
        this.$refs.resultCanvas.get(0).width,
        this.$refs.resultCanvas.get(0).height);
    return ctx;
}

Tool.prototype.clearPreviewCanvas = function() {
    var ctx = this.$refs.previewCanvas.get(0).getContext("2d");
    ctx.globalCompositeOperation = 'source-over';
    // clear canvas
    ctx.clearRect(0, 0,
        this.$refs.previewCanvas.get(0).width,
        this.$refs.previewCanvas.get(0).height);
    return ctx;
}

/**
 * Renders the end result in an offscreen canvas
 */
Tool.prototype.renderResultCanvas = function() {
    var _this = this;
    // there is a chosen frameCode
    if( _this.chosenFrameCodeKey !== -1 && _this.input.maskMaps[_this.chosenFrameCodeKey].exampleCardIMG ) {
        _this.$refs.resultCanvas.get(0).width = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth;
        _this.$refs.resultCanvas.get(0).height = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight;

        var ctx = _this.clearResultCanvas();
        // draw design img

        if( _this.uploadedImage && _this.uploadedImageIsValid ) {
            if( _this.$refs.uploadDesignMaskTransparent.prop("checked") ) {
                ctx.fillStyle = _this.uploadedImageColor;
                ctx.fillRect(0, 0,
                    _this.$refs.resultCanvas.get(0).width,
                    _this.$refs.resultCanvas.get(0).height);
            }
            ctx.drawImage(_this.uploadedImage,
                _this.uploadedImageOffsetX, _this.uploadedImageOffsetY,
                _this.uploadedImage.width / _this.uploadedImageScaledown,
                _this.uploadedImage.height / _this.uploadedImageScaledown);
        } else if( _this.$refs.uploadDesignMaskTransparent.prop("checked") ) {
            //draw only the mask
            ctx.fillStyle = _this.uploadedImageColor;
            ctx.fillRect(0, 0,
                _this.$refs.resultCanvas.get(0).width,
                _this.$refs.resultCanvas.get(0).height);
        }

        // enable mask mode
        ctx.globalCompositeOperation = 'destination-out';

        // draw masks if any
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":checked") && !$(el).is(":disabled") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG ) {
                    ctx.drawImage(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG, 0, 0, _this.$refs.resultCanvas.get(0).width, _this.$refs.resultCanvas.get(0).height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });

        ctx.globalCompositeOperation = 'source-over';

        if( _this.uploadedImageSecondary && _this.uploadedImageSecondaryIsValid ) {
            //draw mask if not transparent
            if( _this.$refs.uploadSecondaryMaskTransparent.prop("checked") ) {
                ctx.fillStyle = _this.uploadedImageSecondaryColor;
                ctx.fillRect(_this.uploadedImageSecondaryOffsetX,
                    _this.uploadedImageSecondaryOffsetY,
                    _this.uploadedImageSecondary.width / _this.uploadedImageSecondaryScaledown,
                    _this.uploadedImageSecondary.height / _this.uploadedImageSecondaryScaledown);
            }
            // draw Secondary img
            ctx.drawImage(_this.uploadedImageSecondary,
                _this.uploadedImageSecondaryOffsetX,
                _this.uploadedImageSecondaryOffsetY,
                _this.uploadedImageSecondary.width / _this.uploadedImageSecondaryScaledown,
                _this.uploadedImageSecondary.height / _this.uploadedImageSecondaryScaledown);
        }

        // enable mask mode
        ctx.globalCompositeOperation = 'destination-out';

        // draw masks if any
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":checked") && $(el).is(":disabled") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG ) {
                    ctx.drawImage(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG, 0, 0, _this.$refs.resultCanvas.get(0).width, _this.$refs.resultCanvas.get(0).height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });

        // save the result to an invisible input in the form
        _this.$refs.resultPNGDataInput.val(_this.$refs.resultCanvas.get(0).toDataURL("image/png"));
        _this.$refs.resultCombinedPNGDataInput.val(_this.$refs.previewCanvas.get(0).toDataURL("image/png"));
    }
}

Tool.prototype.renderCardCanvas = function() {
    var _this = this;
    // there is a chosen frameCode
    if( _this.chosenFrameCodeKey !== -1 && _this.input.maskMaps[_this.chosenFrameCodeKey].exampleCardIMG ) {
        var maskMap = _this.input.maskMaps[_this.chosenFrameCodeKey];
        var resultCanvas = _this.$refs.resultCanvas.get(0);
        resultCanvas.width = maskMap.canvasWidth;
        resultCanvas.height = maskMap.canvasHeight;
        var resultCardCanvas = _this.$refs.resultCardCanvas.get(0);
        resultCardCanvas.width = resultCanvas.width;
        resultCardCanvas.height = resultCanvas.height;

        // draw card-------------------------------------------------------------------------
        var ctxCard = _this.clearResultCanvas();
        //draw card
        ctxCard.drawImage(maskMap.exampleCardIMG, 0, 0,
            resultCanvas.width,
            resultCanvas.height);

        if( _this.uploadedImageSecondary && _this.uploadedImageSecondaryIsValid ) {
            //draw mask if not transparent
            if( _this.$refs.uploadSecondaryMaskTransparent.prop("checked") ) {
                ctxCard.fillStyle = _this.uploadedImageSecondaryColor;
                ctxCard.fillRect(_this.uploadedImageSecondaryOffsetX,
                    _this.uploadedImageSecondaryOffsetY,
                    _this.uploadedImageSecondary.width / _this.uploadedImageSecondaryScaledown,
                    _this.uploadedImageSecondary.height / _this.uploadedImageSecondaryScaledown);
            }
            // draw Secondary img
            ctxCard.drawImage(_this.uploadedImageSecondary,
                _this.uploadedImageSecondaryOffsetX,
                _this.uploadedImageSecondaryOffsetY,
                _this.uploadedImageSecondary.width / _this.uploadedImageSecondaryScaledown,
                _this.uploadedImageSecondary.height / _this.uploadedImageSecondaryScaledown);
        }
        // enable mask mode
        ctxCard.globalCompositeOperation = 'destination-out';

        // draw masks if any
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":checked") && $(el).is(":disabled") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( maskMap.maskElements[maskElementKey].maskIMG ) {
                    ctxCard.drawImage(maskMap.maskElements[maskElementKey].maskIMG, 0, 0, resultCanvas.width, resultCanvas.height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });

        // ----------------------------------------------------------------------------------

        var ctxArt = _this.clearResultCanvas();

        ctxArt.globalCompositeOperation = 'source-over';

        if( _this.uploadedImage && _this.uploadedImageIsValid ) {
            if( _this.$refs.uploadDesignMaskTransparent.prop("checked") ) {
                ctxArt.fillStyle = _this.uploadedImageColor;
                ctxArt.fillRect(0, 0,
                    resultCanvas.width,
                    resultCanvas.height);
            }
            ctxArt.drawImage(_this.uploadedImage,
                _this.uploadedImageOffsetX, _this.uploadedImageOffsetY,
                _this.uploadedImage.width / _this.uploadedImageScaledown,
                _this.uploadedImage.height / _this.uploadedImageScaledown);
        } else if( _this.$refs.uploadDesignMaskTransparent.prop("checked") ) {
            //draw only the mask
            ctxArt.fillStyle = _this.uploadedImageColor;
            ctxArt.fillRect(0, 0,
                resultCanvas.width,
                resultCanvas.height);
        }

        // enable mask mode
        ctxArt.globalCompositeOperation = 'destination-in';

        // draw masks if any
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":checked") && $(el).is(":disabled") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( maskMap.maskElements[maskElementKey].maskIMG ) {
                    ctxArt.drawImage(maskMap.maskElements[maskElementKey].maskIMG, 0, 0, resultCanvas.width, resultCanvas.height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });
        ctxCard.globalCompositeOperation = 'destination-over';
        ctxCard.drawImage(resultCanvas, 0, 0);
        // save the result to an invisible input in the form
        _this.$refs.resultPNGDataInput.val(resultCardCanvas.toDataURL("image/png"));
        ctxArt.clearRect(0, 0,
            _this.$refs.resultCanvas.get(0).width,
            _this.$refs.resultCanvas.get(0).height);
        ctxCard.clearRect(0, 0,
            _this.$refs.resultCanvas.get(0).width,
            _this.$refs.resultCanvas.get(0).height);
    }
}

/**
 * Renders the work in progress in a preview canvas with the preview card set as a bg image
 */
Tool.prototype.renderPreviewCanvas = function() {
    if( this.allImgsLoaded === 0 ) {
        return false;
    }
    var _this = this;
    // there is a chosen frameCode
    if( _this.chosenFrameCodeKey !== -1 && _this.input.maskMaps[_this.chosenFrameCodeKey].exampleCardIMG ) {
        // calculate size of the previewcanvas based on the preview card dimentions and ratio
        var previewImgWidth = _this.input.maskMaps[_this.chosenFrameCodeKey].exampleCardIMG.width;
        var previewImgHeight = _this.input.maskMaps[_this.chosenFrameCodeKey].exampleCardIMG.height;
        var ratio = previewImgWidth / previewImgHeight;

        // set size - at most to be 30 of the viewport width and calculate the height so it fits with the preview card ratio
        var previewCanvasWidth = 240;
        previewCanvasWidth = previewCanvasWidth > 350 ? 400 : previewCanvasWidth;
        var previewCanvasHeight = parseInt(previewCanvasWidth / ratio);

        // calculate the scaledown factor of the previewcanvas - how many time smaller will you draw everything so it can fit on the screen for preview
        _this.previewCanvasScaledown = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasWidth / previewCanvasWidth;

        // set the size of the preview canvas
        _this.$refs.previewCanvas.get(0).width = previewCanvasWidth;
        _this.$refs.previewCanvas.get(0).height = previewCanvasHeight;
        _this.$refs.previewCanvas.width(previewCanvasWidth);
        _this.$refs.previewCanvas.height(previewCanvasHeight);

        // set the size of the out of bounds canvas
        _this.$refs.previewOutOfBoundsCanvas.get(0).width = _this.$refs.previewOutOfBoundsCanvas.width();
        _this.$refs.previewOutOfBoundsCanvas.get(0).height = _this.$refs.previewOutOfBoundsCanvas.height();

        // get the drawing context of the preview canvas
        var ctx = _this.clearPreviewCanvas();

        // get the drawing context of the out of bounds canvas
        var outOfBoundsCtx = _this.$refs.previewOutOfBoundsCanvas.get(0).getContext("2d");

        // add the preview card as a bg image filling it 100% 100%
        _this.$refs.previewCanvas.css("background-size", "100% 100%");
        _this.$refs.previewCanvas.css("background-repeat", "no-repeat");
        _this.$refs.previewCanvas.css("background-image", "url('" + _this.input.maskMaps[_this.chosenFrameCodeKey].exampleCardPNGURL + "')");

        // if there is an uploaded (design) image
        if( _this.uploadedImage && _this.uploadedImageIsValid ) {
            // clear out of bounds canvas from previous drawings
            outOfBoundsCtx.clearRect(0, 0,
                _this.$refs.previewCanvas.get(0).width,
                _this.$refs.previewCanvas.get(0).height);

            // make out of bounds canvas semi transparent
            outOfBoundsCtx.globalAlpha = 0.3;

            if(!_this.uploadedImageInInit) {
				// calculate the relative offset of the out of bounds canvas
				var outOfBoundsOffsetX = _this.$refs.previewCanvas.offset().left - _this.$refs.previewOutOfBoundsCanvas.offset().left;
				var outOfBoundsOffsetY = _this.$refs.previewCanvas.offset().top - _this.$refs.previewOutOfBoundsCanvas.offset().top;

				// draw design img in the out of bounds canvas as well
				// @TODO: check next function - I disabled it for page init loading for now (Sergey)
				outOfBoundsCtx.drawImage(_this.uploadedImage, _this.uploadedImageOffsetX / _this.previewCanvasScaledown + outOfBoundsOffsetX, _this.uploadedImageOffsetY / _this.previewCanvasScaledown + outOfBoundsOffsetY, _this.uploadedImage.width / _this.previewCanvasScaledown / _this.uploadedImageScaledown, _this.uploadedImage.height / _this.previewCanvasScaledown / _this.uploadedImageScaledown);
			}

            //draw the image mask
            if( _this.$refs.uploadDesignMaskTransparent.prop("checked") ) {
                ctx.fillStyle = _this.uploadedImageColor;
                ctx.fillRect(0, 0,
                    _this.$refs.previewCanvas.get(0).width,
                    _this.$refs.previewCanvas.get(0).height);
            }
            // draw design img
            ctx.drawImage(_this.uploadedImage,
                _this.uploadedImageOffsetX / _this.previewCanvasScaledown,
                _this.uploadedImageOffsetY / _this.previewCanvasScaledown,
                _this.uploadedImage.width / _this.previewCanvasScaledown / _this.uploadedImageScaledown,
                _this.uploadedImage.height / _this.previewCanvasScaledown / _this.uploadedImageScaledown);

        } else if( _this.$refs.uploadDesignMaskTransparent.prop("checked") ) {
            //draw only the mask
            ctx.fillStyle = _this.uploadedImageColor;
            ctx.fillRect(0, 0,
                _this.$refs.previewCanvas.get(0).width,
                _this.$refs.previewCanvas.get(0).height);
        }
        // enable mask mode
        ctx.globalCompositeOperation = 'destination-out';

        // draw all masks
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":checked") && !$(el).is(":disabled") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG ) {
                    ctx.drawImage(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG, 0, 0, _this.$refs.previewCanvas.get(0).width, _this.$refs.previewCanvas.get(0).height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });

        ctx.globalCompositeOperation = 'source-over';
        ctx.globalAlpha = 0.8;
        // draw all masks
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":hover") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG ) {
                    ctx.drawImage(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG, 0, 0, _this.$refs.previewCanvas.get(0).width, _this.$refs.previewCanvas.get(0).height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });

        if( _this.uploadedImageSecondary && _this.uploadedImageSecondaryIsValid ) {
            ctx.globalAlpha = 1;
            //draw mask if not transparent
            if( _this.$refs.uploadSecondaryMaskTransparent.prop("checked") ) {
                ctx.fillStyle = _this.uploadedImageSecondaryColor;
                ctx.fillRect(_this.uploadedImageSecondaryOffsetX / _this.previewCanvasScaledown,
                    _this.uploadedImageSecondaryOffsetY / _this.previewCanvasScaledown,
                    _this.uploadedImageSecondary.width / _this.previewCanvasScaledown / _this.uploadedImageSecondaryScaledown,
                    _this.uploadedImageSecondary.height / _this.previewCanvasScaledown / _this.uploadedImageSecondaryScaledown);
            }

            // draw Secondary img
            ctx.drawImage(_this.uploadedImageSecondary,
                _this.uploadedImageSecondaryOffsetX / _this.previewCanvasScaledown,
                _this.uploadedImageSecondaryOffsetY / _this.previewCanvasScaledown,
                _this.uploadedImageSecondary.width / _this.previewCanvasScaledown / _this.uploadedImageSecondaryScaledown,
                _this.uploadedImageSecondary.height / _this.previewCanvasScaledown / _this.uploadedImageSecondaryScaledown);
        }

        // enable mask mode
        ctx.globalCompositeOperation = 'destination-out';
        // draw all only locked layers
        _this.$refs.maskElementsOptions.find("input").each(function( key, el ) {
            if( $(el).is(":checked") && $(el).is(":disabled") ) {
                var maskElementKey = parseInt(jQuery(el).attr("data-key"));
                if( _this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG ) {
                    ctx.drawImage(_this.input.maskMaps[_this.chosenFrameCodeKey].maskElements[maskElementKey].maskIMG, 0, 0, _this.$refs.previewCanvas.get(0).width, _this.$refs.previewCanvas.get(0).height);
                } else {
                    console.warn("Mask Image not loaded for Mask Element #" + maskElementKey)
                }
            }
        });
    }
}

//color picker
Tool.prototype.attachUploadDesignSecondaryColorListener = function() {
    var _this = this;
    _this.$refs.uploadDesignSecondaryColor.off();
    _this.$refs.uploadDesignSecondaryColor.on("change", function( event ) {
        if( _this.uploadedImageSecondaryColor !== event.target.value ) {
            _this.uploadedImageSecondaryColor = event.target.value;
            _this.renderPreviewCanvas();
        }
    })
}

// color picker
Tool.prototype.attachUploadDesignColorListener = function() {
    var _this = this;
    _this.$refs.uploadDesignColor.off();
    _this.$refs.uploadDesignColor.on("change", function( event ) {
        if( _this.uploadedImageColor !== event.target.value ) {
            _this.uploadedImageColor = event.target.value;
            _this.renderPreviewCanvas();
        }
    })
}

//check box to make it transparent
Tool.prototype.attachUploadDesignMaskTransparentListener = function() {
    var _this = this;
    _this.$refs.uploadDesignMaskTransparent.off();
    _this.$refs.uploadDesignMaskTransparent.on("change", function( event ) {
        _this.renderPreviewCanvas();
    })
}

//check box to make it transparent
Tool.prototype.attachUploadSecondaryMaskTransparent = function() {
    var _this = this;
    _this.$refs.uploadSecondaryMaskTransparent.off();
    _this.$refs.uploadSecondaryMaskTransparent.on("change", function( event ) {
        _this.renderPreviewCanvas();
    })
}

//remove image
Tool.prototype.attachRemoveSecondaryListener = function() {
    var _this = this;
    _this.$refs.removeSecondary.off();
    _this.$refs.removeSecondary.on("click", function( event ) {

        _this.uploadedImageSecondaryIsValid = false;
        _this.uploadedImageSecondary = null;
        _this.uploadedImageSecondaryOffsetX = 0;
        _this.uploadedImageSecondaryOffsetY = 0;
        _this.uploadedImageSecondaryScaledown = 1;
        _this.uploadedImageSecondaryMaxScaledown = 1;

        _this.$refs.chooseImage.find("option[value='secondary']").remove();
        _this.$refs.uploadDesignInputSecondary.val('');

        _this.$refs.uploadDesignInputSecondary.siblings('.mc_file_input_label').first().text("No file chosen");
        _this.renderPreviewCanvas();

    })
}

//remove image
Tool.prototype.attachRemoveImageListener = function() {
    var _this = this;
    _this.$refs.removeImage.off();
    _this.$refs.removeImage.on("click", function( event ) {

        _this.uploadedImageIsValid = false;
        _this.uploadedImage = null;
        _this.uploadedImageOffsetX = 0;
        _this.uploadedImageOffsetY = 0;
        _this.uploadedImageScaledown = 1;
        _this.uploadedImageMaxScaledown = 1;

        _this.$refs.chooseImage.find("option[value='design']").remove();
        _this.$refs.uploadDesignInput.val('').trigger('change');

        _this.renderPreviewCanvas();

    })
}

//upload of the Secondary image
Tool.prototype.attachUploadSecondaryListener = function() {
    var _this = this;
    // off from earlier binds in case the function is called multiple times
    _this.$refs.uploadDesignInputSecondary.off();
    _this.$refs.uploadDesignInputSecondary.on("change", function( event ) {
        // if no files are attached stop
        if( event.target.files.length === 0 )
            return;

        // reset the validity of the inputImage
        _this.uploadedImageSecondaryIsValid = false;
        // load the design image and store a pointer in a class variable for later drawing in canvas
        _this.uploadedImageSecondary = _this.loadImage(URL.createObjectURL(event.target.files[0]));

        // when the design image is loaded
        _this.uploadedImageSecondary.onload = function( event ) {

            // mark the design image as valid
            _this.uploadedImageSecondaryIsValid = true;

            if( _this.$refs.chooseImage.find("option[value='secondary']").length === 0 ) {
                _this.$refs.chooseImage.append("<option value='secondary' selected>Secondary</option>");
            } else {
                _this.$refs.chooseImage.find("option[value='secondary']").prop("selected", "true");
            }

            // reset any offsets and zooms
            _this.uploadedImageSecondaryOffsetX = 5;
            _this.uploadedImageSecondaryOffsetY = _this.input.maskMaps[_this.chosenFrameCodeKey].canvasHeight - _this.uploadedImageSecondary.height - 5;
            _this.uploadedImageSecondaryScaledown = 1;

            _this.renderPreviewCanvas();

        };

    });
}

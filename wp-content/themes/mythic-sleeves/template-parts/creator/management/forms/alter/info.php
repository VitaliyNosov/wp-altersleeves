<div id="info-block" class="col-md-3 bg-grey pt-3 d-none d-md-block bg-light position-fixed" style="right:0;top:42px;overflow-y:scroll;">
    <h2>Field information</h2>
    <p><small>For details on Design data structure, <a class="fw-bold" href="<?= AS_URI_IMG.'/diagrams/designs.png' ?>" target="_blank">click
                here</a></small></p>
    <hr>

    <!-- Tab 1 - Design Setup -->
    <div id="info_design_setup" class="is-info d-none">
        <h3>Design Setup</h3>

        <p>Determines whether the artwork you're uploading is completely new or is a variation of something you have previously uploaded</p>

        <ul>
            <li><strong>A new design</strong> - the file I want to upload is either brand new and I haven't uploaded anything like it previously, or
                it is an artistic variation on something I've previously uploaded (ie different colour scheme)
            </li>
            <li><strong>A cropping or printing variation for an existing design</strong> - the file I want to upload is a crop or print variation of
                an existing design
            </li>
        </ul>
    </div>

    <!-- Tab 1 - Design Name -->
    <div id="info_design_name" class="is-info d-none">
        <h3>Design Name</h3>

        <p>The design name will be used for to search for it in the back end when adding variations and on your royalty statements.</p>

        <p>Unfortunately design names are not currently customer facing due to legal complications regarding name usage, though this is something we
            hope to solve in the future.</p>
    </div>

    <!-- Tab 1 - Crop Type -->
    <div id="info_crop_type" class="is-info d-none">
        <h3>Crop Type</h3>

        <p>The crop type is type of cut you have applied (or want to apply using our cutting tool) to the file you are uploading.</p>

        <p>Crop Type definitions can be <a href="https://docs.google.com/document/d/17ECfwVPSOmUZhciz-Sk0rJ2P77MR-9qIH4miYzKyijU" target="_blank">found
                here</a>.</p>
    </div>

    <!-- Tab 1 - Generic Design -->
    <div id="info_design_generic" class="is-info d-none">
        <h3>Generic Design</h3>

        <p>Selecting this checkbox means that this design will appear in generic search results for all cards with a matching frame set of your
            selected printing.</p>

        <p>Frames and generic adornments are considered generic in all situations. For Art replacements, you may choose.</p>
    </div>

    <!-- Tab 1 - Design Availablity -->
    <div id="info_design_availability" class="is-info d-none">
        <h3>Design Availability</h3>

        <p>The availability of your design on the Alter Sleeves website.</p>

        <ul>
            <li><strong>Available in Store</strong> - Users can search for and purchase your design</li>
            <li><strong>For internal use only</strong> - Only you and administrators can access your design. It is not searchable.</li>
            <li><strong>For purchase only</strong> - Your design is not searchable but can be purchased; accessible directly via link (coming soon)
            </li>
            <li><strong>Visible on site, not available for purchase</strong> - Your design is searchable but can not be purchased (coming soon)</li>
        </ul>
    </div>

    <!-- Tab 2 - Card Search -->
    <div id="info_card_search" class="is-info d-none">
        <h3>Card Search</h3>

        <p>Search for a card by name. If there are results, they will display below.</p>

        <p>Select a card from the results by clicking on the name. To undo your selection, press the 'X'.</p>
    </div>

    <!-- Tab 2 - Printing Selection -->
    <div id="info_printing_selection" class="is-info d-none">
        <h3>Printing Selection</h3>
        <p>Select a printing from the options. Options are divided into all available printings of a card that has been released physically.</p>

        <p>If you have selected an art replacement type, it will select all versions with the matching framecode. If not you must select individually
            due to color differences between printings.</p>

        <p>When you select a printing, it will grey out other versions as the variation must have consistency of printing layout.</p>
    </div>

    <!-- Tab 3 - MC_Cutter Locked -->
    <div id="info_cutter" class="is-info d-none">
        <h3>The Cutter/Submitter</h3>
        <p>The uploading and cutting tool. If we have mapped the card, you can use this tool to 'cut' out parts of your design as part of the
            submission process.</p>
        <p>More info coming soon.</p>
    </div>

    <!-- Tab 4 - Tags -->
    <div id="info_tags" class="is-info d-none">
        <h3>Tag Selection</h3>
        <p>Select tags that are relevant to your design. These can be used by customers to browse to your designs based on theme or aesthetic, rather
            than via cards and printings.</p>
    </div>

</div>
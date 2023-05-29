<div id="field-design-id" class="field-wrapper">
    <div class="form-row align-items-end">
        <div class="col">
            <label class="form-label" for="field-design-id">Know your design or alter ID?</label>
            <input id="field-design-id" class="form-control" autocomplete="off" type="text" placeholder="Enter it here: 123456" name="design_id"
                   required value="">
        </div>
        <div class="col-auto">
            <button id='trigger-design-id' type="button" class="btn btn-primary cas-button--blue m-0">Add Design
        </div>
    </div>
</div>
<p>You can find your design IDs <a href="/dashboard/manage-designs" target="_blank">here</a> or your alter IDs <a href="/dashboard/manage-alters"
                                                                                                                  target="_blank">here</a></p>

<?php return; ?>

<hr>
<div id="field-design-search" class="field-wrapper remove-design-search">
    <div class="form-row  align-items-end">
        <div class="col">
            <label class="form-label" for="cardSearch">Search by design name</label>
            <input id="field-design-search" class="form-control" autocomplete="off" type="text" placeholder="For example: Blazing Mountain"
                   name="design_search" required value="">
        </div>
        <div class="col-auto">
            <button id='trigger-design-search' type="button" class="btn btn-primary cas-button--blue m-0">Search designs
        </div>
    </div>
</div>
<hr>
<p>If you don't know your design ID, use the search below:</p>
<div id="field-design-type" class="field-wrapper mb-3">
    <h2>Design Type</h2>
    <select class="form-select form-select-md mb-3" name="alter_type">
        <option value="0" selected>--- Please Select ---</option>
        <option value="art_replacement">Art Replacement</option>
        <option value="card">Card</option>
        <option value="frame">Frame</option>
    </select>
</div>
<div id="field-card-search" class="field-wrapper">
    <h2>Select a card</h2>
    <div class="form-row align-items-center">
        <div class="col">
            <label class="sr-only" for="cardSearch">Find a card</label>
            <input id="cardSearch" autocomplete="off" class="form-control" type="text" placeholder="Enter card name" name="card_search">
        </div>
        <div class="col-auto">
            <div id="loading-card-search" class="loading-inline" style="display: none;"></div>
        </div>
    </div>
</div>
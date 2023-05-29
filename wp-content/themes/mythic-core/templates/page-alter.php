<?php
/**
 * Template Name: What is Alter Sleeve
 */

get_header(); ?>
    <div id="alter-sleeve" class="background-browsing">
        <div class="wing bg-blue"></div>
        <div class="container content bg-white-content py-0">

            <div class="text-center my-3 position-fixed" style="top:40px;left:0;width:100%;z-index: 99999;"><img
                        src="/wp-content/themes/mythic-sleeves/src/img/logo/header@2x.png"
                        style="width:250px;background: #313131;border-radius: 5px;padding: 10px;/* z-index: 99999; *//* z-index: 99999; */-webkit-box-shadow: 0 3px 13px 0 rgba(30, 30, 30, 0.75);-moz-box-shadow: 0 1px 4px 0 rgba(30, 30, 30, 0.75);box-shadow: 0 1px 4px 0 rgba(30, 30, 30, 0.75);">
            </div>
            
            <?php
            if( have_posts() ) : while( have_posts() ) : the_post(); ?>
                <div class="row align-items-end">
                    <div class="col">
                        <?= do_shortcode( '[example_alters]' ) ?>
                    </div>

                    <div class="col-auto" style="width:250px;">
                        <?php the_content(); ?>
                    </div>
                </div>
                <hr>
                <h2 class="text-center">ALTERS PRINTED ON PERFECT FIT INNER SLEEVES</h2>

                <br><br><br><br><br><br>
                <h3 class="mt-4 mb-3">Select Demo Alter</h3>
                <div class="row mt-3">
                    <div class="col-auto">
                        <select class="form-control" id="demo-type" required="">
                            <option value="" data-generic="0">--- Select crop type ---</option>
                            <option value="26150" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement -
                                Adornment
                            </option>
                            <option value="26153" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement -
                                Borderless Extension
                            </option>
                            <option value="26155" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement -
                                Crop-out
                            </option>
                            <option value="26151" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement -
                                Extension
                            </option>
                            <option value="26156" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement - Floating
                                Border Extension
                            </option>
                            <option value="26157" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement - Full
                                Extension
                            </option>
                            <option value="26160" data-generic="0" data-generic-selected="0" data-transferable="0">Art Enhancement - Texbox
                            </option>
                            <option value="26161" data-generic="1" data-generic-selected="0" data-transferable="1">Art Replacement - Artbox
                                Replacement
                            </option>
                            <option value="26159" data-generic="1" data-generic-selected="0" data-transferable="1">Art Replacement -
                                Extension
                            </option>
                            <option value="26158" data-generic="1" data-generic-selected="0" data-transferable="1">Art Replacement - Full
                            </option>
                            <option value="26164" data-generic="0" data-generic-selected="1" data-transferable="1">Frame</option>
                            <option value="35866" data-generic="0" data-generic-selected="1" data-transferable="1">Generic Adornment</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" id="demo-id" placeholder="ID: 12345" style="    padding: .375rem .75rem;">
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" id="demo-submit"
                           class="btn btn-primary blue--button p-1 mx-2">LOAD</a>
                    </div>
                </div>
                <hr>
            
            <?php
            endwhile; endif;
            ?>
            <br>
        </div>
        <div class="wing bg-blue"></div>
    </div>
    <style>
        #atlwdg-trigger,
        div.bg-danger,
        header {
            display: none;
        }
    </style>
<?php

get_footer();

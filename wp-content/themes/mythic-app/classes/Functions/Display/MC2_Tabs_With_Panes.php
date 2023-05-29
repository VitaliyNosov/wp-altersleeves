<?php

namespace Mythic\Functions\Display;

/**
 * Class MC2_Tabs_With_Panes
 *
 * @package Mythic\Functions\Display
 */
class MC2_Tabs_With_Panes {

    /**
     * Renders section content
     *
     * @param $tabs_info
     * @param $args
     */
    public static function mcTabsRender( $tabs_info, $args = [] ) {
        static::mcTabsRenderNav( $tabs_info );
        static::mcTabsRenderContent( $tabs_info, $args );
    }

    /**
     * Renders Tabs nav
     *
     * @param $tabs_info
     */
    public static function mcTabsRenderNav( $tabs_info ) { ?>
        <ul class="nav nav-tabs mc-af-header">
            <?php foreach( $tabs_info as $tabs_info_key => $tabs_info_single ) { ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo !empty( $tabs_info_single['active'] ) ? 'active' : '' ?>"
                       data-bs-toggle="tab"
                       href="#<?php echo $tabs_info_key ?>"><?php echo $tabs_info_single['title'] ?></a>
                </li>
            <?php } ?>
        </ul>
    <?php }

    /**
     * Renders Panes content
     *
     * @param $tabs_info
     * @param $args
     */
    public static function mcTabsRenderContent( $tabs_info, $args ) { ?>
        <div class="tab-content mc-af-content">
            <?php foreach( $tabs_info as $tabs_info_key => $tabs_info_single ) { ?>
                <div class="tab-pane fade <?php echo !empty( $tabs_info_single['active'] ) ? $tabs_info_key.' show active' : $tabs_info_key ?>"
                     id="<?php echo $tabs_info_key ?>">
                    <?php MC2_Render::templatePart( $tabs_info_single['content']['slug'], $tabs_info_single['content']['name'], $args ); ?>
                </div>
            <?php } ?>
        </div>
    <?php }

}


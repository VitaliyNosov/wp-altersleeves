<?php

namespace Mythic\Functions\Display;

use Mythic\Helpers\MC2_Pagination;

/**
 * Class MC2_Table_Flex
 *
 * @package Mythic\Functions\Display
 */
class MC2_Table_Flex {

    /**
     * Renders tableFlex
     *
     * @param        $data
     * @param array  $header_data
     * @param string $classes
     * @param array  $pagination
     */
    public static function renderTableFlex( $data, $header_data = [], $classes = '', $pagination = [] ) {
        echo static::generateTableFlex( $data, $header_data, $classes, $pagination );
    }

    /**
     * Generates "table" - HTML content realized via flexbox
     *
     * @param        $data
     * @param array  $header_data
     * @param string $classes
     * @param array  $pagination
     *
     * @return false|string
     */
    public static function generateTableFlex( $data, $header_data = [], $classes = '', $pagination = [] ) {
        $td_count = !empty( $data ) ? count( $data[0] ) : 0;
        if( !empty( $td_count ) && isset( $data[0]['mc_striked'] ) ) $td_count--;
        if( !empty( $td_count ) && isset( $data[0]['mc_highlighted'] ) ) $td_count--;
        ob_start(); ?>
        <div class="mc-table-flex-container <?php echo $classes ?>">
            <?php

            if( !empty( $pagination ) ) {
                [ $pagination_count, $pagination_per_page, $pagination_align, $pagination_ajax ] = $pagination;
                MC2_Pagination::asDisplayPagination( $pagination_count, $pagination_per_page, $pagination_align, $pagination_ajax );
            }

            if( !empty( $header_data ) ) {
                $td_count = !empty( $td_count ) ? $td_count : count( $header_data ) ?>
                <div class="mc-table-flex-row mc-table-flex-header">
                    <?php

                    foreach( $header_data as $header_data_key => $header_data_single ) { ?>
                        <div class="mc-table-flex-td mc-table-flex-td-<?php echo $td_count ?> mc-table-flex-td-key-<?php echo $header_data_key ?>">
                            <p><?php echo $header_data_single ?></p>
                        </div>
                    <?php }
                    ?>
                </div>
            <?php }
            if( !empty( $data ) ) {
                foreach( $data as $data_key => $data_single ) {
                    $mc_striked = '';
                    if( isset( $data_single['mc_striked'] ) ) {
                        $mc_striked = !empty( $data_single['mc_striked'] ) ? 'mc_striked' : '';
                        unset( $data_single['mc_striked'] );
                    }
                    $mc_highlighted = '';
                    if( isset( $data_single['mc_highlighted'] ) ) {
                        $mc_highlighted = !empty( $data_single['mc_highlighted'] ) ? 'mc_highlighted' : '';
                        unset( $data_single['mc_highlighted'] );
                    } ?>
                    <div class="mc-table-flex-row <?php echo $mc_striked.' '.$mc_highlighted ?>">
                        <?php

                        foreach( $data_single as $data_single_key => $data_single_single ) { ?>
                            <div class="mc-table-flex-td mc-table-flex-td-<?php echo $td_count ?> mc-table-flex-td-key-<?php echo $data_single_key ?>">
                                <p><?php echo $data_single_single ?></p>
                            </div>
                        <?php } ?>
                    </div>
                <?php }
            } else { ?>
                <div class="mc-table-flex-row">
                    <p>Here's no results now</p>
                </div>
            <?php } ?>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * @param       $filter_data
     * @param int   $columns_count
     * @param array $classes
     * @param array $additional_settings
     */
    public static function renderTableFlexFilters( $filter_data, $columns_count = 0, $classes = [], $additional_settings = [] ) {
        echo static::generateTableFlexFilter( $filter_data, $columns_count, $classes, $additional_settings );
    }

    /**
     * Additional settings:
     * select2
     * select2multiple
     *
     * @param       $filter_data
     * @param int   $columns_count
     * @param array $container_classes
     * @param array $additional_settings
     *
     * @return false|string
     */
    public static function generateTableFlexFilter( $filter_data, $columns_count = 0, $container_classes = [], $additional_settings = [] ) {
        if( empty( $filter_data ) ) return '';
        $td_count = !empty( $columns_count ) ? $columns_count : count( $filter_data );
        ob_start(); ?>
        <div class="mc-table-flex-row mc-table-flex-filter <?php echo !empty( $container_classes ) ? implode( ' ', $container_classes ) : '' ?>">
            <?php

            foreach( $filter_data as $filter_data_key => $filter_data_single ) {
                if( empty( $filter_data_single ) || count( $filter_data_single ) == 1 ) continue;
                $filter_classes = '';
                if( !empty( $additional_settings[ $filter_data_key ]['select2'] ) ) {
                    $filter_classes .= 'mc_select2';
                }
                if( !empty( $additional_settings[ $filter_data_key ]['select2multiple'] ) ) {
                    $filter_classes .= 'mc_select2_multiple';
                }
                ?>
                <div class="mc-table-flex-td mc-table-flex-td-<?php echo $td_count ?> mc-table-flex-td-key-<?php echo $filter_data_key ?>">
                    <select name="mc-table-flex-filter-<?php echo $filter_data_key ?>"
                            class="mc-table-flex-filter-<?php echo $filter_data_key ?> <?php echo $filter_classes ?>"
                            data-mc-placeholder="Filter by <?php echo $filter_data_key ?>">
                        <?php if( empty( $additional_settings[ $filter_data_key ]['select2multiple'] ) ) { ?>
                            <option value="">Filter by <?php echo $filter_data_key ?></option>
                        <?php } ?>
                        <?php foreach( $filter_data_single as $select_option_key => $select_option ) { ?>
                            <option value="<?php echo $select_option_key ?>"><?php echo $select_option ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php }
            ?>
        </div>
        <?php

        return ob_get_clean();
    }

}
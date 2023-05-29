<?php

if( empty( $tab ) ) $tab = '';
?>

<div class="wrap">
    <?php if( !empty( $title ) ) : ?>
        <h1><?= $title ?></h1>
    <?php endif; ?>
    <?php if( !empty( $setting_list ) && !empty( $menu_slug ) ) : ?>
        <h2 class="nav-tab-wrapper">
            <?php foreach( $setting_list as $section_id => $data ) : ?>
                <a href="<?php echo add_query_arg( [
                                                       'page' => $menu_slug,
                                                       'tab'  => $section_id,
                                                   ], admin_url( 'admin.php' ) ); ?>" class="nav-tab<?php if( $tab == $section_id || $tab === '' ) {
                    $tab = $tab === '' ? null : $tab;
                    echo ' nav-tab-active';
                } ?>"><?php esc_html_e( $data['title'] ); ?></a>
            <?php endforeach; ?>
        </h2>
    <?php endif; ?>
    <?php if( !empty( $setting_group ) && !empty( $setting_list ) ) : ?>
        <form method="post" action="options.php" class="form-<?php echo $menu_slug ?>">
            <?php
            // This prints out all hidden setting fields
            settings_fields( $setting_group );
            foreach( $setting_list as $section_id => $data ) :
                $section_display = 'style="display:none;"';
                if( $tab == $section_id || $tab === null ) {
                    $tab             = '';
                    $section_display = '';
                }
                ?>
                <table class="form-table" <?php echo $section_display; ?>>
                    <tr>
                        <th colspan="2">
                            <?php

                            if( !empty( $data['title'] ) ) echo '<h2>'.$data['title'].'</h2>';
                            if( !empty( $data['intro'] ) ) echo '<p>'.$data['intro'].'</p>'; ?></th>
                    </tr>
                    <?php if( !empty( $menu_slug ) ) {
                        do_settings_fields( $menu_slug, $section_id );
                    } ?>
                </table>
            <?php endforeach;
            submit_button();
            ?>
        </form>
    <?php endif; ?>
</div>

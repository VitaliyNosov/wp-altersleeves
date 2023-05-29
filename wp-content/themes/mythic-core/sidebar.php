<?php

$sidebar = apply_filters( 'mc_sidebar_filter', 'sidebar' );

if( !is_active_sidebar( $sidebar ) ) return;
dynamic_sidebar( $sidebar );

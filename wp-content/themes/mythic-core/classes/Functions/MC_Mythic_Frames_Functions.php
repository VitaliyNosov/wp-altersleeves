<?php

namespace Mythic_Core\Functions;

use Dompdf\Dompdf;
use Exception;
use Intervention\Image\ImageManagerStatic;
use MC_Alter_Functions;
use MC_Vars;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\Objects\MC_User;
use Mythic_Core\System\MC_Access;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Url;
use WP_Post;
use ZipArchive;

/**
 * Class MC_Mythic_Frames_Functions
 *
 * @package Mythic_Core\Functions
 */
class MC_Mythic_Frames_Functions {
    
    public static function rand_printings( $save = true ) {
        $printings = [
            'creatures'    => self::creatures(),
            'noncreatures' => self::noncreatures(),
            'plains'       => self::plains(),
            'mountain'     => self::mountain(),
            'island'       => self::island(),
            'swamp'        => self::swamp(),
            'forest'       => self::forest(),
        ];
        if( $save ) update_option( 'mc_rand_printings', $printings );
        
        return $printings;
    }
    
    /**
     * @return array|int[]|WP_Post[]
     */
    public static function creatures() {
        $eras = [ 2015 => 0, 2003 => 0, 1997 => 0 ];
        foreach( $eras as $era_key => $era ) {
            $printingArgs = [
                'post_type'      => 'printing',
                'post_status'    => [ 'publish' ],
                'posts_per_page' => 300,
                'orderby'        => 'rand',
                'meta_query'     => [
                    [
                        'key'   => 'mc_frame',
                        'value' => $era_key,
                    ],
                ],
                'fields'         => 'ids',
            ];
            $printings    = get_posts( $printingArgs );
            foreach( $printings as $printing_id ) {
                $type_line = get_post_meta( $printing_id, 'mc_type_line', true );
                if( strpos( $type_line, 'Creature' ) === false || strpos( $type_line, 'Token' ) !== false ) continue;
                
                $printing       = new MC_Mtg_Printing( $printing_id );
                $printing_image = $printing->imgPng;
                if( $printing_image == MC_Mtg_Card_Functions::unavailableCardImg() || empty( $printing_image ) ) continue;
                $eras[ $era_key ] = $printing_id;
                break;
            }
        }
        
        return $eras;
    }
    
    /**
     * @return array|int[]|WP_Post[]
     */
    public static function noncreatures() {
        $eras = [ 2015 => 0, 2003 => 0, 1997 => 0 ];
        foreach( $eras as $era_key => $era ) {
            $printingArgs = [
                'post_type'      => 'printing',
                'post_status'    => [ 'publish' ],
                'posts_per_page' => 300,
                'orderby'        => 'rand',
                'meta_query'     => [
                    [
                        'key'   => 'mc_frame',
                        'value' => $era_key,
                    ],
                ],
                'fields'         => 'ids',
            ];
            $printings    = get_posts( $printingArgs );
            foreach( $printings as $printing_id ) {
                $type_line = get_post_meta( $printing_id, 'mc_type_line', true );
                if( !in_array( $type_line, self::noncreature_typelines() ) ) continue;
                
                $printing       = new MC_Mtg_Printing( $printing_id );
                $printing_image = $printing->imgPng;
                if( $printing_image == MC_Mtg_Card_Functions::unavailableCardImg() || empty( $printing_image ) ) continue;
                
                $eras[ $era_key ] = $printing_id;
                break;
            }
        }
        
        return $eras;
    }
    
    /**
     * @return array
     */
    public static function noncreature_typelines() {
        $typelines     = MC_Mtg_Printing_Functions::typeLineTranslations();
        $typelines_non = [];
        foreach( $typelines as $typeline_key => $typeline ) {
            switch( $typeline ) {
                case MC_Mtg_Printing_Functions::TYPE_LINE_NON_CREATURE :
                    $typelines_non[] = $typeline_key;
                    break;
            }
        }
        
        return $typelines_non;
    }
    
    /**
     * @return int|WP_Post
     */
    public static function plains() {
        return self::land( 'Plains' );
    }
    
    /**
     * @param string $type
     *
     * @return int|WP_Post
     */
    public static function land( $type = '' ) {
        if( empty( $type ) ) return 0;
        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 1,
            'orderby'        => 'rand',
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'mtg_card',
                    'field'    => 'name',
                    'terms'    => $type,
                ],
            ],
            'meta_query'     => [
                [
                    'key'   => 'mc_frame',
                    'value' => 2015,
                ],
                [
                    'key'     => 'mc_full_art',
                    'compare' => '!=',
                    'value'   => 1,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts( $printingArgs );
        
        return $printings[0] ?? 0;
    }
    
    /**
     * @return int|WP_Post
     */
    public static function mountain() {
        return self::land( 'Mountain' );
    }
    
    /**
     * @return int|WP_Post
     */
    public static function island() {
        return self::land( 'Island' );
    }
    
    /**
     * @return int|WP_Post
     */
    public static function swamp() {
        return self::land( 'Swamp' );
    }
    
    /**
     * @return int|WP_Post
     */
    public static function forest() {
        return self::land( 'Forest' );
    }
    
    public static function update_ks_image( $team_id = 0 ) {
        if( !MC_Access::live() ) return;
        
        $teams = self::sorted_teams();
        
        $mythic_frames_dir        = ABSPATH.'files/mythic-frames';
        $mythic_frames_sorted_dir = ABSPATH.'files/mf-generated';
        if( !is_dir( $mythic_frames_dir ) ) {
            mkdir( $mythic_frames_dir, 0755, true );
        }
        if( !is_dir( $mythic_frames_sorted_dir ) ) {
            mkdir( $mythic_frames_sorted_dir, 0755, true );
        }
        
        $count = 0;
        $all   = empty( $team_id );
        foreach( $teams as $id => $team ) {
            $count++;
            if( !empty( $team_id ) and $team_id != $id ) continue;
            
            $alterist_img         = $team['alterist_image'] ?? MC_URI_IMG.'/user/profile.png';
            $alterist_image_media = attachment_url_to_postid( $alterist_img );
            if( !empty( $alterist_image_media ) ) {
                $url = wp_get_attachment_image_src( $alterist_image_media, 'woocommerce_thumbnail' )[0];
                if( MC_Url::is( $url ) ) $alterist_img = $url;
            }
            $alterist_image        = MC_Url::urlToPath( $alterist_img );
            $content_creator_img   = $team['content_creator_image'] ?? MC_URI_IMG.'/user/profile.png';
            $content_creator_media = attachment_url_to_postid( $content_creator_img );
            if( !empty( $content_creator_media ) ) {
                $url = wp_get_attachment_image_src( $content_creator_media, 'woocommerce_thumbnail' )[0];
                if( MC_Url::is( $url ) ) $content_creator_img = $url;
            }
            $content_creator_image = MC_Url::urlToPath( $content_creator_img );
            
            $team_details = is_multisite() ? get_blog_option( 2, 'mf_team_credits_'.$count, 0 ) : get_option( 'mf_team_credits_'.$count, 0 );
            $credit_count = $team_details['set'];
            
            // Files
            if( $credit_count > 199 ) {
                $template_path = $count > 8 ? DIR_THEME_IMAGES.'/mythic-frames/blank-stretch-unlocked.png' : DIR_THEME_IMAGES.'/mythic-frames/blank-unlocked.png';
            } else {
                $template_path = $count > 8 ? DIR_THEME_IMAGES.'/mythic-frames/blank-stretch.png' : DIR_THEME_IMAGES.'/mythic-frames/blank.png';
                if( $count == 9 || $count == 10 || $count == 11 || $count == 12 ) $template_path = DIR_THEME_IMAGES.'/mythic-frames/blank-stretch.png';
            }
            
            //$twitter_icon  = MC_DIR_SRC.'/img/generation/social-poster/social-icons/twitter.png';
            $canvas_path  = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/ad-template.png';
            $sleeve_image = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/sleeve-layer.png';
            $gleam_image  = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/gleam-layer.png';
            
            // Establish Images
            $template = ImageManagerStatic::make( $template_path );
            $crop1    = ImageManagerStatic::make( $template_path );
            $crop2    = ImageManagerStatic::make( $template_path );
            
            // Credit count
            $template->text( $credit_count.'/200', 60, 185, function( $font ) {
                $font->file( MC_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf' );
                $font->size( 110 );
                $font->color( '#fff' );
                $font->valign( 'top' );
                $font->align( 'left' );
            } );
            
            // Text
            $template->text( '#'.$count, 10, 70, function( $font ) {
                $font->file( MC_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf' );
                $font->size( 90 );
                $font->color( '#000' );
                $font->valign( 'top' );
                $font->align( 'left' );
            } );
            
            $alter_id                      = $team['Creature'] ?? 0;
            $creature_image                = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : self::placeholder_frame();
            $creature_image                = MC_Url::urlToPath( $creature_image );
            $printing_id                   = MC_Alter_Functions::printing( $alter_id );
            $printing                      = new MC_Mtg_Printing( $printing_id );
            $creature_printing_selected    = !empty( $printing ) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_selected    = MC_Url::urlToPath( $creature_printing_selected );
            $creatures                     = self::creatures();
            $creature_printing_2015        = $count == 10 ? 38235 : $creatures[2015];
            $printing                      = new MC_Mtg_Printing( $creature_printing_2015 );
            $creature_printing_2015        = !empty( $creature_printing_2015 ) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_2015        = MC_Url::urlToPath( $creature_printing_2015 );
            $creature_printing_2003        = $count == 10 ? 59844 : $creatures[2003];
            $printing                      = new MC_Mtg_Printing( $creature_printing_2003 );
            $creature_printing_2003        = !empty( $creature_printing_2003 ) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_2003        = MC_Url::urlToPath( $creature_printing_2003 );
            $creature_printing_1997        = $count == 10 ? 70811 : $creatures[1997];
            $printing                      = new MC_Mtg_Printing( $creature_printing_1997 );
            $creature_printing_1997        = !empty( $creature_printing_1997 ) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_1997        = MC_Url::urlToPath( $creature_printing_1997 );
            $alter_id                      = $team['Non-Creature'] ?? 0;
            $noncreature_image             = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : self::placeholder_frame();
            $noncreature_image             = MC_Url::urlToPath( $noncreature_image );
            $printing_id                   = MC_Alter_Functions::printing( $alter_id );
            $printing                      = new MC_Mtg_Printing( $printing_id );
            $noncreature_printing_selected = !empty( $printing ) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_selected = MC_Url::urlToPath( $noncreature_printing_selected );
            $noncreatures                  = self::noncreatures();
            $noncreature_printing_2015     = $count == 10 ? 50686 : $noncreatures[2015];
            $printing                      = new MC_Mtg_Printing( $noncreature_printing_2015 );
            $noncreature_printing_2015     = !empty( $noncreature_printing_2015 ) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_2015     = MC_Url::urlToPath( $noncreature_printing_2015 );
            $noncreature_printing_2003     = $count == 10 ? 63093 : $noncreatures[2003];
            $printing                      = new MC_Mtg_Printing( $noncreature_printing_2003 );
            $noncreature_printing_2003     = !empty( $noncreature_printing_2003 ) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_2003     = MC_Url::urlToPath( $noncreature_printing_2003 );
            $noncreature_printing_1997     = $count == 10 ? 38286 : $noncreatures[1997];
            $printing                      = new MC_Mtg_Printing( $noncreature_printing_1997 );
            $noncreature_printing_1997     = !empty( $noncreature_printing_1997 ) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_1997     = MC_Url::urlToPath( $noncreature_printing_1997 );
            
            $alter_id               = $team['Land'] ?? 0;
            $land_image             = !empty( $alter_id ) ? MC_Alter_Functions::image( $alter_id ) : self::placeholder_frame();
            $land_image             = MC_Url::urlToPath( $land_image );
            $printing_id            = MC_Alter_Functions::printing( $alter_id );
            $printing               = new MC_Mtg_Printing( $printing_id );
            $land_printing_selected = !empty( $printing ) ? $printing->imgPng : self::placeholder_frame();
            $land_printing_selected = MC_Url::urlToPath( $land_printing_selected );
            
            // Establish Variables
            $width    = 630;
            $height   = 884;
            $offset_x = 10;
            $offset_y = 10;
            
            $profile_pic_d       = 200;
            $artist_pic_offset_x = 100;
            $cc_pic_offset_x     = 380;
            $featured_w          = 288;
            $featured_h          = 402;
            $example_w           = 150;
            $example_h           = 210;
            $commander_w         = 208;
            $commander_h         = 290;
            
            // Not Stretch
            $template_width          = 680;
            $template_height         = 2560;
            $profile_pics_y          = 400;
            $text_y                  = $profile_pics_y + 200 + 10;
            $creature_selected_x     = 20;
            $creature_selected_y     = 840;
            $creature_example_1_x    = 20;
            $creature_example_1_y    = 1425;
            $creature_example_2_x    = 120;
            $creature_example_2_y    = 1470;
            $creature_example_3_x    = 220;
            $creature_example_3_y    = 1425;
            $noncreature_selected_x  = 196;
            $noncreature_selected_y  = 900;
            $noncreature_example_1_x = 320;
            $noncreature_example_1_y = 1470;
            $noncreature_example_2_x = 420;
            $noncreature_example_2_y = 1425;
            $noncreature_example_3_x = 510;
            $noncreature_example_3_y = 1470;
            $land_x                  = 20;
            $land_y                  = 960;
            $commander_x             = 236;
            $commander_y             = 480;
            $template->crop( $template_width, $template_height );
            
            /** Image Files */
            
            // Profile Pic - Artist
            $canvas      = ImageManagerStatic::canvas( $profile_pic_d, $profile_pic_d );
            $profile_pic = ImageManagerStatic::make( $alterist_image );
            $profile_pic->resize( null, $profile_pic_d, function( $constraint ) {
                $constraint->aspectRatio();
            } );
            $profile_pic->crop( $profile_pic_d, $profile_pic_d );
            $canvas->insert( $profile_pic );
            $profile_frame = $noncreature_image != MC_Url::urlToPath( self::placeholder_frame() ) ? $noncreature_image : self::placeholder_frame();
            $profile_frame = $profile_frame == self::placeholder_frame() && $creature_image != MC_Url::urlToPath( self::placeholder_frame() ) ? $creature_image : self::placeholder_frame();
            $profile_frame = $profile_frame == self::placeholder_frame() && $land_image != MC_Url::urlToPath( self::placeholder_frame() ) ? $land_image : self::placeholder_frame();
            
            if( $profile_frame != self::placeholder_frame() ) {
                $profile_frame = MC_Url::urlToPath( $profile_frame );
                
                $outer_canvas = ImageManagerStatic::canvas( $profile_pic_d, $profile_pic_d );
                $canvas->crop( 190, 190 );
                $outer_canvas->insert( $canvas, 'top-left', 5, 5 );
                $alter_image = ImageManagerStatic::make( $profile_frame );
                $alter_image->resize( $profile_pic_d, null, function( $constraint ) {
                    $constraint->aspectRatio();
                } );
                $alter_image->crop( $profile_pic_d, 100, 0, 0 );
                $outer_canvas->insert( $alter_image );
                $alter_image->rotate( 180 );
                $outer_canvas->insert( $alter_image, 'top-left', 0, 100 );
                $canvas = $outer_canvas;
            }
            $template->insert( $canvas, 'top_left', $artist_pic_offset_x, $profile_pics_y );
            
            // Profile Pic - Content Creator
            $canvas      = ImageManagerStatic::canvas( 200, 200 );
            $profile_pic = ImageManagerStatic::make( $content_creator_image );
            $profile_pic->resize( null, 200, function( $constraint ) {
                $constraint->aspectRatio();
            } );
            $profile_pic->crop( 200, 200 );
            $canvas->insert( $profile_pic );
            $profile_frame = $noncreature_image != MC_Url::urlToPath( self::placeholder_frame() ) ? $noncreature_image : self::placeholder_frame();
            
            $profile_frame = $profile_frame == self::placeholder_frame() && $creature_image != MC_Url::urlToPath( self::placeholder_frame() ) ? $creature_image : self::placeholder_frame();
            $profile_frame = $profile_frame == self::placeholder_frame() && $land_image != MC_Url::urlToPath( self::placeholder_frame() ) ? $land_image : self::placeholder_frame();
            
            if( $count == 9 ) {
                $jeff_frame_id = 187764;
                $profile_frame = MC_Alter_Functions::image( $jeff_frame_id );
            }
            
            if( $profile_frame != self::placeholder_frame() ) {
                $profile_frame = MC_Url::urlToPath( $profile_frame );
                
                $outer_canvas = ImageManagerStatic::canvas( $profile_pic_d, $profile_pic_d );
                $canvas->crop( 190, 190 );
                $outer_canvas->insert( $canvas, 'top-left', 5, 5 );
                $alter_image = ImageManagerStatic::make( $profile_frame );
                $alter_image->resize( $profile_pic_d, null, function( $constraint ) {
                    $constraint->aspectRatio();
                } );
                $alter_image->crop( $profile_pic_d, 100, 0, 0 );
                $outer_canvas->insert( $alter_image );
                $alter_image->rotate( 180 );
                $outer_canvas->insert( $alter_image, 'top-left', 0, 100 );
                $canvas = $outer_canvas;
            }
            $template->insert( $canvas, 'top_left', $cc_pic_offset_x, $profile_pics_y );
            
            // Text
            $template->text( '#'.$count, 10, 70, function( $font ) {
                $font->file( MC_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf' );
                $font->size( 90 );
                $font->color( '#000' );
                $font->valign( 'top' );
                $font->align( 'left' );
            } );
            
            $canvas = ImageManagerStatic::canvas( 560, 240 );
            $canvas->text( $team['alterist_name'], 140, 0, function( $font ) {
                $font->file( MC_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf' );
                $font->size( 18 );
                $font->color( '#fff' );
                $font->valign( 'top' );
                $font->align( 'center' );
            } );
            if( !empty( $team['alterist_twitter'] ) ) {
                //$canvas->insert($twitter_icon, 'top-left', 30, 20);
                $canvas->text( 'TW: '.$team['alterist_twitter'], 140, 25, function( $font ) {
                    $font->file( MC_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf' );
                    $font->size( 13 );
                    $font->color( '#fff' );
                    $font->valign( 'top' );
                    $font->align( 'center' );
                } );
            }
            
            $canvas->text( $team['content_creator_name'], 420, 0, function( $font ) {
                $font->file( MC_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf' );
                $font->size( 18 );
                $font->color( '#fff' );
                $font->valign( 'top' );
                $font->align( 'center' );
            } );
            
            if( !empty( $team['content_creator_twitter'] ) ) {
                //$canvas->insert($twitter_icon, 'top-left', 310, 20);
                $canvas->text( 'TW: '.$team['content_creator_twitter'], 420, 25, function( $font ) {
                    $font->file( MC_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf' );
                    $font->size( 13 );
                    $font->color( '#fff' );
                    $font->valign( 'top' );
                    $font->align( 'center' );
                } );
            }
            
            $design_name = $team['design_name'] ?? '';
            $design_name = '#'.$design_name;
            
            $canvas->text( $design_name, 280, 70, function( $font ) {
                $font->file( MC_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf' );
                $font->size( 40 );
                $font->color( '#fff' );
                $font->valign( 'top' );
                $font->align( 'center' );
            } );
            $template->insert( $canvas, 'top_left', ( $artist_pic_offset_x - 40 ), $text_y );
            
            // Creature
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $creature_printing_selected );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $creature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_y );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_y );
            $canvas->insert( $gleam_image );
            $canvas->resize( $featured_w, $featured_h );
            $template->insert( $canvas, 'top-left', $creature_selected_x, $creature_selected_y );
            
            // Non-Creature
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $noncreature_printing_selected );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $noncreature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $featured_w, $featured_h );
            $template->insert( $canvas, 'top-left', $noncreature_selected_x, $noncreature_selected_y );
            // Land
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $land_printing_selected );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $land_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $featured_w, $featured_h );
            $template->insert( $canvas, 'top-right', $land_x, $land_y );
            
            // Creature Example 1
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $creature_printing_1997 );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $creature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $example_w, $example_h );
            $template->insert( $canvas, 'top-left', $creature_example_1_x, $creature_example_1_y );
            // Creature Example 2
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $creature_printing_2003 );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $creature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $example_w, $example_h );
            $template->insert( $canvas, 'top-left', $creature_example_2_x, $creature_example_2_y );
            
            // Creature Example 3
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $creature_printing_2015 );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $creature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $example_w, $example_h );
            $template->insert( $canvas, 'top-left', $creature_example_3_x, $creature_example_3_y );
            // Non-Creature Example 1
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $noncreature_printing_1997 );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $noncreature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $example_w, $example_h );
            $template->insert( $canvas, 'top-left', $noncreature_example_1_x, $noncreature_example_1_y );
            // Non-Creature Example 2
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $noncreature_printing_2003 );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $noncreature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $example_w, $example_h );
            $template->insert( $canvas, 'top-left', $noncreature_example_2_x, $noncreature_example_2_y );
            // Non-Creature Example 3
            $canvas     = ImageManagerStatic::make( $canvas_path );
            $card_image = ImageManagerStatic::make( $noncreature_printing_2015 );
            $card_image->resize( $width, $height );
            $alter_image = ImageManagerStatic::make( $noncreature_image );
            $alter_image->resize( $width, $height );
            $sleeve_image = ImageManagerStatic::make( $sleeve_image );
            $gleam_image  = ImageManagerStatic::make( $gleam_image );
            $canvas->insert( $sleeve_image );
            $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
            $canvas->insert( $gleam_image );
            $canvas->resize( $example_w, $example_h );
            $template->insert( $canvas, 'top-left', $noncreature_example_3_x, $noncreature_example_3_y );
            
            if( $count == 4 ) {
                $commander_1_id       = 187669;
                $commander_1_image    = !empty( $commander_1_id ) ? MC_Alter_Functions::image( $commander_1_id ) : self::placeholder_frame();
                $commander_1_image    = MC_Url::urlToPath( $commander_1_image );
                $commander_1_printing = ABSPATH.'/wp-content/uploads/2021/04/khm-168-esika-god-of-the-tree.png';
                
                $canvas               = ImageManagerStatic::make( $canvas_path );
                $commander_1_printing = MC_Vars::stringContains( $commander_1_printing,
                                                                 'unavailable-card.png' ) ? self::placeholder_card() : $commander_1_printing;
                $card_image           = ImageManagerStatic::make( $commander_1_printing );
                $card_image->resize( $width, $height );
                $commander_1_image = MC_Vars::stringContains( $commander_1_image,
                                                              'unavailable-card.png' ) ? self::placeholder_card() : $commander_1_image;
                $alter_image       = ImageManagerStatic::make( $commander_1_image );
                $alter_image->resize( $width, $height );
                $sleeve_image = ImageManagerStatic::make( $sleeve_image );
                $gleam_image  = ImageManagerStatic::make( $gleam_image );
                $canvas->insert( $sleeve_image );
                $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
                $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
                $canvas->insert( $gleam_image );
                $canvas->resize( $commander_w, $commander_h );
                $template->insert( $canvas, 'bottom-left', 112, $commander_y );
                
                $commander_2_id       = 187655;
                $commander_2_image    = !empty( $commander_2_id ) ? MC_Alter_Functions::image( $commander_2_id ) : self::placeholder_frame();
                $commander_2_image    = MC_Url::urlToPath( $commander_2_image );
                $commander_2_printing = ABSPATH.'/wp-content/uploads/2021/04/khm-168-the-prismatic-bridge.png';
                
                $canvas               = ImageManagerStatic::make( $canvas_path );
                $commander_2_printing = MC_Vars::stringContains( $commander_2_printing,
                                                                 'unavailable-card.png' ) ? self::placeholder_card() : $commander_2_printing;
                $card_image           = ImageManagerStatic::make( $commander_2_printing );
                $card_image->resize( $width, $height );
                $commander_2_image = MC_Vars::stringContains( $commander_2_image,
                                                              'unavailable-card.png' ) ? self::placeholder_card() : $commander_2_image;
                $alter_image       = ImageManagerStatic::make( $commander_2_image );
                $alter_image->resize( $width, $height );
                $sleeve_image = ImageManagerStatic::make( $sleeve_image );
                $gleam_image  = ImageManagerStatic::make( $gleam_image );
                $canvas->insert( $sleeve_image );
                $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
                $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
                $canvas->insert( $gleam_image );
                $canvas->resize( $commander_w, $commander_h );
                $template->insert( $canvas, 'bottom-left', 364, $commander_y );
            } else {
                $commander_1          = $team['Commander 1'] ?? [];
                $commander_1_id       = !empty( $commander_1 ) ? $commander_1[0] : 0;
                $commander_1_image    = !empty( $commander_1_id ) ? MC_Alter_Functions::image( $commander_1_id ) : self::placeholder_frame();
                $commander_1_image    = MC_Url::urlToPath( $commander_1_image );
                $printing_id          = MC_Alter_Functions::printing( $commander_1_id ) ?? 0;
                $printing             = new MC_Mtg_Printing( $printing_id );
                $commander_1_printing = !empty( $printing_id ) ? $printing->imgPng : self::placeholder_frame();
                // Commander 1
                $canvas = ImageManagerStatic::make( $canvas_path );
                if( !empty( $printing_id ) ) {
                    $commander_1_printing = MC_Vars::stringContains( $commander_1_printing,
                                                                     'unavailable-card.png' ) ? self::placeholder_card() : $commander_1_printing;
                    $card_image           = ImageManagerStatic::make( $commander_1_printing );
                    $card_image->resize( $width, $height );
                }
                $commander_1_image = MC_Vars::stringContains( $commander_1_image,
                                                              'unavailable-card.png' ) ? self::placeholder_card() : $commander_1_image;
                $alter_image       = ImageManagerStatic::make( $commander_1_image );
                $alter_image->resize( $width, $height );
                $sleeve_image = ImageManagerStatic::make( $sleeve_image );
                $gleam_image  = ImageManagerStatic::make( $gleam_image );
                $canvas->insert( $sleeve_image );
                if( !empty( $printing_id ) ) {
                    $canvas->insert( $card_image, 'top-left', $offset_x, $offset_x );
                }
                $canvas->insert( $alter_image, 'top-left', $offset_x, $offset_x );
                $canvas->insert( $gleam_image );
                $canvas->resize( $commander_w, $commander_h );
                $template->insert( $canvas, 'bottom-left', $commander_x, $commander_y );
            }
            
            $crop1->insert( $template );
            $crop1->crop( 680, 740, 0, 1 );
            $crop1->save( $mythic_frames_dir.'/'.$count.'-top.png' );
            
            $crop2->insert( $template );
            $crop2->crop( 680, 1440, 0, 820 );
            $crop2->save( $mythic_frames_dir.'/'.$count.'-bottom.png' );
        }
    }
    
    public static function sorted_teams() {
        $sorted_teams = [];
        $teams        = self::teams();
        foreach( $teams as $team_id => $team ) {
            switch( $team_id ) {
                // Aaron and Nissa
                case 67 :
                    $team['alterist_name']           = 'Aaron Miller';
                    $team['alterist_twitter']        = 'aarondraws';
                    $team['content_creator_name']    = 'Nissa Cosplay';
                    $team['content_creator_twitter'] = 'nissacosplay';
                    $sorted_teams[5]                 = $team;
                    break;
                // Jeff and Zbexx
                case 7399 :
                    $team['alterist_name']           = 'Jeff Laubenstein';
                    $team['alterist_twitter']        = 'IllustratorJeff';
                    $team['content_creator_name']    = 'ZBexx';
                    $team['content_creator_twitter'] = 'ZBexx';
                    $sorted_teams[8]                 = $team;
                    break;
                // Damaride and Mental Misplay
                case 16 :
                    $team['alterist_name']           = 'DamarideNeurommancer';
                    $team['alterist_twitter']        = 'DamNeurommancer';
                    $team['content_creator_name']    = 'Mental Misplay';
                    $team['content_creator_twitter'] = 'MisplayMental';
                    $sorted_teams[0]                 = $team;
                    break;
                // Steven Millage - tw from here
                case 6613 :
                    $team['alterist_name']           = 'Steven Millage';
                    $team['alterist_twitter']        = 'MillageArt';
                    $team['content_creator_name']    = 'I Hate Your Deck';
                    $team['content_creator_twitter'] = 'hate_your_deck';
                    $sorted_teams[1]                 = $team;
                    break;
                // Silas
                case 6222 :
                    $team['alterist_name']           = 'Silas';
                    $team['alterist_twitter']        = 'SilasMccomb';
                    $team['content_creator_name']    = 'Olivia Gobert Hicks';
                    $team['content_creator_twitter'] = 'goberthicks';
                    $sorted_teams[2]                 = $team;
                    break;
                // Hurley
                case 19 :
                    $team['alterist_name']        = 'Hurley Burley';
                    $team['alterist_twitter']     = 'HurleyBurleyArt';
                    $team['content_creator_name'] = 'Amanda Stevens';
                    // check Amanda's twitter - I took it by myself
                    $team['content_creator_twitter'] = 'amandatnstevens';
                    $sorted_teams[3]                 = $team;
                    break;
                // The Proxy Guy
                case 7246 :
                    $team['alterist_name']           = 'The Proxy Guy';
                    $team['alterist_twitter']        = 'theproxyguy';
                    $team['content_creator_name']    = 'Tappy Toe-Claws';
                    $team['content_creator_twitter'] = 'TappyToeClaws';
                    $sorted_teams[4]                 = $team;
                    break;
                // Inklin
                case 5551 :
                    $team['alterist_name']           = 'InklinCustoms';
                    $team['alterist_twitter']        = 'InklinCustoms';
                    $team['content_creator_name']    = 'Pleasant Kenobi';
                    $team['content_creator_twitter'] = 'PleasantKenobi';
                    $sorted_teams[6]                 = $team;
                    break;
                // Titus
                case 47 :
                    $team['alterist_name']           = 'Titus Lunter';
                    $team['alterist_twitter']        = 'tituslunter';
                    $team['content_creator_name']    = 'Spice8Rack';
                    $team['content_creator_twitter'] = 'Spice8Rack';
                    $sorted_teams[7]                 = $team;
                    break;
                //Ishton
                case 6994 :
                    $team['alterist_name']           = 'Ish';
                    $team['alterist_twitter']        = 'Ishton';
                    $team['content_creator_name']    = 'Ashlen Rose';
                    $team['content_creator_twitter'] = 'ashlenrose';
                    $sorted_teams[9]                 = $team;
                    break;
                //Milivoj
                case 1450 :
                    $team['alterist_name']           = 'Milivoj Ćeran';
                    $team['alterist_twitter']        = 'artmceran';
                    $team['content_creator_name']    = 'Mtg Muddstah';
                    $team['content_creator_twitter'] = 'mtgmuddstah';
                    $sorted_teams[10]                = $team;
                    break;
                // The Dinkelist
                case 5339 :
                    $team['alterist_name']           = 'The Dinkelist';
                    $team['alterist_twitter']        = '';
                    $team['content_creator_name']    = 'Kyle Hill';
                    $team['content_creator_twitter'] = 'Sci_Phile';
                    $sorted_teams[11]                = $team;
                    break;
            }
        }
        ksort( $sorted_teams );
        foreach( $sorted_teams as $key => $sorted_team ) {
            $description = $sorted_team['description'] ?? '';
            if( !empty( $description ) ) $sorted_team['description'] = stripslashes( $sorted_team['description'] );
            $sorted_teams[ $sorted_team['alterist_id'] ] = $sorted_team;
            unset( $sorted_teams[ $key ] );
        }
        
        return $sorted_teams;
    }
    
    public static function teams() {
        $teams = [
            67 => [
                'alterist_id'          => 67,
                'alterist_name'        => 'Aaron Miller',
                'content_creator_id'   => 0,
                'content_creator_name' => 'Nissa Cosplay',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [ 0 ],
                'Commander 2'          => [ 0 ],
            ],
            
            7399 => [
                'alterist_id'          => 7399,
                'alterist_name'        => 'Jeff Laubenstein',
                'content_creator_id'   => 0,
                'content_creator_name' => 'Zenaide Beckham',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [ 0 ],
                'Commander 2'          => [ 0 ],
            ],
            // Damaride - Alan
            16   => [
                'alterist_id'          => 16,
                'alterist_name'        => 'DamarideNeurommancer',
                'content_creator_id'   => 3552,
                'content_creator_name' => 'Mental Misplay',
                'Creature'             => 181734,
                'Non-Creature'         => 181739,
                'Land'                 => 181545,
                'Commander 1'          => [ 182396 ],
                'Commander 2'          => [ 182392 ],
            ],
            // 	NekroN99 - Ihateyourdeck
            6613 => [
                'alterist_id'          => 6613,
                'alterist_name'        => 'NekroN99',
                'content_creator_id'   => 6603,
                'content_creator_name' => 'I Hate Your Deck',
                'Creature'             => 183515,
                'Non Creature'         => 183517,
                'Land'                 => 183513,
                'Commander 1'          => [ 183521 ],
                'Commander 2'          => [ 183519 ],
            ],
            // Silas Rex - Olivia
            6222 => [
                'alterist_id'          => 6222,
                'alterist_name'        => 'Silas Rex',
                'content_creator_id'   => 5184,
                'content_creator_name' => 'Olivia Gobert-Hicks',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Hurley Burley - Ali Eldrazi
            19   => [
                'alterist_id'          => 19,
                'alterist_name'        => 'Hurley Burley',
                'content_creator_id'   => 0,
                'content_creator_name' => 'Ali Eldrazi',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Proxy Guy - Tappy Toe Claws
            7246 => [
                'alterist_id'          => 7246,
                'alterist_name'        => 'TheProxyGuy',
                'content_creator_id'   => 0,
                'content_creator_name' => 'TappyToeClaws',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Inklin
            5551 => [
                'alterist_id'          => 5551,
                'alterist_name'        => 'InklinCustoms',
                'content_creator_id'   => 2947,
                'content_creator_name' => 'Pleasant Kenobi',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Titus Lunter
            47   => [
                'alterist_id'          => 47,
                'alterist_name'        => 'Titus-Lunter',
                'content_creator_id'   => 0,
                'content_creator_name' => 'Spice8Rack',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Jeff - Zenade // @Todo fill
            
            // Ishton
            6994 => [
                'alterist_id'          => 6994,
                'alterist_name'        => 'Ishton',
                'content_creator_id'   => 0,
                'content_creator_name' => 'Ashlen Rose',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Milivoj
            1450 => [
                'alterist_id'          => 1450,
                'alterist_name'        => 'Milivoj',
                'content_creator_id'   => 3128,
                'content_creator_name' => 'Mtg Muddstah',
                'Creature'             => 0,
                'Non-Creature'         => 0,
                'Land'                 => 0,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
            
            // Dinkelist
            5339 => [
                'alterist_id'          => 5339,
                'alterist_name'        => 'The Dinkelist',
                'content_creator_id'   => 0,
                'content_creator_name' => 'Kyle Hill',
                'Creature'             => 187722,
                'Non-Creature'         => 187724,
                'Land'                 => 187726,
                'Commander 1'          => [],
                'Commander 2'          => [],
            ],
        
        ];
        
        $stored_team_data = is_multisite() ? get_blog_option( 1, 'mc_mf_teams', [] ) : get_option( 'mc_mf_teams', [] );
        foreach( $teams as $alterist_id => $team ) {
            if( !empty( $stored_team_data[ $alterist_id ] ) ) continue;
            $stored_team_data[ $alterist_id ] = $teams[ $alterist_id ];
        }
        
        return $stored_team_data;
    }
    
    /**
     * @param int $team_id
     *
     * @return array|mixed
     */
    public static function getTeam( $team_id = 0 ) : array {
        $teams = self::sorted_teams();
        $teams = array_values( $teams );
        foreach( $teams as $key => $team ) {
            if( $key + 1 == $team_id ) return $team;
        }
        return [];
    }
    
    /**
     * @return string
     */
    public static function placeholder_frame() {
        return AS_URI_IMG.'/placeholder.png';
    }
    
    /**
     * @return string
     */
    public static function placeholder_card() {
        return AS_URI_IMG.'/unavailable-card.png';
    }
    
    /**
     * @return array
     */
    public static function getContributorUserIds() : array {
        $user_ids = [];
        foreach( self::sorted_teams() as $team ) {
            $user_ids[] = $team['content_creator_id'];
            $user_ids[] = $team['alterist_id'];
        }
        return $user_ids;
    }
    
    /**
     * @return bool
     */
    public static function isUserContributor() : bool {
        global $current_user_id;
        if( in_array( $current_user_id, self::getContributorUserIds() ) ) return true;
        return false;
    }
    
    /**
     * @return array
     */
    public static function defaultBackerCreditArray() : array {
        $data = [
            'backer_id'    => 0,
            'set_credits'  => [
                'init'      => 0,
                'spent'     => 0,
                'available' => 0,
            ],
            'pack_credits' => [
                'init'      => 0,
                'spent'     => 0,
                'available' => 0,
            ],
            'land_credits' => [
                'init'      => 0,
                'spent'     => 0,
                'available' => 0,
            ],
            'spent'        => []
        ];
        for( $x = 1; $x <= 12; $x++ ) {
            $data['spent'][ $x ] = [
                'timestamp'    => 0,
                'set'          => 0,
                'non-creature' => 0,
                'creature'     => 0,
                'land'         => 0,
                'super-land'   => 0
            ];
        }
        return $data;
    }
    
    /**
     * @param int $backer_id
     *
     * @return array
     */
    public static function backerCreditData( $backer_id = 0 ) : array {
        $backer_data = self::defaultBackerCreditArray();
        
        if( MC_User_Functions::isAdmin() ) $backer_id = $_COOKIE['backer_id'] ?? $backer_id;
        
        if( empty( $backer_id ) ) {
            $user_id = MC_User_Functions::id();
            if( empty( $user_id ) ) return $backer_data;
            $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user_id, 'user' );
            if( empty( $backer_id ) ) return $backer_data;
        }
        
        // If backer data exists, get backer data
        if( MC_User_Functions::isAdmin() && !empty( $_GET['credit_reset'] ) ) {
            $backer_data = self::defaultBackerCreditArray();
        } else {
            $backer_data = is_multisite() ? get_blog_option( 2, 'backer_'.$backer_id, $backer_data ) : get_option( 'backer_'.$backer_id,
                                                                                                                   $backer_data );
            if( !empty( $backer_data ) && $backer_data != self::defaultBackerCreditArray() ) return $backer_data;
        }
        
        // Gets the json of pre-existing backer data, provided by importBackers
        $dir  = ABSPATH.'files/campaigns';
        $path = $dir.'/backers.json';
        $json = file_get_contents( $path );
        $json = json_decode( $json, ARRAY_A );
        
        if( !array_key_exists( 'backer_'.$backer_id, $json ) ) return $backer_data;
        
        // Set backer id returned data
        $backer_data['backer_id'] = $backer_id;
        
        // Initial backerdata from Kickstarter
        $json_data = $json[ 'backer_'.$backer_id ];
        
        // Set pre-existing credit usage
        $backer_credits   = $backer_data['set_credits']['init'];      // Pre-existing initial set credits
        $backer_available = $backer_data['set_credits']['available']; // Pre-existing available set credits
        $backer_spent     = $backer_data['set_credits']['spent'];     // Pre-existing spent set credits
        $json_credits     = $json_data['set_credits'];                // Set credits from most recent kickstarter import
        
        // If Imported credits are different current then adjust initial and available set values
        if( $json_credits != $backer_credits ) {
            $difference                              = $json_credits - $backer_credits;
            $backer_data['set_credits']['available'] = $backer_available + $difference;
            $backer_data['set_credits']['init']      = $json_credits;
        } else {
            $backer_data['set_credits']['init'] = $json_data['set_credits'] ?? 0;
            // If no credits used then set available as initial
            if( empty( $backer_available ) && empty( $backer_spent ) ) $backer_data['set_credits']['available'] = $json_data['set_credits'] ?? 0;
        }
        
        $backer_credits   = $backer_data['pack_credits']['init'];      // Pre-existing initial pack credits
        $backer_available = $backer_data['pack_credits']['available']; // Pre-existing available pack credits
        $backer_spent     = $backer_data['pack_credits']['spent'];     // Pre-existing spent pack credits
        $json_credits     = $json_data['pack_credits'];                // Pack credits from most recent kickstarter import
        
        // If Imported credits are different current then adjust initial and available pack values
        if( $json_credits != $backer_credits ) {
            $difference                               = $json_credits - $backer_credits;
            $backer_data['pack_credits']['available'] = $backer_available + $difference;
            $backer_data['pack_credits']['init']      = $json_credits;
        } else {
            $backer_data['pack_credits']['init'] = $json_data['pack_credits'] ?? 0;
            // If no credits used then set available as initial
            if( empty( $backer_available ) && empty( $backer_spent ) ) $backer_data['pack_credits']['available'] = $json_data['pack_credits'] ?? 0;
        }
        
        $backer_credits   = $backer_data['land_credits']['init'];      // Pre-existing initial land credits
        $backer_available = $backer_data['land_credits']['available']; // Pre-existing available land credits
        $backer_spent     = $backer_data['land_credits']['spent'];     // Pre-existing spent land credits
        $json_credits     = $json_data['land_credits'];                // Land credits from most recent kickstarter import
        
        // If Imported credits are different current then adjust initial and available land values
        if( $json_credits != $backer_credits ) {
            $difference                               = $json_credits + $backer_credits;
            $backer_data['land_credits']['available'] = ( $backer_available + $difference ) * 5;
            $backer_data['land_credits']['init']      = $json_credits;
        } else {
            $backer_data['land_credits']['init'] = $json_data['land_credits'] ?? 0;
            // If no credits used then set available as initial
            if( empty( $backer_spent ) && is_numeric( $json_data['land_credits'] ) ) {
                $backer_data['land_credits']['available'] = $json_data['land_credits'] * 5 ?? 0;
            } else if( $backer_available > $json_credits ) {
                $backer_data['land_credits']['available'] = $json_credits * 5;
            }
        }
        
        if( $backer_data['land_credits']['available'] < 0 ) $backer_data['land_credits']['available'] = 0;
        
        if( $backer_id == 1167 ) {
            $backer_data['set_credits']['init']       = 10;
            $backer_data['set_credits']['available']  = 10;
            $backer_data['pack_credits']['init']      = 10;
            $backer_data['pack_credits']['available'] = 10;
            $backer_data['land_credits']['init']      = 10;
            $backer_data['land_credits']['available'] = 10;
        }
        
        is_multisite() ? update_blog_option( 2, 'backer_'.$backer_id, $backer_data ) : update_option( 'backer_'.$backer_id, $backer_data );
        
        return $backer_data;
    }
    
    /**
     * @param int $backer_id
     *
     * @return array
     */
    public static function backerCredits( $backer_id = 0 ) : array {
        if( !is_numeric( $backer_id ) && is_string( $backer_id ) ) {
            $user = get_user_by( 'email', $backer_id );
            if( empty( $user ) ) return [];
            $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user->ID, 'user' );
        }
        $user_credits = self::backerCreditData( $backer_id );
        if( $user_credits == self::defaultBackerCreditArray() ) return [];
        $init_set_credits        = $user_credits['set_credits']['init'] ?? 0;
        $init_pack_credits       = $user_credits['pack_credits']['init'] ?? 0;
        $init_super_land_credits = $user_credits['land_credits']['init'] ?? 0;
        $init_super_land_credits = $init_super_land_credits * 5;
        $set_credits             = $user_credits['set_credits']['available'] ?? 0;
        $pack_credits            = $user_credits['pack_credits']['available'] ?? 0;
        $super_land_credits      = $user_credits['land_credits']['available'] ?? 0;
        
        $total_credits = $set_credits + $pack_credits + $super_land_credits;
        
        $results          = [
            'init_set_credits'         => $init_set_credits,
            'init_pack_credits'        => $init_pack_credits,
            'init_super_land_credits'  => $init_super_land_credits,
            'set_credits'              => $set_credits,
            'pack_credits'             => $pack_credits,
            'super_land_credits'       => $super_land_credits,
            'spent_set_credits'        => $user_credits['set_credits']['spent'] ?? 0,
            'spent_pack_credits'       => $user_credits['pack_credits']['spent'] ?? 0,
            'spent_super_land_credits' => $user_credits['land_credits']['spent'] ?? 0,
            'total_credits'            => $total_credits
        ];
        $teams            = MC_Mythic_Frames_Functions::sorted_teams();
        $count            = 0;
        $results['teams'] = [];
        foreach( $teams as $team ) {
            $count++;
            $results['teams'][ $count ] = [
                'spent_set_credits'          => $user_credits['spent'][ $count ]['set'] ?? 0,
                'spent_non_creature_credits' => $user_credits['spent'][ $count ]['non-creature'] ?? 0,
                'spent_creature_credits'     => $user_credits['spent'][ $count ]['creature'] ?? 0,
                'spent_land_credits'         => $user_credits['spent'][ $count ]['land'] ?? 0,
                'spent_super_land_credits'   => $user_credits['spent'][ $count ]['super-land'] ?? 0
            ];
        }
        foreach( $results as $key => $result ) {
            if( $key == 'teams' ) {
                foreach( $results['teams'] as $team_key => $team ) {
                    foreach( $team as $spend_key => $spent ) {
                        if( $spent < 0 ) $results[ $team_key ][ $spend_key ] = 0;
                    }
                }
                continue;
            }
            if( $result < 0 || !is_numeric( $result ) ) $results[ $key ] = 0;
        }
        return $results;
    }
    
    /**
     * @param int    $team
     * @param string $type
     * @param int    $add
     * @param int    $backer_id
     *
     * @return int
     */
    public static function addTeamCreditSpend( $team = 0, $type = '', $add = 0, $backer_id = 0 ) : int {
        if( $team < 1 || $team > 13 || empty( $type ) || empty( $add ) ) return 0;
        $team_credits = self::getTeamCredits( $team );
        $records      = self::getTeamRecords( $team );
        if( !isset( $records[ $backer_id.'_'.$type ] ) ) $records[ $backer_id.'_'.$type ] = [];
        
        $spent_before = self::getCurrentlySpentWithTeam( $backer_id, $team, $type );
        
        if( count( $records[ $backer_id.'_'.$type ] ) > 0 && !empty( $spent_before ) ) {
            $record = end( $records[ $backer_id.'_'.$type ] );
            if( $record['add'] == $add || $add < $record['add'] ) return 0;
            $spent                 = $add - $record['add'];
            $team_credits[ $type ] = $team_credits[ $type ] + $spent;
        } else {
            $team_credits[ $type ] = $team_credits[ $type ] + $add;
            $spent                 = $add;
        }
        $time                              = time();
        $records[ $backer_id.'_'.$type ][] = [
            'type' => $type,
            'add'  => $add,
        ];
        
        $backer_data                                = self::backerCreditData( $backer_id );
        $backer_data['spent'][ $team ]['timestamp'] = $time;
        $backer_data['spent'][ $team ][ $type ]     = $add;
        if( in_array( $type, [ 'creature', 'non-creature', 'land' ] ) ) {
            $type = 'pack';
        } else if( $type == 'super-land' ) {
            $type = 'land';
        }
        $spent_credits                            = $backer_data[ $type.'_credits' ]['spent'];
        $spent_credits                            = $spent_credits + $spent;
        $backer_data[ $type.'_credits' ]['spent'] = $spent_credits;
        $spent_credits                            = $backer_data[ $type.'_credits' ]['available'];
        $spent_credits                            = $spent_credits - $spent;
        if( $spent_credits < 0 ) $spent_credits = 0;
        $backer_data[ $type.'_credits' ]['available'] = $spent_credits;
        
        is_multisite() ? update_blog_option( 2, 'backer_'.$backer_id, $backer_data ) : update_option( 'backer_'.$backer_id, $backer_data );
        is_multisite() ? update_blog_option( 2, 'mf_team_credits_'.$team, $team_credits ) : update_option( 'mf_team_credits_'.$team, $team_credits );
        is_multisite() ? update_blog_option( 2, 'mf_team_records_'.$team, $records ) : update_option( 'mf_team_records_'.$team, $records );
        
        return $spent;
    }
    
    /**
     * @param int    $backer_id
     * @param int    $team_id
     * @param string $type
     *
     * @return int|mixed
     */
    public static function getCurrentlySpentWithTeam( $backer_id = 0, $team_id = 0, $type = '' ) {
        $backer_data = self::backerCreditData( $backer_id );
        return $backer_data['spent'][ $team_id ][ $type ] ?? 0;
    }
    
    /**
     * @param int $team
     *
     * @return array
     */
    public static function getTeamCredits( $team = 0 ) : array {
        if( $team < 1 || $team > 13 ) return [];
        
        $value = [
            'set'          => 0,
            'non-creature' => 0,
            'creature'     => 0,
            'land'         => 0,
            'super-land'   => 0,
        ];
        
        return is_multisite() ? get_blog_option( 2, 'mf_team_credits_'.$team, $value ) : get_option( 'mf_team_credits_'.$team, $value );
    }
    
    /**
     * @param int $team
     *
     * @return array
     */
    public static function getTeamRecords( $team = 0 ) : array {
        return is_multisite() ? get_blog_option( 2, 'mf_team_records_'.$team, [] ) : get_option( 'mf_team_records_'.$team, [] );
    }
    
    /**
     * @return string[]
     */
    public static function shippingOptionsToRemove() {
        $paid = MC_Woo_Order_Functions::userHasPreviouslyPurchased();
        return $paid ? self::normalShippingMethods() : self::cheaperShippingMethods();
    }
    
    public static function normalShippingMethods() {
        return [
            'flexible_shipping_single:3', 'flexible_shipping_single:2', 'flexible_shipping_single:1'
        ];
    }
    
    public static function cheaperShippingMethods() {
        return [
            'flexible_shipping_single:10', 'flexible_shipping_single:8', 'flexible_shipping_single:9'
        ];
    }
    
    /**
     * @param int $backer_id
     *
     * @return int
     */
    public static function feeProductId( $backer_id = 0 ) : int {
        if( !is_numeric( $backer_id ) && is_string( $backer_id ) ) {
            $user = get_user_by( 'email', $backer_id );
            if( empty( $user ) ) return 0;
            $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user->ID, 'user' );
        }
        if( empty( $backer_id ) ) $backer_id = self::getBackerId();
        $product = get_page_by_title( $backer_id.'-kickstarter-fee', OBJECT, 'product' );
        if( empty( $product ) ) return 0;
        return $product->ID;
    }
    
    /**
     * @param int $user_id
     */
    public static function getBackerId( $user_id = 0 ) {
        if( empty( $user_id ) ) $user_id = MC_User_Functions::id();
        $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user_id, 'user' );
        if( empty( $backer_id ) ) $backer_id = 0;
        return $backer_id;
    }
    
    /**
     * @return array|mixed
     */
    public static function getBackersFromJson() {
        $dir  = ABSPATH.'files/campaigns';
        $path = $dir.'/backers.json';
        if( !file_exists( $path ) ) return [];
        $json = file_get_contents( $path );
        return json_decode( $json, ARRAY_A );
    }
    
    /**
     * @return array
     */
    public static function getBackersAsUserIds() : array {
        $backer_ids = [];
        foreach( self::getBackersFromJson() as $backer ) {
            $backer_id = $backer['id'] ?? '';
            if( empty( $backer_id ) || is_numeric( $backer_id ) ) continue;
            $backer_ids[] = $backer['id'];
        }
        return get_users( [
                              'meta_query' => [
                                  [
                                      'key'     => 'mc_mf_backer_id',
                                      'compare' => 'IN',
                                      'value'   => $backer_ids,
                                  ]
                              ],
            
                              'number'      => -1,
                              'count_total' => false,
                              'fields'      => 'ids'
                          ] );
    }
    
    /**
     * @return array
     */
    public static function getUsersWithCredits() : array {
        $backers = [];
        foreach( self::getBackersAsUserIds() as $user_id ) {
            $backer_id = self::getBackerId( $user_id );
            if( empty( $backer_id ) ) continue;
            if( self::hasRemainingCredits( $backer_id ) ) $backers[] = $user_id;
        }
        return $backers;
    }
    
    /**
     * @return array
     */
    public static function getUsersWithoutCredits() : array {
        $backers = [];
        foreach( self::getBackersAsUserIds() as $user_id ) {
            $backer_id = self::getBackerId( $user_id );
            if( empty( $backer_id ) ) continue;
            if( !self::hasRemainingCredits( $backer_id ) ) $backers[] = $user_id;
        }
        return $backers;
    }
    
    /**
     * @param int $backer_id
     *
     * @return int
     */
    public static function hasRemainingCredits( $backer_id = 0 ) : int {
        if( empty( $backer_id ) ) {
            $backer_id = !empty( $user_id ) ? MC_WP::meta( 'mc_mf_backer_id', $user_id, 'user' ) : 0;
            $backer_id = MC_User_Functions::isAdmin() && !empty( $_GET['backer_id'] ) ? $_GET['backer_id'] : $backer_id;
        }
        if( !is_numeric( $backer_id ) && is_string( $backer_id ) ) {
            $user = get_user_by( 'email', $backer_id );
            if( empty( $user ) ) return 0;
            $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user->ID, 'user' );
        }
        $user_credits       = MC_Mythic_Frames_Functions::backerCreditData( $backer_id );
        $set_credits        = $user_credits['set_credits']['available'] ?? 0;
        $set_credits        = is_numeric( $set_credits ) ? $set_credits : 0;
        $pack_credits       = $user_credits['pack_credits']['available'] ?? 0;
        $pack_credits       = is_numeric( $set_credits ) ? $pack_credits : 0;
        $super_land_credits = $user_credits['land_credits']['available'] ?? 0;
        $super_land_credits = is_numeric( $set_credits ) ? $super_land_credits : 0;
        
        $credits = (int) ( $set_credits + $pack_credits + $super_land_credits );
        if( $credits < 0 ) $credits = 0;
        return $credits;
    }
    
    /**
     * @param int    $backer_id
     * @param string $address
     *
     * @return bool
     * @throws Exception
     */
    public static function hasAddress( $backer_id = 0, $address = 'shipping' ) : bool {
        if( !is_numeric( $backer_id ) && is_string( $backer_id ) ) {
            $user = get_user_by( 'email', $backer_id );
            if( empty( $user ) ) return false;
            $backer_id = MC_WP::meta( 'mc_mf_backer_id', $user->ID, 'user' );
        }
        if( empty( $backer_id ) ) return false;
        $user_id = self::backerIdToUserId( $backer_id );
        $user    = MC_Woo_Customer_Functions::getCustomer( $user_id );
        if( empty( $user ) ) return false;
        $method = 'get_'.$address.'_address';
        return !empty( $user->$method() );
    }
    
    /**
     * @param string $email
     *
     * @return bool
     */
    public static function emailToBackerId( string $email = '' ) : bool {
        if( empty( $email ) ) return 0;
        $user = get_user_by( 'email', $email );
        if( empty( $user ) ) return 0;
        $user_id = $user->ID;
        return self::userIdToBackerId( $user_id );
    }
    
    /**
     * @param int $user_id
     *
     * @return array|mixed
     */
    public static function userIdToBackerId( int $user_id = 0 ) {
        return MC_WP::meta( 'mc_mf_backer_id', $user_id, 'user' );
    }
    
    /**
     * @param int $backer_id
     *
     * @return int|mixed
     */
    public static function backerIdToUserId( int $backer_id = 0 ) {
        $users = get_users( [
                                'meta_key'   => 'mc_mf_backer_id',
                                'meta_value' => $backer_id,
                                'fields'     => 'ids'
                            ] );
        if( empty( $users ) ) return 0;
        return $users[0];
    }
    
    public static function backerId() {
        $user_id   = MC_User_Functions::id();
        $backer_id = !empty( $user_id ) ? MC_WP::meta( 'mc_mf_backer_id', $user_id, 'user' ) : 0;
        $backer_id = MC_User_Functions::isAdmin() && !empty( $_GET['backer_id'] ) ? $_GET['backer_id'] : $backer_id;
        $backer_id = empty( $backer_id ) ? 0 : $backer_id;
        
        if( MC_User_Functions::isAdmin() ) {
            $backer_id = $_COOKIE['backer_id'] ?? $backer_id;
            $json_file = ABSPATH.'/files/campaigns/backers.json';
            $json_file = file_get_contents( $json_file );
            $json      = json_decode( $json_file, ARRAY_A );
            if( isset( $json[ 'backer_'.$backer_id ] ) ) $backer_name = $json[ 'backer_'.$backer_id ]['name'];
        }
        return $backer_id;
    }
    
    public static function updateCreditTotals() {
        // Create team counters
        $teams = [];
        for( $x = 1; $x <= 12; $x++ ) {
            $teams[ $x ] = 0;
        }
        
        // Cycle backers
        for( $i = 1; $i <= 1800; $i++ ) {
            $backer = get_blog_option( 2, 'backer_'.$i, [] );
            if( empty( $backer ) ) continue;
            $spent = $backer['spent'];
            for( $x = 1; $x <= 12; $x++ ) {
                $set = $spent[ $x ]['set'] ?? 0;
                if( !is_numeric( $set ) ) continue;
                $teams[ $x ] = $teams[ $x ] + $set;
            }
        }
        
        $filters = [
            'post_status'    => [ 'wc-processing' ],
            'post_type'      => 'shop_order',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ];
        $orders  = get_posts( $filters );
        foreach( $orders as $order ) {
            $order = wc_get_order( $order );
            
            // Loop though order "line items"
            foreach( $order->get_items() as $item_id => $item ) {
                $product_id   = $item->get_product_id(); //Get the product ID
                $quantity     = $item->get_quantity();   //Get the product QTY
                $product_name = $item->get_name();       //Get the product NAME
                
                // Get an instance of the WC_Product object (can be a product variation  too)
                $product = $item->get_product();
                
                // Get the product description (works for product variation too)
                $description = $product->get_description();
                
                // Only for product variation
                if( $product->is_type( 'variation' ) ) {
                    // Get the variation attributes
                    $variation_attributes = $product->get_variation_attributes();
                    if( count( $variation_attributes ) > 1 ) continue;
                    // Loop through each selected attributes
                    foreach( $variation_attributes as $attribute_taxonomy => $term_slug ) {
                        if( $attribute_taxonomy != 'attribute_pa_design-team' ) continue;
                        $teams[ $term_slug ] = $teams[ $term_slug ] + $quantity;
                    }
                }
            }
            break;
        }
        
        foreach( $teams as $team_id => $team ) {
            $additional = 0;
            switch( $team_id ) {
                case 2 :
                    $additional = 68;
                    break;
                case 4 :
                    $additional = 94;
                    break;
                case 7 :
                    $additional = 36;
                    break;
                case 6 :
                    $additional = 2;
                    break;
                case 8 :
                    $additional = 123;
                    break;
                case 9 :
                    $additional = 13;
                    break;
                case 12 :
                    $additional = 76;
                    break;
            }
            $team      = $team + $additional;
            $team_data = get_blog_option( 2, 'mf_team_credits_'.$team_id );
            if( $team < 200 ) $team = 200;
            $team_data['set'] = $team;
            update_blog_option( 2, 'mf_team_credits_'.$team_id, $team_data );
        }
    }
    
    /**
     * @return int|mixed|string
     */
    public static function remainingSetCredits() {
        $sets = 0;
        foreach( self::getBackersFromJson() as $backer ) {
            if( empty( $backer['id'] ) ) continue;
            $user_credits = MC_Mythic_Frames_Functions::backerCredits( $backer['id'] ?? 0 );
            extract( $user_credits );
            if( empty( $set_credits ) || !is_numeric( $set_credits ) ) continue;
            $sets = $sets + $set_credits;
        }
        return $sets;
    }
    
    /**
     * @param null $product
     *
     * @return int
     */
    public static function getProductDesignTeam( $product = null ) {
        if( empty( $product ) ) {
            global $product;
        }
        if( empty( $product ) ) return 0;
        if( !is_object( $product ) ) return 0;
        if( !method_exists( $product, 'get_attributes' ) ) return 0;
        $attributes = $product->get_attributes();
        if( empty( $design_team = $attributes['pa_design-team'] ) ) return 0;
        $team_id = $design_team->get_data()['options'][0];
        switch( $team_id ) {
            case 18 :
                return 1;
            case 19 :
                return 2;
            case 20 :
                return 3;
            case 21 :
                return 4;
            case 22 :
                return 5;
            case 23 :
                return 6;
            case 24 :
                return 7;
            case 25 :
                return 8;
            case 26 :
                return 9;
            case 27 :
                return 10;
            case 28 :
                return 11;
            case 29 :
                return 12;
            default :
                return 0;
        }
    }
    
    /**
     * @param string $order
     * @param int    $count
     * @param int    $exclude
     *
     * @return array
     */
    public static function getPlaymatProducts( $count = 0, $exclude = 0 ) : array {
        $playmats = [];
        for( $x = 1; $x <= 12; $x++ ) {
            if( $exclude == $x ) continue;
            $playmats[] = self::getPlaymatIdByTeam( $x );
            if( $count == $x ) break;
        }
        shuffle( $playmats );
        return $playmats;
    }
    
    /**
     * @param int $team_id
     *
     * @return int
     */
    public static function getPlaymatIdByTeam( $team_id = 0 ) : int {
        switch( $team_id ) {
            case 1 :
                return 676;
            case 2 :
                return 677;
            case 3 :
                return 679;
            case 4 :
                return 681;
            case 5 :
                return 682;
            case 6 :
                return 663;
            case 7 :
                return 672;
            case 9 :
                return 673;
            case 10 :
                return 683;
            case 11 :
                return 684;
            case 12 :
                return 685;
            default :
                return 0;
        }
    }
    
    /**
     * @param int $team_id
     *
     * @return int
     */
    public static function getMythicFramesProductByTeamId( $team_id = 0 ) : int {
        switch( $team_id ) {
            case 1 :
                return 694;
            case 2 :
                return 824;
            case 3 :
                return 893;
            case 4 :
                return 899;
            case 5 :
                return 905;
            case 6 :
                return 910;
            case 7 :
                return 915;
            case 8 :
                return 921;
            case 9 :
                return 926;
            case 10 :
                return 932;
            case 11 :
                return 937;
            case 12 :
                return 943;
            default :
                return 0;
        }
    }
    
    public static function getTeamName( $team_id = 0 ) {
        $team = MC_Mythic_Frames_Functions::getTeam( $team_id );
        return !empty( $team['design_name'] ) ? $team['design_name'] : '';
    }
    
    public static function update_production_totals() {
        // Set up the storage array
        $totals = [];
        for( $x = 1; $x <= 12; $x++ ) {
            $totals[ $x ] = [
                'set'          => 0,
                'creature'     => 0,
                'non-creature' => 0,
                'land'         => 0,
                'playmat_1'    => 0,
                'playmat_2'    => 0,
                'stretch'      => 0,
                'US'           => [
                    'set'          => 0,
                    'creature'     => 0,
                    'non-creature' => 0,
                    'land'         => 0,
                    'playmat_1'    => 0,
                    'playmat_2'    => 0,
                    'stretch'      => 0
                ],
                'NL'           => [
                    'set'          => 0,
                    'creature'     => 0,
                    'non-creature' => 0,
                    'land'         => 0,
                    'playmat_1'    => 0,
                    'playmat_2'    => 0,
                    'stretch'      => 0
                ]
            ];
        }
        
        // Backer counts first!
        for( $backer = 1; $backer <= 2000; $backer++ ) {
            $backer_credits = get_option( 'backer_'.$backer, [] );
            if( empty( $backer_credits ) || !isset( $backer_credits['spent'] ) ) continue;
            
            $users = get_users( [
                                    'meta_key'   => 'mc_mf_backer_id',
                                    'meta_value' => $backer,
                                    'fields'     => 'ids'
                                ] );
            
            $user_id = !empty( $users ) ? $users[0] : 0;
            $country = 'US';
            if( !empty( $user_id ) ) {
                $user_meta = get_user_meta( $user_id );
                if( isset( $user_meta['shipping_country'] ) ) {
                    $country = $user_meta['shipping_country'];
                } else if( isset( $user_meta['billing_country'] ) ) {
                    $country = $user_meta['billing'];
                } else if( isset( $user_meta['address_country'] ) ) {
                    $country = $user_meta['address_country'];
                }
            }
            if( is_array( $country ) ) $country = $country[0];
            if( strtolower( $country ) != 'us' ) $country = 'NL';
            
            for( $team = 1; $team <= 12; $team++ ) {
                $spend = $backer_credits['spent'][ $team ];
                if( empty( $spend['timestamp'] ) ) continue;
                $totals[ $team ]['set']          += $spend['set'] ?? 0;
                $totals[ $team ]['non-creature'] += $spend['non-creature'] ?? 0;
                $totals[ $team ]['land']         += $spend['land'] ?? 0;
                $totals[ $team ]['land']         += $spend['super-land'] ?? 0;
                
                $totals[ $team ][ $country ]['set']          += $spend['set'] ?? 0;
                $totals[ $team ][ $country ]['non-creature'] += $spend['non-creature'] ?? 0;
                $totals[ $team ][ $country ]['land']         += $spend['land'] ?? 0;
                $totals[ $team ][ $country ]['land']         += $spend['super-land'] ?? 0;
            }
        }
        
        // Count sales from initial products
        $orders = MC_Woo_Order_Functions::get_all_site_order_ids( [ 'wc-processing' ] );
        foreach( $orders as $order_id ) {
            $order   = wc_get_order( $order_id );
            $items   = $order->get_items();
            $country = $order->get_shipping_country();
            $country = empty( $country ) ? $order->get_billing_country() : $country;
            $country = strtolower( $country ) != 'us' ? 'NL' : 'US';
            foreach( $items as $item ) {
                $quantity = $item->get_quantity();   //Get the product QTY
                
                $product = $item->get_product();
                // Only for product variation
                if( $product->is_type( 'variation' ) ) {
                    // Get the variation attributes
                    $variation_attributes = $product->get_variation_attributes();
                    // Loop through each selected attributes
                    $type = '';
                    $team = $product->get_name();
                    foreach( $variation_attributes as $attribute_taxonomy => $term_slug ) {
                        $term_slug = sanitize_title( $term_slug );
                        
                        // Get product attribute name or taxonomy
                        $taxonomy = str_replace( 'attribute_', '', $attribute_taxonomy );
                        
                        switch( $taxonomy ) {
                            case 'pa_design-team' :
                                $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy );
                                if( empty( $attribute_value ) ) continue 2;
                                $attribute_value = $attribute_value->name;
                                $team            = $attribute_value;
                                break;
                            case 'pa_pack-type' :
                                $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy );
                                if( empty( $attribute_value ) ) continue 2;
                                $attribute_value = $attribute_value->name;
                                $type            = $attribute_value;
                                $type            = self::deduce_product_type( $type );
                                break;
                            case 'playmat-design' :
                                if( $product->get_id() == 675 ) {
                                    $type = 'playmat_2';
                                } else if( $product->get_id() == 674 ) {
                                    $type = 'playmat_1';
                                }
                        }
                    }
                    $team = self::deduce_team_to_team_id( $team );
                }
                
                if( $product->is_type( 'simple' ) ) {
                    $team = $product->get_name();
                    $team = self::deduce_team_to_team_id( $team );
                    $type = 'playmat_1';
                }
                
                if( empty( $type ) || empty( $team ) ) continue;
                $totals[ $team ][ $type ]             += $quantity;
                $totals[ $team ][ $country ][ $type ] += $quantity;
            }
        }
        
        $totals[8]['set']       += 20;
        $totals[8]['US']['set'] += 10;
        $totals[8]['NL']['set'] += 10;
        
        foreach( $totals as $key => $total ) {
            $totals[ $key ]['total_creature']     = ( $total['set'] * 2 ) + $total['creature'];
            $totals[ $key ]['total_non_creature'] = ( $total['set'] * 2 ) + $total['non-creature'];
            $totals[ $key ]['total_land']         = $total['set'] + $total['land'];
            
            $totals[ $key ]['US']['total_creature']     = ( $total['US']['set'] * 2 ) + $total['US']['creature'];
            $totals[ $key ]['US']['total_non_creature'] = ( $total['US']['set'] * 2 ) + $total['US']['non-creature'];
            $totals[ $key ]['US']['total_land']         = $total['US']['set'] + $total['US']['land'];
            
            $totals[ $key ]['NL']['total_creature']     = ( $total['NL']['set'] * 2 ) + $total['NL']['creature'];
            $totals[ $key ]['NL']['total_non_creature'] = ( $total['NL']['set'] * 2 ) + $total['NL']['non-creature'];
            $totals[ $key ]['NL']['total_land']         = $total['NL']['set'] + $total['NL']['land'];
        }
        
        update_option( 'mc_teams_production_totals', $totals );
    }
    
    public static function update_production_payout() {
        // Set up the storage array
        $totals = [];
        for( $x = 1; $x <= 12; $x++ ) {
            $totals[ $x ] = [
                'set'          => 0,
                'creature'     => 0,
                'non-creature' => 0,
                'land'         => 0,
                'stretch'      => 0,
            ];
        }
        
        // Backer counts first!
        for( $backer = 1; $backer <= 2000; $backer++ ) {
            $backer_credits = get_option( 'backer_'.$backer, [] );
            if( empty( $backer_credits ) || !isset( $backer_credits['spent'] ) ) continue;
            for( $team = 1; $team <= 12; $team++ ) {
                $spend = $backer_credits['spent'][ $team ];
                if( empty( $spend['timestamp'] ) ) continue;
                $totals[ $team ]['set']          += $spend['set'] ?? 0;
                $totals[ $team ]['creature']     += $spend['creature'] ?? 0;
                $totals[ $team ]['non-creature'] += $spend['non-creature'] ?? 0;
                $totals[ $team ]['land']         += $spend['land'] ?? 0;
                $totals[ $team ]['land']         += $spend['super-land'] ?? 0;
            }
        }
        
        $totals[8]['set'] += 20;
        
        foreach( $totals as $key => $total ) {
            $totals[ $key ]['total_creature']     = ( $total['set'] * 2 ) + $total['creature'];
            $totals[ $key ]['total_non_creature'] = ( $total['set'] * 2 ) + $total['non-creature'];
            $totals[ $key ]['total_land']         = $total['set'] + $total['land'];
        }
        
        update_option( 'mc_teams_kickstarter_totals', $totals );
        
        // Set up the storage array
        $totals = [];
        for( $x = 1; $x <= 12; $x++ ) {
            $totals[ $x ] = [
                'set'          => 0,
                'creature'     => 0,
                'non-creature' => 0,
                'land'         => 0,
                'stretch'      => 0,
            ];
        }
        
        // Count sales from initial products
        $orders = MC_Woo_Order_Functions::get_all_site_order_up_to_july();
        foreach( $orders as $order_id ) {
            $order = wc_get_order( $order_id );
            $items = $order->get_items();
            foreach( $items as $item ) {
                $quantity = $item->get_quantity();   //Get the product QTY
                
                $product = $item->get_product();
                // Only for product variation
                if( $product->is_type( 'variation' ) ) {
                    // Get the variation attributes
                    $variation_attributes = $product->get_variation_attributes();
                    // Loop through each selected attributes
                    $type = '';
                    $team = $product->get_name();
                    foreach( $variation_attributes as $attribute_taxonomy => $term_slug ) {
                        // Get product attribute name or taxonomy
                        $taxonomy = str_replace( 'attribute_', '', $attribute_taxonomy );
                        
                        $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy )->name;
                        
                        switch( $attribute_taxonomy ) {
                            case 'attribute_pa_design-team' :
                                $team = $attribute_value;
                                break;
                            case 'attribute_pa_pack-type' :
                                $type = $attribute_value;
                                break;
                        }
                    }
                    $type = self::deduce_product_type( $type );
                    $team = self::deduce_team_to_team_id( $team );
                }
                
                if( empty( $type ) || empty( $team ) ) continue;
                $totals[ $team ][ $type ] += $quantity;
            }
        }
        
        foreach( $totals as $key => $total ) {
            $totals[ $key ]['total_creature']     = ( $total['set'] * 2 ) + $total['creature'];
            $totals[ $key ]['total_non_creature'] = ( $total['set'] * 2 ) + $total['non-creature'];
            $totals[ $key ]['total_land']         = $total['set'] + $total['land'];
        }
        
        update_option( 'mc_teams_preorder_totals', $totals );
    }
    
    /**
     * @param string $string
     *
     * @return mixed|string
     */
    public static function deduce_product_type( $string = '' ) {
        $string = strtolower( $string );
        $string = str_replace( ' ', '', $string );
        if( strpos( $string, 'playmat' ) !== false ) {
            $type = 'playmat';
        } else if( strpos( $string, 'set' ) !== false ) {
            $type = 'set';
        } else if( strpos( $string, 'stretch' ) !== false ) {
            $type = 'stretch';
        } else if( strpos( $string, 'land' ) !== false ) {
            $type = 'land';
        } else if( strpos( $string, 'non-creature' ) !== false ) {
            $type = 'non-creature';
        } else if( strpos( $string, 'creature' ) !== false ) {
            $type = 'creature';
        }
        return $type ?? '';
    }
    
    public static function deduce_team_to_team_id( $string = '' ) {
        $string = strtolower( $string );
        $string = str_replace( ' ', '', $string );
        
        $team = 0;
        if( strpos( $string, 'graveyardgang' ) !== false ) {
            $team = 1;
        } else if( strpos( $string, 'spithotfire' ) !== false ) {
            $team = 2;
        } else if( strpos( $string, 'bladelotus' ) !== false ) {
            $team = 3;
        } else if( strpos( $string, 'sleevewithpride' ) !== false ) {
            $team = 4;
        } else if( strpos( $string, 'jhoiride' ) !== false ) {
            $team = 5;
        } else if( strpos( $string, 'greenspirits' ) !== false ) {
            $team = 6;
        } else if( strpos( $string, 'bigcitymelee' ) !== false ) {
            $team = 7;
        } else if( strpos( $string, 'wizardimmortal' ) !== false ) {
            $team = 8;
        } else if( strpos( $string, 'goblinblade' ) !== false ) {
            $team = 9;
        } else if( strpos( $string, 'mfishlen' ) !== false ) {
            $team = 10;
        } else if( strpos( $string, 'mishlen' ) !== false ) {
            $team = 10;
        } else if( strpos( $string, 'nordicmistform' ) !== false ) {
            $team = 11;
        } else if( strpos( $string, 'sciwave' ) !== false || strpos( $string, 'sci-wave' ) !== false ) {
            $team = 12;
        }
        return $team;
    }
    
    /**
     * @param int $product_id
     *
     * @return int
     */
    public static function get_team_id_from_product_id( $product_id = 0 ) : int {
        $product = wc_get_product( $product_id );
        if( empty( $product ) ) return 0;
        $design_team = $product->get_attribute( 'pa_design-team' );
        if( is_string( $design_team ) ) {
            return MC_Mythic_Frames_Functions::deduce_team_to_team_id( $design_team );
        }
        return 0;
    }
    
    /**
     * @param int $user_id
     *
     * @return float|int
     */
    public static function get_royalty_percentage_by_user_id( $user_id = 0 ) {
        $users = [
            16    => .15,
            9463  => .05,
            6603  => .1,
            6613  => .1,
            19    => .08,
            10706 => .06,
            7246  => .1,
            6462  => .1,
            67    => .12,
            10956 => 0.08,
            2947  => .1,
            5551  => .1,
            47    => .1,
            10777 => .1,
            7399  => .1,
            9488  => .1,
            6994  => .1,
            10693 => .1,
            1450  => .14,
            3128  => .06,
            5339  => .1,
            10133 => .1,
            6222  => .1,
            5184  => .1
        ];
        return $users[ $user_id ] ?? 0;
    }
    
    /**
     * @param int $team_id
     *
     * @return array
     */
    public static function get_user_ids_in_team( int $team_id = 0 ) : array {
        $teams = [
            1  => [ 16, 9463 ],
            2  => [ 6603, 6613 ],
            3  => [ 6222, 5184 ],
            4  => [ 19, 10706 ],
            5  => [ 7246, 6462 ],
            6  => [ 67, 10956 ],
            7  => [ 2947, 5551 ],
            8  => [ 47, 10777 ],
            9  => [ 7399, 9488 ],
            10 => [ 6994, 10693 ],
            11 => [ 1450, 3128 ],
            12 => [ 5339, 10133 ]
        ];
        return $teams[ $team_id ] ?? $teams;
    }
    
    /**
     * @param int $user_id
     *
     * @return int|string
     */
    public static function get_team_from_user_id( int $user_id = 0 ) {
        if( empty( $user_id ) ) {
            $user_id = \MC_User::id();
            if( MC_User::isAdmin() ) $user_id = 16;
        }
        $teams = self::get_user_ids_in_team();
        foreach( $teams as $team_id => $team ) {
            if( in_array( $user_id, $team ) ) return $team_id;
        }
        return 0;
    }
    
    /**
     * @param string $email
     *
     * @return string
     */
    public static function get_invoice_id_from_email( $email = '' ) {
        $user      = get_user_by( 'email', $email );
        $last_name = '';
        if( !empty( $user ) ) {
            $user_id   = $user->ID;
            $last_name = $user->last_name;
            $last_name = MC_User::nametoFirstLast( $last_name );
            if( empty( $last_name ) ) {
                $last_name = $user->first_name;
            } else if( isset( $last_name['last_name'] ) ) {
                $last_name = $last_name['last_name'];
            }
            $backer_id = MC_Mythic_Frames_Functions::userIdToBackerId( $user_id );
        }
        if( empty( $backer_id ) ) $backer_id = 0;
        
        $orders = MC_Woo_Order_Functions::get_order_ids_by_email( $email, [ 'wc-processing' ] );
        
        foreach( $orders as $order_id ) break;
        
        if( !empty( $order_id ) ) {
            $order = wc_get_order( $order_id );
            if( !empty( $order ) && empty( $last_name ) ) {
                $last_name = $order->get_billing_last_name();
                if( empty( $last_name ) ) $last_name = $order->get_billing_first_name();
            }
        }
        
        if( empty( $last_name ) || empty( MC_Vars::alphanumericOnly( $last_name, false ) ) ) {
            $last_name = $email;
        }
        $last_name = strtoupper( $last_name );
        
        // Backer Readable
        $backer_readable = $backer_id;
        while( strlen( $backer_readable ) < 4 ) {
            $backer_readable = '0'.$backer_readable;
        }
        // Order Readable
        if( empty( $orders ) || empty( $order_id ) ) $order_id = 0;
        $order_readable = $order_id;
        while( strlen( $order_readable ) < 4 ) {
            $order_readable = '0'.$order_readable;
        }
        
        return $last_name.'-'.$backer_readable.'-'.$order_readable;
    }
    
    public static function get_readable_address_from_email( $email = '' ) {
        $orders = MC_Woo_Order_Functions::get_all_site_order_ids( [ 'wc-processing' ] );
        
        foreach( $orders as $key => $order_id ) {
            $order       = wc_get_order( $order_id );
            $order_email = strtolower( $order->get_billing_email() );
            if( $order_email != strtolower( $email ) ) unset( $orders[ $key ] );
        }
        
        foreach( $orders as $order_id ) {
            $order            = wc_get_order( $order_id );
            $first_name       = $order->get_billing_first_name();
            $last_name        = $order->get_billing_last_name();
            $address_1        = $order->get_shipping_address_1();
            $address_2        = $order->get_shipping_address_2();
            $address_city     = $order->get_shipping_city();
            $address_postcode = $order->get_shipping_postcode();
            $address_state    = $order->get_shipping_state();
            $address_country  = $order->get_shipping_country();
            break;
        }
        
        $user = get_user_by( 'email', $email );
        if( !empty( $user ) ) {
            $user_id    = $user->ID;
            $first_name = $user->first_name;
            $last_name  = $user->last_name;
            
            // Address
            $user_address_1        = get_user_meta( $user_id, 'shipping_address_1', true );
            $user_address_2        = get_user_meta( $user_id, 'shipping_address_2', true );
            $user_address_city     = get_user_meta( $user_id, 'shipping_city', true );
            $user_address_postcode = get_user_meta( $user_id, 'shipping_postcode', true );
            $user_address_state    = get_user_meta( $user_id, 'shipping_state', true );
            $user_address_country  = get_user_meta( $user_id, 'shipping_country', true );
            
            if( !empty( $user_address_1 ) ) $address_1 = $user_address_1;
            if( !empty( $user_address_2 ) ) $address_1 = $user_address_2;
            if( !empty( $user_address_city ) ) $address_city = $user_address_city;
            if( !empty( $user_address_state ) ) $address_state = $user_address_state;
            if( !empty( $user_address_postcode ) ) $address_postcode = $user_address_postcode;
            if( !empty( $user_address_country ) ) $address_country = $user_address_country;
        }
        
        $first_name = $first_name ?? '';
        $last_name  = $last_name ?? '';
        
        if( $first_name == $last_name || empty( $last_name ) ) $last_name = '';
        
        $address = $first_name.' '.$last_name."\n";
        if( !empty( $address_1 ) ) $address .= $address_1."\n";
        if( !empty( $address_2 ) ) $address .= $address_2."\n";
        if( !empty( $address_city ) ) $address .= $address_city."\n";
        if( !empty( $address_state ) ) $address .= $address_state."\n";
        if( !empty( $address_postcode ) ) $address .= $address_postcode."\n";
        if( !empty( $address_country ) ) $address .= $address_country."\n";
        return nl2br( $address );
    }
    
    /**
     * @param $details
     */
    public static function render_invoice( $details ) {
        $dompdf = new Dompdf();
        ob_start();
        include get_template_directory().'/template-parts/mf-invoice.php';
        $output = ob_get_clean();
        $dompdf->loadHtml( $output );
        $dompdf->setPaper( 'A6' );
        $dompdf->render();
        file_put_contents( ABSPATH.'/printables/'.$details['invoice_id'].'.pdf', $dompdf->output() );
    }
    
    public static function generate_packing_slips_and_csv() {
        if( get_current_blog_id() != 2 ) return;
        
        $emails = [];
        // Get backer emails
        for( $backer = 1; $backer <= 2000; $backer++ ) {
            if( in_array( $backer, [
                878, 844, 101, 1407, 1368, 732, 703, 0, 711, 1438, 1164, 208, 248, 1031, 1329, 1396, 1571, 517, 1071, 1209, 157, 1706, 1605, 1369, 116, 1194, 940, 76, 941, 507, 628, 368, 1590, 440, 751, 1046, 1123, 1004, 195, 1168, 785, 54, 567, 427, 269, 1413, 1063, 665, 63, 128, 535, 980, 1615, 828, 1516, 1383, 1633, 982, 1120, 832, 1622, 205, 211, 1566, 575, 842, 1621, 1718, 1491, 659, 1033, 1582, 1771, 1616, 560, 1446, 455, 65, 333, 1700, 20, 1078, 1655, 1507, 293, 1666, 1720, 1243, 1482, 698, 969, 1725, 857, 1347, 1391, 1585, 1715, 1624, 1242, 1252, 1581, 1315, 900, 1142, 850, 19, 311, 1575, 799, 74, 182, 1713, 697, 374, 720, 1551, 1480, 817, 1595, 589, 1701, 849, 337, 55, 476, 1628, 1337, 1459, 1156, 1567, 1040, 1584, 1634, 1348, 377, 336, 968, 1533, 802, 488, 1320, 196, 278, 1594, 1094, 1711, 1305, 1042, 397, 1649, 954, 1177, 412, 614, 510, 1052, 1281, 421, 1317, 275, 1513, 225, 494, 1550, 1495, 165, 358, 1030, 1126, 760, 553, 1712, 1183, 321, 270, 979, 1608, 1732, 12, 843, 782, 493, 1170, 42, 426, 118, 443, 1122, 218, 783, 1346, 1096, 67, 1080, 1222, 1738, 1241, 1039, 887, 1101, 1197, 189, 1770, 1231, 910, 1256, 238, 213, 1512, 1344, 1215, 800, 1587, 926, 439, 378, 547, 470, 1379, 1084, 1596, 839, 1300, 1224, 788, 1749, 743, 1636, 200, 290, 557, 943, 1145, 775, 809, 1412, 5, 938, 1354, 1104, 756, 1563, 221, 254, 1528, 604, 1588, 1095, 177, 622, 90, 1340, 261, 430, 1239, 1729, 1041, 766, 658, 1273, 307, 848, 1221, 473, 424, 1316, 818, 381, 669, 382, 1552, 1271, 719, 801, 1574, 611, 137, 1057, 110, 1530, 1387, 444, 471, 1181, 49, 1319, 1641, 1626, 745, 769, 1011, 995, 657, 838, 1056, 759, 1714, 1024, 551, 342, 271, 1504, 135, 1028, 777, 710, 1643, 1010, 894, 392, 1275, 513, 1208, 1035, 297, 728, 974, 1051, 1723, 924, 1486, 1307, 925, 1144, 902, 1500, 340, 1187, 896, 1288, 1448, 1478, 1027, 37, 1014, 907, 1456, 1026, 1740, 190, 913, 428, 1601, 454, 641, 267, 583, 1741, 1265, 1543, 1606, 1708, 353, 133, 399, 595, 1445, 21, 394, 1477, 1282, 369, 184, 1529, 1260, 1679, 1772, 1599, 1746, 779, 142, 831, 836, 923, 1032, 384, 1637, 1163, 935, 1699, 302, 865, 548, 13, 484, 1255, 478, 640, 38, 773, 1220, 883, 1578, 1417, 1591, 1773, 967, 1099, 232, 445, 550, 871, 1167, 1235, 7, 413, 928, 1695, 1678, 781, 1090, 533, 879, 102, 168, 620, 652, 1455, 1520, 1429, 1623, 761, 1351, 1326, 323, 245, 356, 167, 352, 521, 587, 1073, 1753, 615, 1658, 18, 626, 1294, 1457, 1250, 1343, 1060, 317, 971, 1548, 1393, 1648, 40, 62, 1336, 407, 451, 1580, 1380, 176, 1748, 422, 767, 959, 1157, 1558, 204, 1540, 1642, 758, 1754, 1573, 1546, 1149, 1489, 740, 737, 332, 543, 393, 678, 594, 1230, 284, 11, 780, 691, 1472, 771, 793, 357, 1650, 1165, 1475, 343, 1604, 1067, 1515, 1269, 17, 906, 949, 1009, 784, 1141, 914, 633, 1696, 166, 835, 904, 1062, 387, 313, 1161, 257, 1251, 483, 565, 1353, 1638, 224, 509, 892, 1726, 1000, 1061, 1439, 1214, 1556, 1416, 175, 794, 815, 1502, 1127, 268, 812, 320, 1747, 1645, 762, 1207, 1114, 402, 1470, 104, 635, 134, 288, 1188, 579, 148, 1212, 1237, 1484, 1216, 1450, 1437, 4, 1428, 1618, 306, 1485, 1247, 1226, 281, 95, 1331, 816, 1248, 763, 918, 1422, 1058, 1418, 1435, 1213, 1432, 1278, 939, 1311, 1627, 1146, 1555, 1525, 518, 1082, 376, 753, 119, 1498, 1291, 83, 1698, 605, 599, 541, 1406, 1756, 631, 981, 992, 161, 1171, 1503, 178, 1350, 718, 389, 1651, 813, 1541, 141, 1098, 523, 1395, 1514, 890, 1766, 625, 1229, 1137, 355, 778, 1113, 886, 301, 616, 558, 1703, 972, 1761, 873, 34, 449, 741, 676, 310, 623, 1, 1639, 738, 233, 1264, 1360, 1116, 1664, 1722, 123, 689, 642, 734, 988, 1681, 193, 1070, 1016, 1611, 888, 197, 546, 646, 373, 672, 1677, 1107, 671, 1254, 417, 1662, 1535, 486, 33, 874, 811, 655, 648, 1211, 106, 305, 1029, 276, 1542, 526, 1049, 571, 1086, 909, 1538, 861, 1403, 1554, 1283, 258, 109, 556, 71, 1487, 1745, 94, 952, 1737, 1092, 897, 349, 496, 549, 61, 808, 303, 1724, 1217, 1569, 870, 1373, 192, 1721, 350, 1449, 298, 1397, 986, 942, 1359, 1103, 1465, 1612, 388, 1129, 917, 774, 327, 505, 1474, 1378, 1667, 922, 1553, 610, 1238, 362, 272, 87, 1193, 1717, 1671, 170, 603, 545, 472, 1172, 1719, 765, 1676, 601, 322, 114, 1312, 854, 479, 911, 1225, 729, 264, 953, 822, 889, 612, 79, 124, 522, 1258, 84, 1613, 1743, 467, 699, 1074, 1458, 295, 661, 1365, 1444, 44, 256, 251, 830, 975, 1476, 1635, 1140, 1076, 1370, 2, 180, 1025, 121, 86, 500, 682, 860, 1296, 593, 677, 1692, 1065, 916, 1440, 973, 1536, 1631, 725, 448, 1240, 1750, 287, 837, 1119, 46, 1763, 1451, 1736, 1386, 927, 823, 1468, 1190, 159, 433, 99, 958, 1687, 144, 1521, 149, 554, 591, 1121, 882, 956, 183, 210, 1665, 1266, 559, 656, 998, 1089, 1518, 411, 1301, 898, 714, 1289, 1688, 544, 702, 1166, 519, 827, 108, 16, 795, 1366, 497, 1479, 73, 1249, 1757, 30, 475, 885, 372, 319, 8, 687, 1037, 35, 354, 1730, 1186, 1355, 792, 1467, 122, 1670, 1219, 947, 1545, 1357, 146, 1497, 1659, 1130, 936, 994, 234, 1019, 1068, 1154, 1132, 821, 416, 366, 569, 1598, 1576, 3, 103, 425, 884, 1002, 683, 1508, 1661, 1093, 1646, 945, 499, 66, 747, 47, 429, 716, 1083, 512, 1425, 1034, 1744, 1223, 1460, 1494, 1349, 1399, 596, 1506, 1200, 1341, 112, 1431, 919, 1108, 1442, 1547, 1178, 1443, 14, 572, 730, 724, 576, 977, 147, 396, 1185, 1501, 1297, 1769, 754, 966, 1176, 186, 351, 1021, 1363, 755, 627, 1005, 50, 1158, 1202, 899, 841, 1304, 684, 127, 239, 999, 1203, 1313, 893, 667, 252, 515, 375, 997, 1660, 632, 1179, 820, 588, 735, 790, 1707, 26, 85, 348, 872, 296, 160, 964, 325, 637, 536, 608, 1075, 960, 1630, 1505, 946, 263, 649, 1572, 1657, 1697, 59, 846, 1592, 1109, 639, 1441, 1559, 1292, 420, 1322, 1081, 1110, 624, 987, 864, 174, 1420, 328, 1620, 1519, 1408, 1376, 1583, 1138, 1153, 1565, 1338, 1522, 463, 1131, 1405, 1632, 726, 1008, 385, 787, 1143, 1532, 1436, 768, 1199, 1050, 581, 564, 1321, 1325, 294, 855, 881, 1510, 1710, 236, 1262, 1690, 431, 247, 1669, 459, 466, 692, 1328, 1488, 534, 152, 1162, 361, 406, 1427, 1483, 1586, 1334, 1453, 1549, 242, 367, 138, 1424, 1233, 365, 1286, 450, 1106, 520, 1492, 1184, 880, 733, 1085, 405, 1464, 803, 91, 498, 951, 643, 171, 1473, 1135, 868, 1298, 107, 98, 469, 1079, 598, 1672, 1625, 1654, 259, 88, 1125, 1705, 1693, 289, 1310, 1358, 1402, 1704, 1411, 1526, 1012, 1751, 201, 212, 262, 829, 948, 1394, 1728, 243, 341, 921, 1006, 584, 53, 750, 1013, 51, 28, 1245, 1290, 852, 136, 531, 315, 1105, 1259, 441, 993, 1619, 1685, 419, 1683, 1727, 749, 1246, 723, 1115, 739, 530, 408, 31, 226, 1195, 508, 6, 1332, 511, 696, 789, 920, 937, 1414, 770, 111, 1228, 1191, 1324, 1136, 1716, 1323, 786, 1234, 57, 359, 1640, 1493, 452, 1023, 220, 326, 991, 617, 1760, 1560, 563, 532, 1364, 432, 1454, 506, 1673, 1433, 580, 1534, 482, 1570, 990, 700, 203, 363, 1306, 1617, 490, 1562, 840, 338, 344, 834, 1044, 1206, 934, 1421, 1531, 1675, 436, 679, 1765, 231, 1511, 634, 1653, 1117, 418, 1204, 862, 314, 985, 1471, 206, 1609, 1361, 1564, 1755, 853, 1499, 961, 199, 1593, 253, 1597, 1048, 1768, 1064, 903, 191, 1603, 537, 1205, 905, 1088, 1539, 807, 748, 131, 1739, 701, 930, 955, 1053, 1647, 277, 1466, 1285, 291, 752, 731, 901, 1270, 1385, 1481, 1434, 1160, 1244, 316, 1674, 590, 92, 1759, 32, 1517, 414, 1680, 457, 1461, 1007, 585, 957, 1735, 285, 222, 1257, 364, 1303, 1155, 1043, 1426, 514, 179, 819, 933, 636, 162, 976, 464, 1045, 10, 1610, 540, 746
            ] ) ) continue;
            $users = get_users( [
                                    'meta_key'   => 'mc_mf_backer_id',
                                    'meta_value' => $backer,
                                ] );
            if( empty( $users ) ) continue;
            $emails[] = $users[0]->user_email;
        }
        
        $orders = \MC_Woo_Order_Functions::get_all_site_order_ids( [ 'wc-processing' ] );
        foreach( $orders as $order_id ) {
            if( in_array( $order_id, [
                1346, 1214, 1131, 525, 1051, 535, 1164, 536, 1086, 353, 1098, 1179, 619, 988, 10, 251, 004, 1254, 1147, 605, 363, 422, 966, 351, 381, 987, 1094, 1, 110, 589, 372, 361, 360, 1223, 371, 1226, 1262, 398, 1304, 1207, 1261, 600, 1182, 540, 1, 238, 972, 393, 627, 554, 515, 428, 618, 1039, 1240, 1299, 530, 1340, 514, 573, 349, 1237, 1024, 1010, 1341, 1278, 1253, 390, 1081, 632, 559, 1021, 1298, 1244, 1178, 1134, 1019, 534, 1338, 1096, 558, 1221, 1120, 990, 1154, 1089, 1228, 1273, 377, 1245, 405, 1, 219, 356, 1114, 556, 1119, 510, 980, 1185, 389, 1246, 1043, 541, 368, 542, 1013, 484, 414, 404, 1248, 1000, 1052, 592, 599, 1295, 383, 382, 974, 391, 1148, 660, 547, 545, 579, 628, 1077, 622, 1216, 617, 1231, 593, 1294, 1075, 1158, 551, 602, 601, 1196, 1274, 1206, 511, 413, 412, 1345, 1243, 364, 532, 1264, 429, 1200, 996, 607, 1336, 1034, 1078, 1138, 1132, 1187, 1160, 1136, 1, 229, 1225, 1283, 1151, 1126, 1139, 1232, 955, 1224, 1159, 1122, 1125, 1279, 11, 451, 144, 1180, 1212, 1124, 1137, 1141, 1123, 1149, 1220, 1227, 1041, 1321, 624, 401, 1252, 1107, 598, 1152, 583, 517, 561, 407, 597, 590, 423, 991, 1342, 1307, 1267, 1, 022, 492, 488, 1143, 566, 1050, 1031, 375, 1241, 581, 415, 411, 508, 1257, 419, 11, 351, 016, 395, 1350, 396, 384, 1095, 358, 1184, 1157, 596, 1028, 357, 550, 370, 588, 518, 410, 1087, 350, 546, 994, 567, 555, 983, 386, 376, 516, 392, 970, 1199, 1, 222, 582, 408, 1088, 374, 1192, 432, 1, 011, 613, 426, 1005, 1, 112, 568, 626, 976, 427, 403, 13, 191, 270, 1343, 1250, 367, 1064, 1046, 958, 1, 539, 1045, 552, 424, 528, 612, 1117, 1002, 659, 1318, 570, 512, 1008, 585, 354, 421, 373, 1305, 1007, 1150, 957, 544, 420, 1076, 580, 1029, 489, 1271, 1012, 549, 513, 1235, 1266, 1194, 1121, 569, 533, 531, 485, 1090, 1, 023, 543, 486, 1, 302, 995, 1163, 1315, 565, 1092, 1314, 1316, 981, 1049, 553, 1018, 564, 595, 661, 1, 161, 402, 1083, 1033, 657, 1313, 1047, 1079, 1032, 1, 003, 577, 406, 1115, 1233, 960, 1109, 397, 978, 973, 606, 982, 658, 1217, 1269, 12, 031, 006, 509, 1, 105, 1155, 1215, 1173, 1284, 1030, 1108, 1339, 1146, 1128, 387, 1168, 490, 563, 1288, 587, 12, 551, 247, 979, 877, 1208, 507, 575, 586, 359, 977, 1301, 1074, 1017, 1, 213, 362, 1296, 881, 620, 1202, 975, 1140, 557, 1249, 323, 1209, 394, 13, 031, 001, 537, 623, 1320, 989, 572, 355, 1, 571, 1337, 878, 631, 630, 538, 1308, 1093, 1236, 430, 959, 1277, 967, 352, 11, 811, 174, 1292, 992, 1129, 1239, 1175, 379, 1073, 952, 560, 1035, 388, 997, 1, 142, 576, 954, 425, 998, 1218, 594, 1193, 1102, 1293, 526, 1026, 956, 365, 1259, 12, 101, 106, 385, 1084, 882, 971, 562, 1186, 603
            ] ) ) continue;
            $order = wc_get_order( $order_id );
            if( !method_exists( $order, 'get_billing_email' ) ) continue;
            $email = $order->get_billing_email();
            if( empty( $email ) ) continue;
            $emails[] = $email;
        }
        $emails = array_unique( $emails );
        
        $land_emails = "";
        
        $us_emails    = [];
        $nl_emails    = [];
        $empty_emails = [];
        foreach( $emails as $email ) {
            // Reset addresses
            $address_1        = '';
            $address_2        = '';
            $address_city     = '';
            $address_postcode = '';
            $address_state    = '';
            $address_country  = '';
            
            $orders = MC_Woo_Order_Functions::get_order_ids_by_email( $email, [ 'wc-processing' ] );
            foreach( $orders as $order_id ) {
                $order            = wc_get_order( $order_id );
                $first_name       = $order->get_billing_first_name();
                $last_name        = $order->get_billing_last_name();
                $address_1        = $order->get_shipping_address_1();
                $address_2        = $order->get_shipping_address_2();
                $address_city     = $order->get_shipping_city();
                $address_postcode = $order->get_shipping_postcode();
                $address_state    = $order->get_shipping_state();
                $address_country  = $order->get_shipping_country();
                break;
            }
            $user = get_user_by( 'email', $email );
            if( !empty( $user ) ) {
                $user_id    = $user->ID;
                $first_name = $user->first_name;
                $last_name  = $user->last_name;
                
                // Address
                $user_address_1        = get_user_meta( $user_id, 'shipping_address_1', true );
                $user_address_2        = get_user_meta( $user_id, 'shipping_address_2', true );
                $user_address_city     = get_user_meta( $user_id, 'shipping_city', true );
                $user_address_postcode = get_user_meta( $user_id, 'shipping_postcode', true );
                $user_address_state    = get_user_meta( $user_id, 'shipping_state', true );
                $user_address_country  = get_user_meta( $user_id, 'shipping_country', true );
                
                if( !empty( $user_address_1 ) ) $address_1 = $user_address_1;
                if( !empty( $user_address_2 ) ) $address_2 = $user_address_2;
                if( !empty( $user_address_city ) ) $address_city = $user_address_city;
                if( !empty( $user_address_state ) ) $address_state = $user_address_state;
                if( !empty( $user_address_postcode ) ) $address_postcode = $user_address_postcode;
                if( !empty( $user_address_country ) ) $address_country = $user_address_country;
            }
            $first_name = $first_name ?? '';
            $last_name  = $last_name ?? '';
            $name       = $first_name.' '.$last_name;
            $items      = self::get_items_by_email( $email );
            if( empty( $items ) ) continue;
            $item_count = $items['count'];
            $items      = $items['products'];
            
            $weight = self::get_weight_from_items( $items );
            
            $invoice_id = self::get_invoice_id_from_email( $email );
            if( strpos( $invoice_id, '0000-0000' ) !== false ) continue;
            $updated = get_option( $invoice_id.'_mf_items', '' );
            $updated = $updated != $items ? 'yes' : '';
            
            $user = [
                'email'            => $email,
                'backer_id'        => $backer_id = self::getBackerId( $user_id ),
                'order_ids'        => implode( ',', $orders ),
                'invoice-id'       => self::get_invoice_id_from_email( $email ),
                'first_name'       => $first_name,
                'last_name'        => $last_name,
                'name'             => $name,
                'address_1'        => $address_1 ?? '',
                'address_2'        => $address_2 ?? '',
                'address_city'     => $address_city ?? '',
                'address_postcode' => $address_postcode ?? '',
                'address_state'    => $address_state ?? '',
                'address_country'  => $address_country ?? '',
                'has_credits'      => self::hasRemainingCredits( $backer_id ),
                'items'            => $items,
                'items_count'      => $item_count ?? 0,
                'weight'           => $weight,
                'updated'          => $updated
            ];
            
            $address  = MC_Mythic_Frames_Functions::get_readable_address_from_email( $email );
            $products = $items;
            
            $details = [
                'email'      => $email,
                'invoice_id' => $invoice_id,
                'address'    => $address,
                'products'   => $products
            ];
            MC_Mythic_Frames_Functions::render_invoice( $details );
            
            foreach( $details['products'] ?? [] as $item ) {
                $product_type = $item['product_type'] ?? '';
                if( strpos( $product_type, 'land' ) === false ) continue;
                $land_emails .= $email."\n";
                break;
            }
            
            $country = strtolower( $address_country ?? '' );
            if( $country == 'us' ) {
                $us_emails[] = $user;
            } else if( !empty( $country ) ) {
                $nl_emails[] = $user;
            } else {
                $empty_emails[] = $user;
            }
        }
        
        $in_order = [
            'nl'    => $nl_emails,
            'us'    => $us_emails,
            'issue' => $empty_emails
        ];
        
        $zip_files = [];
        foreach( $in_order as $key => $user_section ) {
            $csv = [
                [
                    'email',
                    'backer_id',
                    'order_ids',
                    'invoice-id',
                    'first_name',
                    'last_name',
                    'name',
                    'address_1',
                    'address_2',
                    'address_city',
                    'address_postcode',
                    'address_state',
                    'address_country',
                    'used_credits',
                    'items',
                    'item_count',
                    'weight',
                    'updated'
                ]
            ];
            
            $file_path = ABSPATH.'/printables/shipping-'.$key.'.csv';
            $files     = [ $file_path ];
            foreach( $user_section as $user ) {
                $csv[]      = $user;
                $invoice_id = MC_Mythic_Frames_Functions::get_invoice_id_from_email( $user['email'] );
                $files[]    = ABSPATH.'/printables/'.$invoice_id.'.pdf';
            }
            
            if( file_exists( $file_path ) ) unlink( $file_path );
            
            $fp = fopen( $file_path, 'w' );
            foreach( $csv as $fields ) {
                if( isset( $fields['items'] ) ) {
                    $fields['items'] = self::convert_items_to_readable( $fields['items'] );
                }
                fputcsv( $fp, $fields );
            }
            fclose( $fp );
            
            $zipPath     = ABSPATH.'/printables-'.$key.'.zip';
            $zip_files[] = $zipPath;
            if( file_exists( $zipPath ) ) unlink( $zipPath );
            $zip = new ZipArchive();
            $zip->open( $zipPath, ZipArchive::CREATE );
            $zipUrl = MC_Url::pathToUrl( $zipPath );
            foreach( $files as $file ) {
                if( !file_exists( $file ) ) continue;
                $fileName = basename( $file );
                $zip->addFile( "{$file}", $fileName );
            }
            $zip->close();
            
            wp_mail( 'james@altersleeves.com', 'Zip done ('.$key.')', $zipUrl );
        }
        
        /*
        $zipPath = ABSPATH.'/printables.zip';
        if( file_exists( $zipPath ) ) unlink( $zipPath );
        $zip = new ZipArchive();
        $zip->open( $zipPath, ZipArchive::CREATE );
        $zipUrl = MC_Url::pathToUrl( $zipPath );
        foreach( $zip_files as $zip_file ) {
            if( !file_exists( $zip_file ) ) continue;
            $fileName = basename( $zip_file );
            $zip->addFile( "{$zip_file}", $fileName );
        }
        $zip->close();
                wp_mail( 'james@altersleeves.com', 'Zip done (ALL)', $zipUrl );
    */
        wp_mail( 'james@altersleeves.com', 'Land Missing', $land_emails );
    }
    
    public static function get_items_by_email( $email = '' ) {
        $items = [];
        
        $user = get_user_by( 'email', $email );
        if( !empty( $user ) ) {
            $user_id   = $user->ID;
            $backer_id = MC_Mythic_Frames_Functions::userIdToBackerId( $user_id );
        }
        if( empty( $backer_id ) ) $backer_id = 0;
        
        if( !empty( $backer_id ) ) {
            $user_credits = MC_Mythic_Frames_Functions::backerCredits( $backer_id );
            $team_id      = 1;
            foreach( MC_Mythic_Frames_Functions::sorted_teams() as $team ) {
                $set          = $user_credits['teams'][ $team_id ]['spent_set_credits'];
                $creature     = $user_credits['teams'][ $team_id ]['spent_creature_credits'];
                $non_creature = $user_credits['teams'][ $team_id ]['spent_non_creature_credits'];
                $land         = $user_credits['teams'][ $team_id ]['spent_land_credits'] ?? 0;
                $super_lands  = $user_credits['teams'][ $team_id ]['spent_super_land_credits'] ?? 0;
                $land         = $land + $super_lands;
                
                if( !empty( $set ) ) {
                    $items[ $team_id.'_set' ]     = [
                        'product_type' => 'set',
                        'team'         => $team_id,
                        'quantity'     => $set,
                        'weight'       => 400
                    ];
                    $items[ $team_id.'_deckbox' ] = [
                        'product_type' => 'deckbox',
                        'team'         => $team_id,
                        'quantity'     => $set
                    ];
                    if( in_array( $team_id, [ 1, 3, 10, 11 ] ) ) {
                        $items[ $team_id.'_commander_1' ] = [
                            'product_type' => 'commander',
                            'team'         => $team_id,
                            'quantity'     => $set,
                            'add'          => ' 1'
                        ];
                        $items[ $team_id.'_commander_2' ] = [
                            'product_type' => 'commander',
                            'team'         => $team_id,
                            'quantity'     => $set,
                            'add'          => ' 2'
                        ];
                    } else {
                        $items[ $team_id.'_commander' ] = [
                            'product_type' => 'commander',
                            'team'         => $team_id,
                            'quantity'     => $set
                        ];
                    }
                }
                
                if( !empty( $creature ) ) {
                    $items[ $team_id.'creature' ] = [
                        'product_type' => 'creature',
                        'team'         => $team_id,
                        'quantity'     => $set,
                        'weight'       => 60
                    ];
                }
                
                if( !empty( $non_creature ) ) {
                    $items[ $team_id.'_non-creature' ] = [
                        'product_type' => 'non_creature',
                        'team'         => $team_id,
                        'quantity'     => $set,
                        'source'       => 'Kickstarter',
                        'weight'       => 60
                    ];
                }
                
                if( !empty( $land ) ) {
                    $items[ $team_id.'_land' ] = [
                        'product_type' => 'land',
                        'team'         => $team_id,
                        'quantity'     => $land,
                        'weight'       => 60
                    ];
                }
                $team_id++;
            }
        }
        $team_id = 0;
        
        $orders = MC_Woo_Order_Functions::get_order_ids_by_email( $email, [ 'wc-processing' ] );
        if( !empty( $orders ) ) {
            foreach( $orders as $order_id ) {
                $order       = wc_get_order( $order_id );
                $order_items = $order->get_items();
                foreach( $order_items as $item ) {
                    $quantity = $item->get_quantity();   //Get the product QTY
                    
                    $product    = $item->get_product();
                    $product_id = $product->get_id();
                    // Only for product variation
                    if( $product->is_type( 'variation' ) ) {
                        // Get the variation attributes
                        $variation_attributes = $product->get_variation_attributes();
                        // Loop through each selected attributes
                        $type    = '';
                        $team_id = 0;
                        foreach( $variation_attributes as $attribute_taxonomy => $term_slug ) {
                            $term_slug = sanitize_title( $term_slug );
                            
                            // Get product attribute name or taxonomy
                            $taxonomy = str_replace( 'attribute_', '', $attribute_taxonomy );
                            
                            switch( $taxonomy ) {
                                case 'pa_design-team' :
                                    $team_id = $term_slug;
                                    break;
                                case 'pa_pack-type' :
                                    $attribute_value = get_term_by( 'slug', $term_slug, $taxonomy );
                                    if( empty( $attribute_value ) ) continue 2;
                                    $attribute_value = $attribute_value->name;
                                    $type            = $attribute_value;
                                    $type            = self::deduce_product_type( $type );
                                    break;
                                case 'playmat-design' :
                                    if( $product_id == 675 ) {
                                        $items[] = [
                                            'product_type' => 'Playmat',
                                            'team'         => 9,
                                            'add'          => ' Grenzo',
                                            'quantity'     => $quantity,
                                            'source'       => 'Order',
                                            'weight'       => 0
                                        ];
                                        
                                        continue 3;
                                    } else if( $product_id == 674 ) {
                                        $items[] = [
                                            'product_type' => 'Playmat',
                                            'team'         => 9,
                                            'add'          => ' Krenko',
                                            'quantity'     => $quantity,
                                            'source'       => 'Order',
                                            'weight'       => 0
                                        ];
                                        continue 3;
                                    }
                                    break;
                            }
                        }
                    }
                    
                    $product_name = $product->get_name();
                    if( empty( $team_id ) ) {
                        $team_id = self::deduce_team_to_team_id( $product_name );
                    }
                    if( empty( $type ) ) {
                        $type = self::deduce_product_type( $product_name );
                    }
                    if( $product->is_type( 'simple' ) ) {
                        $items[] = [
                            'product_type' => 'Playmat',
                            'team'         => $team_id,
                            'quantity'     => $item->get_quantity(),
                            'weight'       => 0
                        ];
                    } else if( !empty( $type ) ) {
                        $weight = $type == 'set' ? 400 : 60;
                        if( isset( $items[ $team_id.'_'.$type ] ) ) {
                            $items[ $team_id.'_'.$type ]['quantity'] += $quantity;
                        } else {
                            $items[ $team_id.'_'.$type ] = [
                                'product_type' => $type,
                                'team'         => $team_id,
                                'quantity'     => $quantity,
                                'weight'       => $weight
                            ];
                        }
                        if( $type == 'set' ) {
                            if( isset( $items[ $team_id.'_deckbox' ] ) ) {
                                $items[ $team_id.'_deckbox' ]['quantity'] += $quantity;
                            } else {
                                $items[ $team_id.'_deckbox' ] = [
                                    'product_type' => 'deckbox',
                                    'team'         => $team_id,
                                    'quantity'     => $quantity
                                ];
                            }
                            
                            $invoice_id = self::get_invoice_id_from_email( $email );
                            if( substr( $invoice_id, 0, 4 ) != '0000' ) {
                                if( in_array( $team_id, [ 1, 3, 10, 11 ] ) ) {
                                    $items[ $team_id.'_commander_1' ] = [
                                        'product_type' => 'commander',
                                        'team'         => $team_id,
                                        'quantity'     => $set,
                                        'add'          => ' 1'
                                    ];
                                    $items[ $team_id.'_commander_2' ] = [
                                        'product_type' => 'commander',
                                        'team'         => $team_id,
                                        'quantity'     => $set,
                                        'add'          => ' 2'
                                    ];
                                } else {
                                    $items[ $team_id.'_commander' ] = [
                                        'product_type' => 'commander',
                                        'team'         => $team_id,
                                        'quantity'     => $set
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        $invoice_id = self::get_invoice_id_from_email( $email );
        update_option( $invoice_id.'_mf_items', $items );
        if( empty( $items ) ) return $items;
        $count = 0;
        foreach( $items as $item ) {
            if( isset( $item['quantity'] ) ) $count += $item['quantity'];
        }
        return [ 'products' => $items, 'count' => $count ];
    }
    
    public static function default_products() {
        $products = [];
        for( $x = 1; $x <= 12; $x++ ) {
            $products[ $x ] = [ 'set' => 0, 'creature' => 0, 'non-creature' => 0, 'land' => 0, 'stretch' => 0, 'playmat' => 0 ];
        }
        return $products;
    }
    
    /**
     * @param array $items
     *
     * @return string
     */
    public static function convert_items_to_readable( $items = [] ) {
        if( empty( $items ) || !is_array( $items ) ) return '';
        $output = '';
        foreach( $items as $item ) {
            if( empty( $item ) || !is_array( $item ) ) continue;
            $output .= self::convert_item_to_readable_name( $item ).' x '.$item['quantity']."\n";
        }
        return $output;
    }
    
    public static function convert_item_to_readable_name( $item = [] ) {
        $name = '';
        if( isset( $item['team'] ) ) {
            $team = self::getTeam( $item['team'] );
            $name .= $team['design_name'] ?? '';
            $name .= ': ';
        }
        if( isset( $item['product_type'] ) ) {
            $product_type = $item['product_type'];
            if( strpos( $product_type, 'creature' ) !== false ||
                strpos( $product_type, 'land' ) !== false ) {
                $name .= 'BOOSTER - '.ucfirst( str_replace( '_', ' ', $product_type ) ).' ';
            } else {
                $name .= ucfirst( $product_type ).' ';
            }
        }
        if( isset( $item['add'] ) ) {
            $name .= '- '.$item['add'];
        }
        return $name;
    }
    
    public static function get_weight_from_items( $items = [] ) {
        if( empty( $items ) || !is_array( $items ) ) return '';
        $weight = 0;
        foreach( $items as $item ) {
            if( empty( $item ) || !is_array( $item ) || empty( $item['weight'] ) ) continue;
            $weight += $item['weight'];
        }
        return $weight;
    }
    
}
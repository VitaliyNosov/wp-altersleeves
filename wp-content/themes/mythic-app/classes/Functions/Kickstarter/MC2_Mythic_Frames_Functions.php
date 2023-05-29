<?php

namespace Mythic\Functions\Kickstarter;

use Intervention\Image\ImageManagerStatic;
use Mythic\Functions\MC2_Mtg_Printing_Functions;
use Mythic\Objects\MC2_Printing;
use Mythic\Helpers\MC2_Url;
use WP_Post;
use ZipArchive;

/**
 * Class MC2_Mythic_Frames
 *
 * @package Mythic\System
 */
class MC2_Mythic_Frames_Functions {

    public static function rand_printings($save = true) {

        $printings = [
            'creatures'    => self::creatures(),
            'noncreatures' => self::noncreatures(),
            'plains'       => self::plains(),
            'mountain'     => self::mountain(),
            'island'       => self::island(),
            'swamp'        => self::swamp(),
            'forest'       => self::forest(),
        ];
        if ( $save ) update_option('as_rand_printings', $printings);

        return $printings;

    }

    /**
     * @return array|int[]|WP_Post[]
     */
    public static function creatures() {
        $eras         = [];
        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 1,
            'orderby'        => 'rand',
            'meta_query'     => [
                [
                    'key'     => 'as_type_line',
                    'compare' => 'LIKE',
                    'value'   => 'Creature',
                ],
                [
                    'key'     => 'as_type_line',
                    'compare' => 'NOT LIKE',
                    'value'   => 'Token',
                ],
                [
                    'key'   => 'as_frame',
                    'value' => 2015,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);
        $eras[2015]   = $printings[0];

        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 3,
            'orderby'        => 'rand',
            'meta_query'     => [
                [
                    'key'     => 'as_type_line',
                    'compare' => 'LIKE',
                    'value'   => 'Creature',
                ],
                [
                    'key'     => 'as_type_line',
                    'compare' => 'NOT LIKE',
                    'value'   => 'Token',
                ],
                [
                    'key'   => 'as_frame',
                    'value' => 2003,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);
        $eras[2003]   = $printings[0];

        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 3,
            'orderby'        => 'rand',
            'meta_query'     => [
                [
                    'key'     => 'as_type_line',
                    'compare' => 'LIKE',
                    'value'   => 'Creature',
                ],
                [
                    'key'     => 'as_type_line',
                    'compare' => 'NOT LIKE',
                    'value'   => 'Token',
                ],
                [
                    'key'   => 'as_frame',
                    'value' => 1997,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);
        $eras[1997]   = $printings[0];

        return $eras;
    }

    /**
     * @return array|int[]|WP_Post[]
     */
    public static function noncreatures() {
        $eras         = [];
        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 3,
            'orderby'        => 'rand',
            'meta_query'     => [
                [

                    'key'     => 'as_type_line',
                    'compare' => 'IN',
                    'value'   => self::noncreature_typelines(),
                ],

                [
                    'key'   => 'as_frame',
                    'value' => 2015,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);
        $eras[2015]   = $printings[0];

        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 3,
            'orderby'        => 'rand',
            'meta_query'     => [
                [

                    'key'     => 'as_type_line',
                    'compare' => 'IN',
                    'value'   => self::noncreature_typelines(),
                ],

                [
                    'key'   => 'as_frame',
                    'value' => 2003,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);
        $eras[2003]   = $printings[0];

        $printingArgs = [
            'post_type'      => 'printing',
            'post_status'    => [ 'publish' ],
            'posts_per_page' => 3,
            'orderby'        => 'rand',
            'meta_query'     => [
                [

                    'key'     => 'as_type_line',
                    'compare' => 'IN',
                    'value'   => self::noncreature_typelines(),
                ],

                [
                    'key'   => 'as_frame',
                    'value' => 1997,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);
        $eras[1997]   = $printings[0];

        return $eras;
    }

    /**
     * @return array
     */
    public static function noncreature_typelines() {
        $typelines     = MC2_Mtg_Printing_Functions::typeLineTranslations();
        $typelines_non = [];
        foreach($typelines as $typeline_key => $typeline) {
            switch($typeline) {
                case MC2_Mtg_Printing_Functions::TYPE_LINE_NON_CREATURE :
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
        return self::land('Plains');
    }

    /**
     * @param string $type
     *
     * @return int|WP_Post
     */
    public static function land($type = '') {
        if ( empty($type) ) return 0;
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
                    'key'   => 'as_frame',
                    'value' => 2015,
                ],
                [
                    'key'     => 'as_full_art',
                    'compare' => '!=',
                    'value'   => 1,
                ],
            ],
            'fields'         => 'ids',
        ];
        $printings    = get_posts($printingArgs);

        return $printings[0] ?? 0;
    }

    /**
     * @return int|WP_Post
     */
    public static function mountain() {
        return self::land('Mountain');
    }

    /**
     * @return int|WP_Post
     */
    public static function island() {
        return self::land('Island');
    }

    /**
     * @return int|WP_Post
     */
    public static function swamp() {
        return self::land('Swamp');
    }

    /**
     * @return int|WP_Post
     */
    public static function forest() {
        return self::land('Forest');
    }

    public static function update_ks_image($team_id = 0) {

        $teams = self::sorted_teams();

        $mythic_frames_dir        = ABSPATH.'files/mythic-frames';
        $mythic_frames_sorted_dir = ABSPATH.'files/mf-generated';
        if ( !is_dir($mythic_frames_dir) ) {
            mkdir($mythic_frames_dir, 0755, true);
        }
        if ( !is_dir($mythic_frames_sorted_dir) ) {
            mkdir($mythic_frames_sorted_dir, 0755, true);
        }

        $count = 0;
        foreach($teams as $id => $team) {
            $count++;
            if ( !empty($team_id) and $team_id != $id ) continue;
            if( $count < 10 ) continue;

            $alterist_img         = $team['alterist_image'] ?? SITE_URL_IMG.'/user/profile.png';
            $alterist_image_media = attachment_url_to_postid($alterist_img);
            if ( !empty($alterist_image_media) ) {
                $url = wp_get_attachment_image_src($alterist_image_media, 'woocommerce_thumbnail')[0];
                if ( MC2_Url::is($url) ) $alterist_img = $url;
            }
            $alterist_image        = MC2_Url::urlToPath($alterist_img);
            $content_creator_img   = $team['content_creator_image'] ?? SITE_URL_IMG.'/user/profile.png';
            $content_creator_media = attachment_url_to_postid($content_creator_img);
            if ( !empty($content_creator_media) ) {
                $url = wp_get_attachment_image_src($content_creator_media, 'woocommerce_thumbnail')[0];
                if ( MC2_Url::is($url) ) $content_creator_img = $url;
            }
            $content_creator_image = MC2_Url::urlToPath($content_creator_img);

            // Files
            $template_path = $count > 8 ? DIR_THEME_IMAGES.'/mythic-frames/blank-stretch.png' : DIR_THEME_IMAGES.'/mythic-frames/blank.png';
            if ( $count == 9 || $count == 10 || $count == 11 || $count == 12 ) $template_path = DIR_THEME_IMAGES.'/mythic-frames/blank-stretch-met.png';

            //$twitter_icon  = MC2_DIR_SRC.'/img/generation/social-poster/social-icons/twitter.png';
            $canvas_path  = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/ad-template.png';
            $sleeve_image = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/sleeve-layer.png';
            $gleam_image  = DIR_THEME_IMAGES.'/generation/social-poster/sleeve-overlays/gleam-layer.png';

            $alter_id                   = $team['Creature'] ?? 0;
            $creature_image             = !empty($alter_id) ? MC2_Alter_Functions::image($alter_id) : self::placeholder_frame();
            $creature_image             = MC2_Url::urlToPath($creature_image);
            $printing_id                = MC2_Alter_Functions::printing($alter_id);
            $printing                   = new MC2_Printing( $printing_id);
            $creature_printing_selected = !empty($printing) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_selected = MC2_Url::urlToPath($creature_printing_selected);
            $creatures                  = self::creatures();
            $creature_printing_2015     = $count == 10 ? 38235 : $creatures[2015];
            $printing                   = new MC2_Printing( $creature_printing_2015);
            $creature_printing_2015     = !empty($creature_printing_2015) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_2015     = MC2_Url::urlToPath($creature_printing_2015);
            $creature_printing_2003     = $count == 10 ? 59844 : $creatures[2003];
            $printing                   = new MC2_Printing( $creature_printing_2003);
            $creature_printing_2003     = !empty($creature_printing_2003) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_2003     = MC2_Url::urlToPath($creature_printing_2003);
            $creature_printing_1997     = $count == 10 ? 70811 : $creatures[1997];
            $printing                   = new MC2_Printing( $creature_printing_1997);
            $creature_printing_1997     = !empty($creature_printing_1997) ? $printing->imgPng : self::placeholder_card();
            $creature_printing_1997     = MC2_Url::urlToPath($creature_printing_1997);

            $alter_id                      = $team['Non-Creature'] ?? 0;
            $noncreature_image             = !empty($alter_id) ? MC2_Alter_Functions::image($alter_id) : self::placeholder_frame();
            $noncreature_image             = MC2_Url::urlToPath($noncreature_image);
            $printing_id                   = MC2_Alter_Functions::printing($alter_id);
            $printing                      = new MC2_Printing( $printing_id);
            $noncreature_printing_selected = !empty($printing) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_selected = MC2_Url::urlToPath($noncreature_printing_selected);
            $noncreatures                  = self::noncreatures();
            $noncreature_printing_2015     = $count == 10 ? 50686 : $noncreatures[2015];
            $printing                      = new MC2_Printing( $noncreature_printing_2015);
            $noncreature_printing_2015     = !empty($noncreature_printing_2015) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_2015     = MC2_Url::urlToPath($noncreature_printing_2015);
            $noncreature_printing_2003     = $count == 10 ? 63093 : $noncreatures[2003];
            $printing                      = new MC2_Printing( $noncreature_printing_2003);
            $noncreature_printing_2003     = !empty($noncreature_printing_2003) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_2003     = MC2_Url::urlToPath($noncreature_printing_2003);
            $noncreature_printing_1997     = $count == 10 ? 38286 : $noncreatures[1997];
            $printing                      = new MC2_Printing( $noncreature_printing_1997);
            $noncreature_printing_1997     = !empty($noncreature_printing_1997) ? $printing->imgPng : self::placeholder_card();
            $noncreature_printing_1997     = MC2_Url::urlToPath($noncreature_printing_1997);

            $alter_id               = $team['Land'] ?? 0;
            $land_image             = !empty($alter_id) ? MC2_Alter_Functions::image($alter_id) : self::placeholder_frame();
            $land_image             = MC2_Url::urlToPath($land_image);
            $printing_id            = MC2_Alter_Functions::printing($alter_id);
            $printing               = new MC2_Printing( $printing_id);
            $land_printing_selected = !empty($printing) ? $printing->imgPng : self::placeholder_frame();
            $land_printing_selected = MC2_Url::urlToPath($land_printing_selected);

            // Establish Images
            $template = ImageManagerStatic::make($template_path);
            $crop1    = ImageManagerStatic::make($template_path);
            $crop2    = ImageManagerStatic::make($template_path);

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
            $template->crop($template_width, $template_height);

            /** Image Files */

            // Profile Pic - Artist
            $canvas      = ImageManagerStatic::canvas($profile_pic_d, $profile_pic_d);
            $profile_pic = ImageManagerStatic::make($alterist_image);
            $profile_pic->resize(null, $profile_pic_d, function($constraint) {
                $constraint->aspectRatio();
            });
            $profile_pic->crop($profile_pic_d, $profile_pic_d);
            $canvas->insert($profile_pic);
            $profile_frame = $noncreature_image != MC2_Url::urlToPath(self::placeholder_frame()) ? $noncreature_image : self::placeholder_frame();
            $profile_frame = $profile_frame == self::placeholder_frame() && $creature_image != MC2_Url::urlToPath(self::placeholder_frame()) ? $creature_image : self::placeholder_frame();
            $profile_frame = $profile_frame == self::placeholder_frame() && $land_image != MC2_Url::urlToPath(self::placeholder_frame()) ? $land_image : self::placeholder_frame();

            if ( $profile_frame != self::placeholder_frame() ) {
                $profile_frame = MC2_Url::urlToPath($profile_frame);

                $outer_canvas = ImageManagerStatic::canvas($profile_pic_d, $profile_pic_d);
                $canvas->crop(190, 190);
                $outer_canvas->insert($canvas, 'top-left', 5, 5);
                $alter_image = ImageManagerStatic::make($profile_frame);
                $alter_image->resize($profile_pic_d, null, function($constraint) {
                    $constraint->aspectRatio();
                });
                $alter_image->crop($profile_pic_d, 100, 0, 0);
                $outer_canvas->insert($alter_image);
                $alter_image->rotate(180);
                $outer_canvas->insert($alter_image, 'top-left', 0, 100);
                $canvas = $outer_canvas;
            }
            $template->insert($canvas, 'top_left', $artist_pic_offset_x, $profile_pics_y);

            // Profile Pic - Content Creator
            $canvas      = ImageManagerStatic::canvas(200, 200);
            $profile_pic = ImageManagerStatic::make($content_creator_image);
            $profile_pic->resize(null, 200, function($constraint) {
                $constraint->aspectRatio();
            });
            $profile_pic->crop(200, 200);
            $canvas->insert($profile_pic);
            $profile_frame = $noncreature_image != MC2_Url::urlToPath(self::placeholder_frame()) ? $noncreature_image : self::placeholder_frame();

            $profile_frame = $profile_frame == self::placeholder_frame() && $creature_image != MC2_Url::urlToPath(self::placeholder_frame()) ? $creature_image : self::placeholder_frame();
            $profile_frame = $profile_frame == self::placeholder_frame() && $land_image != MC2_Url::urlToPath(self::placeholder_frame()) ? $land_image : self::placeholder_frame();

            if ( $count == 9 ) {
                $jeff_frame_id = 187764;
                $profile_frame = MC2_Alter_Functions::image($jeff_frame_id);
            }

            if ( $profile_frame != self::placeholder_frame() ) {
                $profile_frame = MC2_Url::urlToPath($profile_frame);

                $outer_canvas = ImageManagerStatic::canvas($profile_pic_d, $profile_pic_d);
                $canvas->crop(190, 190);
                $outer_canvas->insert($canvas, 'top-left', 5, 5);
                $alter_image = ImageManagerStatic::make($profile_frame);
                $alter_image->resize($profile_pic_d, null, function($constraint) {
                    $constraint->aspectRatio();
                });
                $alter_image->crop($profile_pic_d, 100, 0, 0);
                $outer_canvas->insert($alter_image);
                $alter_image->rotate(180);
                $outer_canvas->insert($alter_image, 'top-left', 0, 100);
                $canvas = $outer_canvas;
            }
            $template->insert($canvas, 'top_left', $cc_pic_offset_x, $profile_pics_y);

            // Text
            $template->text('#'.$count, 10, 70, function($font) {
                $font->file(MC2_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf');
                $font->size(90);
                $font->color('#000');
                $font->valign('top');
                $font->align('left');
            });

            if ( $count > 13 ) {
                switch($count) {
                    case 9 :
                        $stretch = '$72,000';
                        break;
                    case 10 :
                        $stretch = '$80,000';
                        break;
                    case 11 :
                        $stretch = '$88,000';
                        break;
                    case 12 :
                        $stretch = '$96,000';
                        break;
                }
                $canvas = ImageManagerStatic::canvas(680, 240);
                $canvas->text($stretch, 340, 0, function($font) {
                    $font->file(MC2_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf');
                    $font->size(90);
                    $font->color('#fff');
                    $font->valign('top');
                    $font->align('center');
                });
                $template->insert($canvas, 'top-left', 0, 240);
            }

            $canvas = ImageManagerStatic::canvas(560, 240);
            $canvas->text($team['alterist_name'], 140, 0, function($font) {
                $font->file(MC2_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf');
                $font->size(18);
                $font->color('#fff');
                $font->valign('top');
                $font->align('center');
            });
            if ( !empty($team['alterist_twitter']) ) {
                //$canvas->insert($twitter_icon, 'top-left', 30, 20);
                $canvas->text('TW: '.$team['alterist_twitter'], 140, 25, function($font) {
                    $font->file(MC2_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf');
                    $font->size(13);
                    $font->color('#fff');
                    $font->valign('top');
                    $font->align('center');
                });
            }

            $canvas->text($team['content_creator_name'], 420, 0, function($font) {
                $font->file(MC2_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf');
                $font->size(18);
                $font->color('#fff');
                $font->valign('top');
                $font->align('center');
            });

            if ( !empty($team['content_creator_twitter']) ) {
                //$canvas->insert($twitter_icon, 'top-left', 310, 20);
                $canvas->text('TW: '.$team['content_creator_twitter'], 420, 25, function($font) {
                    $font->file(MC2_DIR_FONTS.'/Open_Sans/OpenSans-Bold.ttf');
                    $font->size(13);
                    $font->color('#fff');
                    $font->valign('top');
                    $font->align('center');
                });
            }

            $design_name = $team['design_name'] ?? '';
            $design_name = '#'.$design_name;

            $canvas->text($design_name, 280, 70, function($font) {
                $font->file(MC2_DIR_FONTS.'/Novecento_Sans/Novecento WideBold.otf');
                $font->size(40);
                $font->color('#fff');
                $font->valign('top');
                $font->align('center');
            });
            $template->insert($canvas, 'top_left', ( $artist_pic_offset_x - 40 ), $text_y);

            // Creature
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($creature_printing_selected);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($creature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_y);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_y);
            $canvas->insert($gleam_image);
            $canvas->resize($featured_w, $featured_h);
            $template->insert($canvas, 'top-left', $creature_selected_x, $creature_selected_y);

            // Non-Creature
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($noncreature_printing_selected);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($noncreature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($featured_w, $featured_h);
            $template->insert($canvas, 'top-left', $noncreature_selected_x, $noncreature_selected_y);
            // Land
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($land_printing_selected);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($land_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($featured_w, $featured_h);
            $template->insert($canvas, 'top-right', $land_x, $land_y);

            // Creature Example 1
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($creature_printing_1997);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($creature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($example_w, $example_h);
            $template->insert($canvas, 'top-left', $creature_example_1_x, $creature_example_1_y);
            // Creature Example 2
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($creature_printing_2003);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($creature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($example_w, $example_h);
            $template->insert($canvas, 'top-left', $creature_example_2_x, $creature_example_2_y);

            // Creature Example 3
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($creature_printing_2015);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($creature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($example_w, $example_h);
            $template->insert($canvas, 'top-left', $creature_example_3_x, $creature_example_3_y);
            // Non-Creature Example 1
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($noncreature_printing_1997);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($noncreature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($example_w, $example_h);
            $template->insert($canvas, 'top-left', $noncreature_example_1_x, $noncreature_example_1_y);
            // Non-Creature Example 2
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($noncreature_printing_2003);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($noncreature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($example_w, $example_h);
            $template->insert($canvas, 'top-left', $noncreature_example_2_x, $noncreature_example_2_y);
            // Non-Creature Example 3
            $canvas     = ImageManagerStatic::make($canvas_path);
            $card_image = ImageManagerStatic::make($noncreature_printing_2015);
            $card_image->resize($width, $height);
            $alter_image = ImageManagerStatic::make($noncreature_image);
            $alter_image->resize($width, $height);
            $sleeve_image = ImageManagerStatic::make($sleeve_image);
            $gleam_image  = ImageManagerStatic::make($gleam_image);
            $canvas->insert($sleeve_image);
            $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
            $canvas->insert($gleam_image);
            $canvas->resize($example_w, $example_h);
            $template->insert($canvas, 'top-left', $noncreature_example_3_x, $noncreature_example_3_y);

            if ( $count == 4 ) {

                $commander_1_id       = 187669;
                $commander_1_image    = !empty($commander_1_id) ? MC2_Alter_Functions::image($commander_1_id) : self::placeholder_frame();
                $commander_1_image    = MC2_Url::urlToPath($commander_1_image);
                $commander_1_printing = ABSPATH.'/wp-content/uploads/2021/04/khm-168-esika-god-of-the-tree.png';

                $canvas               = ImageManagerStatic::make($canvas_path);
                $commander_1_printing = \MC2_Vars::stringContains($commander_1_printing, 'unavailable-card.png') ? self::placeholder_card() : $commander_1_printing;
                $card_image           = ImageManagerStatic::make($commander_1_printing);
                $card_image->resize($width, $height);
                $commander_1_image = \MC2_Vars::stringContains($commander_1_image, 'unavailable-card.png') ? self::placeholder_card() : $commander_1_image;
                $alter_image       = ImageManagerStatic::make($commander_1_image);
                $alter_image->resize($width, $height);
                $sleeve_image = ImageManagerStatic::make($sleeve_image);
                $gleam_image  = ImageManagerStatic::make($gleam_image);
                $canvas->insert($sleeve_image);
                $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
                $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
                $canvas->insert($gleam_image);
                $canvas->resize($commander_w, $commander_h);
                $template->insert($canvas, 'bottom-left', 112, $commander_y);

                $commander_2_id       = 187655;
                $commander_2_image    = !empty($commander_2_id) ? MC2_Alter_Functions::image($commander_2_id) : self::placeholder_frame();
                $commander_2_image    = MC2_Url::urlToPath($commander_2_image);
                $commander_2_printing = ABSPATH.'/wp-content/uploads/2021/04/khm-168-the-prismatic-bridge.png';

                $canvas               = ImageManagerStatic::make($canvas_path);
                $commander_2_printing = \MC2_Vars::stringContains($commander_2_printing, 'unavailable-card.png') ? self::placeholder_card() : $commander_2_printing;
                $card_image           = ImageManagerStatic::make($commander_2_printing);
                $card_image->resize($width, $height);
                $commander_2_image = \MC2_Vars::stringContains($commander_2_image, 'unavailable-card.png') ? self::placeholder_card() : $commander_2_image;
                $alter_image       = ImageManagerStatic::make($commander_2_image);
                $alter_image->resize($width, $height);
                $sleeve_image = ImageManagerStatic::make($sleeve_image);
                $gleam_image  = ImageManagerStatic::make($gleam_image);
                $canvas->insert($sleeve_image);
                $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
                $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
                $canvas->insert($gleam_image);
                $canvas->resize($commander_w, $commander_h);
                $template->insert($canvas, 'bottom-left', 364, $commander_y);
            } else {

                $commander_1          = $team['Commander 1'] ?? [];
                $commander_1_id       = !empty($commander_1) ? $commander_1[0] : 0;
                $commander_1_image    = !empty($commander_1_id) ? MC2_Alter_Functions::image($commander_1_id) : self::placeholder_frame();
                $commander_1_image    = MC2_Url::urlToPath($commander_1_image);
                $printing_id          = MC2_Alter_Functions::printing($commander_1_id) ?? 0;
                $printing             = new MC2_Printing( $printing_id);
                $commander_1_printing = !empty($printing_id) ? $printing->imgPng : self::placeholder_frame();
                // Commander 1
                $canvas = ImageManagerStatic::make($canvas_path);
                if ( !empty($printing_id) ) {
                    $commander_1_printing = \MC2_Vars::stringContains($commander_1_printing, 'unavailable-card.png') ? self::placeholder_card() : $commander_1_printing;
                    $card_image           = ImageManagerStatic::make($commander_1_printing);
                    $card_image->resize($width, $height);
                }
                $commander_1_image = \MC2_Vars::stringContains($commander_1_image, 'unavailable-card.png') ? self::placeholder_card() : $commander_1_image;
                $alter_image       = ImageManagerStatic::make($commander_1_image);
                $alter_image->resize($width, $height);
                $sleeve_image = ImageManagerStatic::make($sleeve_image);
                $gleam_image  = ImageManagerStatic::make($gleam_image);
                $canvas->insert($sleeve_image);
                if ( !empty($printing_id) ) {
                    $canvas->insert($card_image, 'top-left', $offset_x, $offset_x);
                }
                $canvas->insert($alter_image, 'top-left', $offset_x, $offset_x);
                $canvas->insert($gleam_image);
                $canvas->resize($commander_w, $commander_h);
                $template->insert($canvas, 'bottom-left', $commander_x, $commander_y);
            }

            $template->save($mythic_frames_dir.'/'.$count.'.png');
            $template->save($mythic_frames_dir.'/'.$id.'.png');
            $template->save($mythic_frames_sorted_dir.'/'.$count.'.png');

            $crop1->insert($template);
            $crop1->crop(680, 740, 0, 1);
            $crop1->save($mythic_frames_dir.'/'.$count.'-top.png');

            $crop2->insert($template);
            $crop2->crop(680, 1440, 0, 820);
            $crop2->save($mythic_frames_dir.'/'.$count.'-bottom.png');

        }

        $zip_path = ABSPATH.'mf.zip';
        $zip      = new ZipArchive();
        $zip->open($zip_path, ZipArchive::CREATE);
        for($x = 1; $x <= 12; $x++) {
            $file_path = $mythic_frames_sorted_dir.'/'.$x.'.png';
            $zip->addFile($file_path, basename($file_path));
        }
        $zip->close();

    }

    public static function sorted_teams() {
        $sorted_teams = [];
        $teams        = self::teams();
        foreach($teams as $team_id => $team) {
            switch($team_id) {
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
                    $team['alterist_name']           = 'Hurley Burley';
                    $team['alterist_twitter']        = 'HurleyBurleyArt';
                    $team['content_creator_name']    = 'AliEldrazi';
                    $team['content_creator_twitter'] = 'AliEldrazi';
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
        ksort($sorted_teams);
        foreach($sorted_teams as $key => $sorted_team) {
            $description = $sorted_team['description'] ?? '';
            if ( !empty($description) ) $sorted_team['description'] = stripslashes($sorted_team['description']);
            $sorted_teams[ $sorted_team['alterist_id'] ] = $sorted_team;
            unset($sorted_teams[ $key ]);
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
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
                'Commander 3'          => []
            ],

        ];

        $stored_team_data = get_option('as_mf_teams', []);
        foreach($teams as $alterist_id => $team) {
            if ( !empty($stored_team_data[ $alterist_id ]) ) continue;
            $stored_team_data[ $alterist_id ] = $teams[ $alterist_id ];
        }

        return $stored_team_data;
    }

    /**
     * @return string
     */
    public static function placeholder_frame() {
        return SITE_URL_IMG.'/placeholder.png';
    }

    /**
     * @return string
     */
    public static function placeholder_card() {
        return SITE_URL_IMG.'/unavailable-card.png';
    }

}
<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Functions\MC_WP_Post_Functions;

/**
 * Class MC_Post
 *
 * @package Mythic_Core\Objects
 */
class MC_WP_Post {
    
    public $post;
    public $meta;
    
    /**
     * MC_WP_Post constructor.
     *
     * @param int $post_id
     * @param int $blog
     */
    public function __construct( int $post_id = 0, int $blog = 0 ) {
        switch( $blog ) {
            case $blog > 1 :
                $this->setPost( MC_WP_Post_Functions::getPost( $post_id, $blog ) );
                $this->setMeta( MC_WP_Post_Functions::getPostMeta( $post_id, $blog ) );
                break;
            default :
                $post = get_post( $post_id );
                $this->setPost( $post );
                $this->setMeta( get_post_meta( $post_id ) );
                break;
        }
    }
    
    /**
     * @param mixed $post
     */
    public function setPost( $post ) {
        $this->post = $post;
    }
    
    /**
     * @return mixed
     */
    public function getPost() {
        return $this->post;
    }
    
    /**
     * @param mixed $meta
     */
    public function setMeta( $meta ) {
        $this->meta = $meta;
    }
    
    /**
     * @return mixed
     */
    public function getMeta() {
        return $this->meta;
    }
    
}
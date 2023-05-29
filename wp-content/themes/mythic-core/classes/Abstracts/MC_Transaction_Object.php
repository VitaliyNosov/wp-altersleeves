<?php

namespace Mythic_Core\Abstracts;

/**
 * Class MC_Transaction_Object
 *
 * @package Mythic_Core\Abstracts
 */
abstract class MC_Transaction_Object {
    
    public $author_id;
    public $id;
    public $meta;
    public $slug;
    public $status;
    public $title;
    public $url;
    
    /**
     * MC_Transaction_Object constructor.
     *
     * @param $term
     */
    public function __construct( $term ) {
        $post = get_post( $term );
        if( empty( $post ) ) return;
        $id   = $post->ID;
        $slug = $post->post_name;
        $this->setAuthorId( $post->post_author );
        $this->setId( $id );
        $this->setMeta( get_post_meta( $id ) );
        $this->setStatus( $post->post_status );
        $this->setSlug( $slug );
        $this->setTitle( $post->post_title );
        $this->setUrl( get_site_url().'/'.$slug );
    }
    
    /**
     * @return mixed
     */
    public function getAuthorId() {
        return $this->author_id;
    }
    
    /**
     * @param mixed $author_id
     */
    public function setAuthorId( $author_id ) {
        $this->author_id = $author_id;
    }
    
    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId( int $id ) {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getMeta() {
        return $this->meta;
    }
    
    /**
     * @param mixed $meta
     */
    public function setMeta( $meta ) {
        $this->meta = $meta;
    }
    
    /**
     * @return string
     */
    public function getSlug() : string {
        return $this->slug;
    }
    
    /**
     * @param string $slug
     */
    public function setSlug( string $slug ) {
        $this->slug = $slug;
    }
    
    /**
     * @return string
     */
    public function getStatus() : string {
        return $this->status;
    }
    
    /**
     * @param string $status
     */
    public function setStatus( string $status ) {
        $this->status = $status;
    }
    
    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }
    
    /**
     * @param string $title
     */
    public function setTitle( string $title ) {
        $this->title = $title;
    }
    
    /**
     * @return string
     */
    public function getUrl() : string {
        return $this->url;
    }
    
    /**
     * @param string $url
     */
    public function setUrl( string $url ) {
        $this->url = $url;
    }
    
}
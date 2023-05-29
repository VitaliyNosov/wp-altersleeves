<?php

namespace Mythic\Objects;

use Mythic\Abstracts\MC2_Object;
use Mythic\Functions\MC2_Mtg_Card_Functions;

/**
 * Class MC2_ShortLink
 *
 * @package Mythic\Objects\Marketing
 */
class MC2_ShortLink extends MC2_Object {

    protected static $table_name = 'shortened_links';

    protected $id;
    protected $slug;
    protected $destination;
    protected $url;

    /**
     * @return mixed
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function set_id( $id ){
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function get_slug() {
        return $this->slug ?? MC2_Mtg_Card_Functions::generate_marketing_phrase();
    }

    /**
     * @param mixed $slug
     */
    public function set_slug( $slug ){
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function get_destination() {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     */
    public function set_destination( $destination ){
        $this->destination = $destination;
    }

    /**
     * @return mixed
     */
    public function get_url() {
        return $this->url ?? 'https://altrslv.co/'.$this->get_slug();
    }

    /**
     * @param mixed $url
     */
    public function set_url( $url ){
        $this->url = $url;
    }

    /**
     * @return false|int
     */
    public function save() {
        $id   = $this->get_id();
        $data = [
            'slug'        => $this->get_slug(),
            'destination' => $this->get_destination(),
            'url'         => $this->get_url(),
        ];

        if( empty( $id ) ) {
            return $this->insert( $data );
        } else {
            return $this->update( $data, [ 'id' => $id ] );
        }
    }

    public function remove() {
        $this->delete( [ 'id' => $this->get_id() ] );
    }

}
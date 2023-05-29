<?php

namespace Mythic\Objects\Mtg;

use Mythic\Abstracts\MC2_Object;

class MC2_Card extends MC2_Object {

    protected static $table_name = 'mtg_cards';

    protected $id;
    protected $name;
    protected $searchable_name;
    protected $type_line;
    protected $card_type;
    protected $edhrec_rank;
    protected $last_edited;
    protected $printings;
    protected $sets;

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
    public function get_name() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function set_name( $name ){
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function get_searchable_name() {
        return $this->searchable_name;
    }

    /**
     * @param mixed $searchable_name
     */
    public function set_searchable_name( $searchable_name ){
        $this->searchable_name = $searchable_name;
    }

    /**
     * @return mixed
     */
    public function get_type_line() {
        return $this->type_line;
    }

    /**
     * @param mixed $type_line
     */
    public function set_type_line( $type_line ){
        $this->type_line = $type_line;
    }

    /**
     * @return mixed
     */
    public function get_card_type() {
        return $this->card_type;
    }

    /**
     * @param mixed $card_type
     */
    public function set_card_type( $card_type ){
        $this->card_type = $card_type;
    }

    /**
     * @return mixed
     */
    public function get_edhrec_rank() {
        return $this->edhrec_rank;
    }

    /**
     * @param mixed $edhrec_rank
     */
    public function set_edhrec_rank( $edhrec_rank ){
        $this->edhrec_rank = $edhrec_rank;
    }

    /**
     * @return mixed
     */
    public function get_last_edited() {
        return $this->last_edited;
    }

    /**
     * @param mixed $last_edited
     */
    public function set_last_edited( $last_edited ){
        $this->last_edited = $last_edited;
    }

    /**
     * @return mixed
     */
    public function get_printings() {
        return $this->printings;
    }

    /**
     * @param mixed $printings
     */
    public function set_printings( $printings ){
        $this->printings = $printings;
    }

    /**
     * @return mixed
     */
    public function get_sets() {
        return $this->sets;
    }

    /**
     * @param mixed $sets
     */
    public function set_sets( $sets ){
        $this->sets = $sets;
    }

    /**
     * @return false|int
     */
    public function save() {
        global $wpdb;
        $id = $this->get_id();

        $data = [
            'name'            => $this->get_name(),
            'searchable_name' => $this->get_searchable_name(),
            'type_line'       => $this->get_type_line(),
            'card_type'       => $this->get_card_type(),
            'edhrec_rank'     => $this->get_edhrec_rank(),
            'last_edited'     => $this->get_last_edited(),
        ];

        if( empty( $id ) ) {
            $insert = $this->insert( $data );
            if( empty( $insert ) ) return false;
            $this->set_id( $wpdb->insert_id );
            return $insert;
        } else {
            return $this->update( $data, [ 'id' => $id ] );
        }
    }

}
<?php

namespace Mythic\Objects\Mtg;

class MC2_Printing extends MC2_Card {

    protected static $table_name = 'mtg_printings';

    protected $card_id;
    protected $scryfall_id;
    protected $face;
    protected $set_name;
    protected $set_code;
    protected $collector_number;
    protected $lang;
    protected $border_color;
    protected $frame;
    protected $edhrec_rank;
    protected $png;
    protected $jpg_large;
    protected $jpg_normal;
    protected $last_edited;


    public function __construct( $data = null ) {
        parent::__construct( $data );
        $card_details = (array) new MC2_Card($this->get_card_id());
        $this->allocate_values($card_details);
    }

    /**
     * @return mixed
     */
    public function get_card_id() {
        return $this->card_id;
    }

    /**
     * @param mixed $card_id
     */
    public function set_card_id( $card_id ){
        $this->card_id = $card_id;
    }

    /**
     * @return mixed
     */
    public function get_scryfall_id() {
        return $this->scryfall_id;
    }

    /**
     * @param mixed $scryfall_id
     */
    public function set_scryfall_id( $scryfall_id ){
        $this->scryfall_id = $scryfall_id;
    }

    /**
     * @return mixed
     */
    public function get_face() {
        return $this->face;
    }

    /**
     * @param mixed $face
     */
    public function set_face( $face ){
        $this->face = $face;
    }

    /**
     * @return mixed
     */
    public function get_set_name() {
        return $this->set_name;
    }

    /**
     * @param mixed $set_name
     */
    public function set_set_name( $set_name ){
        $this->set_name = $set_name;
    }

    /**
     * @return mixed
     */
    public function get_set_code() {
        return $this->set_code;
    }

    /**
     * @param mixed $set_code
     */
    public function set_set_code( $set_code ){
        $this->set_code = $set_code;
    }

    /**
     * @return mixed
     */
    public function get_collector_number() {
        return $this->collector_number;
    }

    /**
     * @param mixed $collector_number
     */
    public function set_collector_number( $collector_number ){
        $this->collector_number = $collector_number;
    }

    /**
     * @return mixed
     */
    public function get_lang() {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function set_lang( $lang ){
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function get_border_color() {
        return $this->border_color;
    }

    /**
     * @param mixed $border_color
     */
    public function set_border_color( $border_color ){
        $this->border_color = $border_color;
    }

    /**
     * @return mixed
     */
    public function get_frame() {
        return $this->frame;
    }

    /**
     * @param mixed $frame
     */
    public function set_frame( $frame ){
        $this->frame = $frame;
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
    public function get_png() {
        return $this->png;
    }

    /**
     * @param mixed $png
     */
    public function set_png( $png ){
        $this->png = $png;
    }

    /**
     * @return mixed
     */
    public function get_jpg_large() {
        return $this->jpg_large;
    }

    /**
     * @param mixed $jpg_large
     */
    public function set_jpg_large( $jpg_large ){
        $this->jpg_large = $jpg_large;
    }

    /**
     * @return mixed
     */
    public function get_jpg_normal() {
        return $this->jpg_normal;
    }

    /**
     * @param mixed $jpg_normal
     */
    public function set_jpg_normal( $jpg_normal ){
        $this->jpg_normal = $jpg_normal;
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



}
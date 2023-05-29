<?php

namespace Mythic\Objects\Finance;

use Mythic\Abstracts\MC2_Object;

/**
 * Class MC2_Transaction
 *
 * @package Mythic\Objects
 */
class MC2_Transaction extends MC2_Object {

    protected static $table_name = 'transactions';

    public $id;
    public $type;
    public $action_id;
    public $date;
    public $value;
    public $details;
    public $message;
    public $user_id;
    public $order_id;
    public $product_id;
    public $currency;

    /**
     * @return mixed
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function set_id( $id ) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function set_type( $type ) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function get_action_id() {
        return $this->action_id;
    }

    /**
     * @param mixed $action_id
     */
    public function set_action_id( $action_id ) {
        $this->action_id = $action_id;
    }

    /**
     * @return mixed
     */
    public function get_date() {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function set_date( $date ) {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function get_value() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function set_value( $value ) {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function get_details() {
        return $this->details;
    }

    /**
     * @param mixed $details
     */
    public function set_details( $details ) {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function set_message( $message ) {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function get_user_id() {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function set_user_id( $user_id ) {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function get_order_id() {
        return $this->order_id;
    }

    /**
     * @param mixed $order_id
     */
    public function set_order_id( $order_id ) {
        $this->order_id = $order_id;
    }

    /**
     * @return mixed
     */
    public function get_product_id() {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     */
    public function set_product_id( $product_id ) {
        $this->product_id = $product_id;
    }

    /**
     * @return mixed
     */
    public function get_currency() {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function set_currency( $currency ) {
        $this->currency = $currency;
    }

}
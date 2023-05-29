<?php

namespace Mythic_Core\Objects;

use Mythic_Core\Functions\MC_Transaction_Functions;

/**
 * Class MC_Transaction
 *
 * @package Mythic_Core\Objects
 */
class MC_Transaction {
    
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
     * MC_Transaction constructor.
     *
     * @param $id
     */
    public function __construct( $id ) {
        if( empty( $transaction = MC_Transaction_Functions::getTransactionData( $id ) ) ) return;
        $this->id         = $transaction->id;
        $this->type       = $transaction->type;
        $this->action_id  = $transaction->action_id;
        $this->date       = $transaction->date;
        $this->value      = $transaction->value;
        $this->details    = $transaction->details;
        $this->message    = $transaction->message;
        $this->user_id    = $transaction->user_id;
        $this->order_id   = $transaction->order_id;
        $this->product_id = $transaction->product_id;
        $this->currency   = $transaction->currency;
    }
    
    public function getId() : int {
        return $this->id;
    }
    
    public function getType() : string {
        return $this->type;
    }
    
    public function getActionId() : string {
        return $this->action_id;
    }
    
    public function getDate() : string {
        return $this->date;
    }
    
    public function getValue() : int {
        return $this->value;
    }
    
    public function getDetails() : string {
        return $this->details;
    }
    
    public function getMessage() : string {
        return $this->message;
    }
    
    public function getUserId() : int {
        return $this->user_id;
    }
    
    public function getOrderId() : int {
        return $this->order_id;
    }
    
    public function getProductId() : int {
        return $this->product_id;
    }
    
    public function getCurrency() : string {
        return $this->currency;
    }
    
}
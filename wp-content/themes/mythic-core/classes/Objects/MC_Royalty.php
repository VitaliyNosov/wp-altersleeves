<?php

namespace Mythic_Core\Objects;

/**
 * Class Royalty
 *
 * @package Mythic_Core\Objects
 */
class MC_Royalty {
    
    const TABLE_NAME     = 'as_royalties';
    const DB_ID          = 'id';
    const DB_DATE        = 'date';
    const DB_CLEARED     = 'cleared';
    public $quantity;
    public $date;
    public $type;
    public $cleared;
    public $value;
    protected $id;
    protected $alteristId;
    protected $orderId;
    protected $productId;
    
    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function setId( $id ) {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getAlteristId() {
        return $this->alteristId;
    }
    
    /**
     * @param mixed $idCreator
     */
    public function setAlteristId( $idCreator ) {
        $this->alteristId = $idCreator;
    }
    
    /**
     * @return mixed
     */
    public function getOrderId() {
        return $this->orderId;
    }
    
    /**
     * @param mixed $order_id
     */
    public function setOrderId( $order_id ) {
        $this->orderId = $order_id;
    }
    
    /**
     * @return mixed
     */
    public function getProductId() {
        return $this->productId;
    }
    
    /**
     * @param mixed $product_id
     */
    public function setProductId( $product_id ) {
        $this->productId = $product_id;
    }
    
    /**
     * @return mixed
     */
    public function getQuantity() {
        return $this->quantity;
    }
    
    /**
     * @param mixed $quantity
     */
    public function setQuantity( $quantity ) {
        $this->quantity = $quantity;
    }
    
    /**
     * @return mixed
     */
    public function getDate() {
        return $this->date;
    }
    
    /**
     * @param mixed $date
     */
    public function setDate( $date ) {
        $this->date = $date;
    }
    
    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * @param mixed $type
     */
    public function setType( $type ) {
        $this->type = $type;
    }
    
    /**
     * @return mixed
     */
    public function getCleared() {
        return $this->cleared;
    }
    
    /**
     * @param mixed $cleared
     */
    public function setCleared( $cleared ) {
        $this->cleared = $cleared;
    }
    
    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * @param mixed $value
     */
    public function setValue( $value ) {
        $this->value = $value;
    }
    
}
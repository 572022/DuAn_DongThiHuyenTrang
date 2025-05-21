<?php
class ListGioHang {
    private $list = array();
    public function __construct() {
        $this->list = array();
    }
    public function getList() {
        return $this->list;
    }
    public function setList($list) {
        $this->list = $list;
    }
    public function them($giohang) {
        foreach ($this->list as $son) {
            if ($giohang->getBill_id() == $son->getBill_id()) { 
                $son->setQuantity($son->getQuantity() + 1);
                return;
            }
        }
        
        $this->list[] = $giohang;
    }
    public function xoa($id){
        foreach ($this->list as $key => $son) {
            if ($son->getBill_id() == $id) {
                unset($this->list[$key]);
                return;
            }
        }
    }
    public function tang($id){
        foreach ($this->list as $key => $son) {
            if ($son->getBill_id() == $id) {
                $son->setQuantity($son->getQuantity() + 1);
                return;
            }
        }
    }
    public function giam($id){
        foreach ($this->list as $key => $son) {
            if ($son->getBill_id() == $id) {
                $son->setQuantity($son->getQuantity() - 1 );
                return;
            }
        }
    }
}
class Cart {
    private $id_table;
    private $bill_id;
    private $quantity;
    private $price;
    private $status;

    public function __construct($id_table, $bill_id, $quantity, $price, $status) {
        $this->id_table = $id_table;
        $this->bill_id = $bill_id;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->status = $status;
    }

    // Getter và Setter cho id_table
    public function getId_table() {
        return $this->id_table;
    }
    public function setId_table($id_table) {
        $this->id_table = $id_table;
    }

    // Getter và Setter cho bill_id
    public function getBill_id() {
        return $this->bill_id;
    }
    public function setBill_id($bill_id) {
        $this->bill_id = $bill_id;
    }

    // Getter và Setter cho quantity
    public function getQuantity() {
        return $this->quantity;
    }
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    // Getter và Setter cho price
    public function getPrice() {
        return $this->price;
    }
    public function setPrice($price) {
        $this->price = $price;
    }

    // Getter và Setter cho status
    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }
}


?>
<?php
class Address {
    private $addressId;
    private $installOrderId;
    private $removalOrderId;
    private $installStart;
    private $installOrder;
    private $houseNumber;
    private $streetName;
    private $city;
    private $zip4;

    public function __construct($aID) {
        global $database;
        
        $this->addressId = $aID;

        $sql = "SELECT * FROM " . TABLE_ADDRESSES . " WHERE address_id = '". $aID . "'";
        $query = $database->query($sql);
        if ($result = $database->fetch_array($query)) {
            $this->houseNumber = $result['house_number'];
            $this->streetName = $result['street_name'];
            $this->city = $result['city'];
            $this->zip4 = $result['zip4'];
        }
    }

    public function setInstallOrderId($oID) {
        $this->installOrderId = $oID;
    }

    public function fetchInstallOrder() {
        $this->installOrder = new orders('fetch', $this->installOrderId);
    }

    public function setInstallStart($ts) {
        $this->installStart = $ts;
    }

    public function setRemovalOrderId($oID) {
        $this->removalOrderId = $oID;
    }

    public function calcRemovalStart() {
        $delay = 86400 * (AUTOMATIC_REMOVAL_TIME - 1);
        $removal_time = ($this->installStart + $delay);
        do {
            $removal_time = add_business_days($removal_time, 1);
            $removal_day = date('N', $removal_time);
        } while ($removal_day != 1);

        return $removal_time;
    }

    public function isInstallWithoutRemoval() {
        if (isset($this->installOrderId) && !isset($this->removalOrderId)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAddressId() {
        return $this->addressId;
    }

    public function getPostCount() {
        global $database;
        if ($this->installOrderId) {
            $query = $database->query("SELECT number_of_posts FROM orders_description WHERE order_id = " . $this->installOrderId);
            if ($result = $database->fetch_array($query)) {
                return $result['number_of_posts'];
            }
        }
    }

    public function getCountyID() {
        global $database;
        $query = $database->query("SELECT county_id FROM addresses WHERE address_id = " . $this->addressId);
        if ($result = $database->fetch_array($query)) {
            return $result['county_id'];
        }
    }

    public function toJSON() {
        return "{\"address_id\": " . $this->addressId . ", \"install_order_id\": " . $this->installOrderId . "}";
    }

    public function createRemoval() {
        $this->fetchInstallOrder();
        $billingMethodID = $this->installOrder->fetch_data_item('billing_method_id');

        $data = array('address_id' => $this->addressId,
                      'order_type_id' => ORDER_TYPE_REMOVAL,
                      'schedualed_start' => $this->calcRemovalStart(),
                      'county' => $this->getCountyID(),
                      'promo_code' => '',
                      'number_of_posts' => $this->getPostCount(),
                      'billing_method_id' => $billingMethodID);
        $order = new orders('insert', '', $data, '', false, '1');
    }

    public function toString() {
        return "{$this->houseNumber} {$this->streetName}, {$this->city}";
    }
}

?>

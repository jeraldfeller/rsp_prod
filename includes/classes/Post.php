<?php
class Post {
    private $equipmentItemId;
    private $equipmentId;
    private $equipmentTypeId;
    private $name;
    private $installedTimestamp;
    private $agentId;
    private $installAddressId;
    private $installOrderId;
    private $removalOrderId;

    public function __construct($id) {
        global $database;

        $query = $database->query("SELECT ei.equipment_id, e.name FROM " . TABLE_EQUIPMENT_ITEMS . " ei JOIN " . TABLE_EQUIPMENT . " e ON (e.equipment_id = ei.equipment_id) WHERE ei.equipment_item_id = '" . $id . "'");
        if ($result = $database->fetch_array($query)) {
            $this->equipmentId = $result['equipment_id'];
            $this->name = $result['name'];
        }

      //  $query = $database->query("SELECT o.order_id, o.date_completed, o.user_id, o.address_id FROM " . TABLE_ORDERS . " o JOIN " . TABLE_ADDRESSES . " a ON (a.address_id = o.address_id) JOIN " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita ON (eita.address_id = a.address_id) WHERE eita.equipment_item_id = '" . $id . "' ORDER BY eita.equipment_item_to_address_id DESC LIMIT 1");
		 $query = $database->query("SELECT o.order_id, o.date_completed, o.user_id, o.address_id FROM " . TABLE_ORDERS . " o JOIN " . TABLE_ADDRESSES . " a ON (a.address_id = o.address_id) JOIN " . TABLE_EQUIPMENT_ITEMS_TO_ADDRESSES . " eita ON (eita.address_id = a.address_id and eita.equipment_status_id = 2) WHERE o.order_status_id = 3 and eita.equipment_item_id = '" . $id . "' ORDER BY eita.equipment_item_to_address_id DESC LIMIT 1");
        if ($result = $database->fetch_array($query)) {
            $this->installedTimestamp = $result['date_completed'];
            $this->agentId = $result['user_id'];
            $this->installOrderId = $result['order_id'];
            $this->installAddressId = $result['address_id'];
        }

        $query = $database->query("SELECT o.order_id FROM " . TABLE_ORDERS . " o WHERE address_id = '" . $this->installAddressId . "' AND order_type_id = 3 ORDER BY order_id DESC LIMIT 1");
        if ($result = $database->fetch_array($query)) {
            $this->removalOrderId = $result['order_id'];
        }

        $this->equipmentTypeId = 1;
        $this->equipmentItemId = $id;
    }

    public function getInstalledDays() {
        if (!$this->installedTimestamp) {
            return;
        }
        $installedSeconds = time() - $this->installedTimestamp;
        $installedDays = ceil($installedSeconds / (60*60*24));
        return $installedDays;
    }

    public function getInstalledTimestamp() {
        return $this->installedTimestamp;
    }

    public function getAgentId() {
        return $this->agentId;
    }

    public function getInstallOrderId () {
        return $this->installOrderId;
    }

    public function getRemovalOrderId () {
        return $this->removalOrderId;
    }

    public function getInstallAddressId() {
        return $this->installAddressId;
    }

    public function getEquipmentId() {
        return $this->equipmentId;
    }

    public function getEquipmentItemId() {
        return $this->equipmentItemId;
    }

    public function getEquipmentTypeId() {
        return $this->equipmentTypeId;
    }

    public function getName() {
        return $this->name;
    }

}
?>

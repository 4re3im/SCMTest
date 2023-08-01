<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
session_start();
Loader::model('title/model', 'cup_content');

class CupContentInspectionCopyOrder extends Object {

    protected $fields = array('id', 'school_order_number', 'email',
        'title', 'first_name', 'last_name', 'position',
        'school_campus', 'school_postcode', 'phone',
        'add_to_mailling_list', 'add_to_post_list',
        'shipping_address_line_1',
        'shipping_address_line_2', 'shipping_address_state',
        'shipping_address_city', 'shipping_address_postcode',
        'shipping_address_country', 'status', 'createdAt',
        'modifiedAt', 'syncData', 'syncAt');
    protected $record = array();
    protected $items = array();
    protected $errors = array();

    function __construct($id = false) {
        if ($id) {
            $db = Loader::db();
            $q = "select * from CupContentInspectionCopyOrder where id = ?";
            $result = $db->getRow($q, array($id));

            if ($result) {
                $this->record = $result;

                $q = "select * from CupContentInspectionCopyOrderItem where orderID = ?";
                $rows = $db->getAll($q, array($id));
                foreach ($rows as $row) {
                    $this->items[] = $row;
                }
            }
        }
    }

    public static function fetchByID($id) {
        $object = new CupContentInspectionCopyOrder($id);
        if ($object->id === FALSE) {
            return FALSE;
        } else {
            return $object;
        }
    }

    public function loadByID($id) {
        if ($id) {
            $db = Loader::db();
            $q = "select * from CupContentInspectionCopyOrder where id = ?";
            $result = $db->getRow($q, array($id));

            if ($result) {
                $this->record = $result;

                $q = "select * from CupContentInspectionCopyOrderItem where orderID = ?";
                $rows = $db->getAll($q, array($id));
                foreach ($rows as $row) {
                    $this->items[] = $row;
                }
            }
        }
    }

    public function __get($property) {
        if (isset($this->record[$property])) {
            return $this->record[$property];
        } else {
            return false;
        }
    }

    public function __set($property, $value) {
        if (in_array($property, $this->fields)) {
            $this->record[$property] = $value;
        }

        return $this;
    }

    public function getAssoc() {
        $tmp = $this->record;
        $tmp['items'] = $this->items;
        return $tmp;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function save() {
        if ($this->validation()) {
            if (isset($this->record['id']) && $this->record['id'] > 0) { //update
                $this->record['modifiedAt'] = date('Y-m-d H:i:s');

                $db = Loader::db();
                $q = "update CupContentInspectionCopyOrder 
						set school_order_number = ?, email = ?, title = ?, 
						first_name = ?, last_name = ?, position = ?, 
						school_campus = ?, school_postcode = ?, phone = ?, 
						add_to_mailling_list = ?, add_to_post_list = ?,
						shipping_address_line_1 = ?, shipping_address_line_2 = ?,
						shipping_address_state = ?, shipping_address_city = ?, 
						shipping_address_postcode = ?, shipping_address_country = ?,
						status = ?, modifiedAt = ?, syncData = ?, syncAt = ?
						WHERE id = ?";
                $v = array($this->record['school_order_number'], $this->record['email'], $this->record['title'],
                    $this->record['first_name'], $this->record['last_name'], $this->record['position'],
                    $this->record['school_campus'], $this->record['school_postcode'], $this->record['phone'],
                    $this->record['add_to_mailling_list'], $this->record['add_to_post_list'],
                    $this->record['shipping_address_line_1'], $this->record['shipping_address_line_2'],
                    $this->record['shipping_address_state'], $this->record['shipping_address_city'],
                    $this->record['shipping_address_postcode'], $this->record['shipping_address_country'],
                    $this->record['status'], $this->record['modifiedAt'], $this->record['syncData'], $this->record['syncAt'],
                    $this->record['id']);
                $r = $db->prepare($q);
                $res = $db->Execute($r, $v);
                if ($res) {
                    $this->loadByID($this->id);
                    return true;
                } else {
                    return false;
                }
            } else {
                return $this->saveNew();
            }
        }
    }

    public function savetoSalesForce() {

        $product_isbn = self::getOrderItemList('', $_SESSION['inspection_copy']['order_list']);

        foreach ($product_isbn as $values) {
            $books[] = 'ISBN:' . $values['isbn'] . ' Title:' . $values['product_name'];
        }

        $booktitles = implode(', ', $books);

        // we need to htmlspecialchars to clean the special characters that are not supported in XML
        $booktitles = htmlspecialchars($booktitles);

        define("USERNAME", "itservices@cambridge.edu.au");
        define("PASSWORD", "ZxAsQw21Cv43Df");
        define("SECURITY_TOKEN", "IQ8Fcf3wiANKAj3DvX2oLK36");


        Loader::library('3rdparty/SFDC/soapclient/SforcePartnerClient');

        $mySforceConnection = new SforcePartnerClient();
        $mySforceConnection->createConnection(DIR_LIBRARIES_3RDPARTY . "/SFDC/PartnerWSDL.xml");
        $mySforceConnection->login(USERNAME, PASSWORD . SECURITY_TOKEN);

        $post = $_POST;


        $lead = new sObject();
        $lead->type = 'Lead';

        $recv_email = 1;
        $recv_post = 1;

        if (isset($post['no_email']) && $post['no_email'] == 1) {
            $recv_email = 0;
        }

        if (isset($post['no_post']) && $post['no_post'] == 1) {
            $recv_post = 0;
        }


        $lead->fields = array(
            'Salutation' => $post['title'],
            'FirstName' => $post['first_name'],
            'LastName' => $post['last_name'],
            'Email' => $post['email'],
            'Phone' => $post['phone'],
            'HasOptedOutOfEmail' => $recv_email,
            'HardCopyOptOut__c' => 1,
            'Street' => $post['shipping_address_line_1'],
            'City' => $post['shipping_address_city'],
            'State' => $post['shipping_address_state'],
            'PostalCode' => $post['shipping_address_postcode'],
            'Country' => 'Australia',
            'Description' => $booktitles,
            //'Description'			=>  implode(',', $_SESSION['inspection_copy']['order_list_product_name']) .' ISBN : ' . implode(',', $_SESSION['inspection_copy']['order_list_isbn']),
            'Company' => $post['school_campus']
        );

        /* Submitting the Lead to Salesforce */

        $result = $mySforceConnection->create(array($lead), 'Lead');

        unset($_SESSION['order_list_product_name']);
        unset($_SESSION['inspection_copy']);
    }

    public function saveNew() {
        $this->record['createdAt'] = date('Y-m-d H:i:s');
        $this->record['modifiedAt'] = $this->record['createdAt'];

        /*
          school_order_number = ?, email = ?, title = ?,
          first_name = ?, last_name = ?, position = ?,
          school_campus = ?, school_postcode = ?, phone = ?,
          add_to_mailling_list = ?,
          shipping_address_line_1 = ?, shipping_address_line_2 = ?,
          shipping_address_state = ?, shipping_address_city = ?,
          shipping_address_postcode = ?, shipping_address_country = ?,
          status = ?, modifiedAt = ?, syncData = ?, syncAt = ?
         */

        $db = Loader::db();
        $q = "INSERT INTO CupContentInspectionCopyOrder 
					(school_order_number, email, title, 
					first_name, last_name, position,
					school_campus, school_postcode, phone, 
					add_to_mailling_list, add_to_post_list,
					shipping_address_line_1, shipping_address_line_2, 
					shipping_address_state, shipping_address_city,
					shipping_address_postcode, shipping_address_country,
					status, createdAt, modifiedAt, 
					syncData, syncAt) 
				VALUES (?, ?, ?, 
						?, ?, ?,
						?, ?, ?,
						?, ?,
						?, ?, 
						?, ?,
						?, ?, 
						?, ?, ?, 
						?, ?)";
        $v = array($this->record['school_order_number'], $this->record['email'], $this->record['title'],
            $this->record['first_name'], $this->record['last_name'], $this->record['position'],
            $this->record['school_campus'], $this->record['school_postcode'], $this->record['phone'],
            $this->record['add_to_mailling_list'], $this->record['add_to_post_list'],
            $this->record['shipping_address_line_1'], $this->record['shipping_address_line_2'],
            $this->record['shipping_address_state'], $this->record['shipping_address_city'],
            $this->record['shipping_address_postcode'], $this->record['shipping_address_country'],
            $this->record['status'], $this->record['createdAt'], $this->record['modifiedAt'],
            $this->record['syncData'], $this->record['syncAt']
        );
        $r = $db->prepare($q);
        $res = $db->Execute($r, $v);

        if ($res) {
            $this->loadByID($db->Insert_ID());
            return true;
        } else {
            return false;
        }
    }

    public function saveOrderItems($title_ids = array()) {
        $item_to_be_added = array();
        $item_to_be_removed = array();
        $existing_item_title_ids = array();
        foreach ($this->items as $each_item) {
            $existing_item_title_ids[] = $each_item['titleID'];
            if (!in_array($each_item['titleID'], $title_ids)) {
                $item_to_be_remove[] = $each_item['titleID'];
            }
        }

        foreach ($title_ids as $title_id) {
            if (!in_array($title_id, $existing_item_title_ids)) {
                $item_to_be_added[] = $title_id;
            }
        }

        if (count($item_to_be_removed) > 0) {
            $db = Loader::db();
            $q = "DELETE FROM CupContentInspectionCopyOrderItem WHERE orderID = ?";
            $q .= " AND titleID in (" . implode(', ', $item_to_be_removed) . ")";
            $result = $db->Execute($q, array($this->record['id']));
            if (!$result) {
                $this->errors[] = "Error when deleting OrderItems";
            }
        }

        if (!isset($_SESSION['order_list_product_name'])) {
            $_SESSION['order_list_product_name'] = array();
        }

        $order_list_product_name = array();

        if (count($item_to_be_added) > 0) {
            foreach ($item_to_be_added as $title_id) {
                $titleObj = CupContentTitle::fetchByID($title_id);

                $order_list_product_name[] = "Inspection copy: " . $titleObj->name;

                if ($titleObj) {
                    $tmp_title_id = $titleObj->id;
                    $tmp_isbn = $titleObj->isbn13;
                    $tmp_product_name = $titleObj->generateProductName();

                    $db = Loader::db();
                    $q = "INSERT INTO CupContentInspectionCopyOrderItem
						(orderID, titleID, ISBN, product_name)
						VALUES (?, ?, ?, ?)";
                    $result = $db->Execute($q, array($this->record['id'], $tmp_title_id,
                        $tmp_isbn, $tmp_product_name));
                    if (!$result) {
                        $this->errors[] = "Error when adding OrderItems (TitleID: {$tmp_title_id})";
                    }
                }
            }
        }


        $_SESSION['order_list_product_name'] = $order_list_product_name;

        if (count($this->errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    static function getOrderItemList($oid, $title_id = NULL) {
        $db = Loader::db();


        if (isset($oid) && !empty($oid)) {
            $q = "SELECT * FROM CupContentInspectionCopyOrderItem WHERE orderID = ?";
            $result = $db->getAll($q, array($oid));
        } else {
            $q = "SELECT isbn, product_name  FROM CupContentInspectionCopyOrderItem WHERE titleID IN (" . implode(', ', $title_id) . ") GROUP BY titleID";

            //echo $q;

            $result = $db->getAll($q);
        }


        return $result;
    }

    /* 	
      static function getOrderItemList($oid){
      $db = Loader::db();
      $q = "SELECT * FROM CupContentInspectionCopyOrderItem WHERE orderID = ?";
      $result = $db->getAll($q, array($oid));
      return $result;
      }
     */

    public function delete() {
        if ($this->record['id'] > 0) {
            $db = Loader::db();
            $q = "DELETE FROM CupContentInspectionCopyOrderItem WHERE orderID = ?";
            $result = $db->Execute($q, array($this->record['id']));
            if ($result) {
                $q = "DELETE FROM CupContentInspectionCopyOrder WHERE id = ?";

                $result = $db->Execute($q, array($this->record['id']));
                if ($result) {
                    return true;
                } else {
                    $this->errors[] = "Error occurs when deleting this Order";
                    return false;
                }
            } else {
                $this->errors[] = "Error occurs when deleting event Order Items";
                return false;
            }
        } else {
            $this->errors[] = "id is missing";
            return false;
        }
    }

    public function validation() {
        $this->errors = array();

        if (strlen($this->record['first_name']) < 1) {
            $this->errors[] = "First Name is required";
        }
        if (strlen($this->record['last_name']) < 1) {
            $this->errors[] = "Last Name is required";
        }
        if (strlen($this->record['position']) < 1) {
            $this->errors[] = "Position is required";
        }

        if (strlen($this->record['school_campus']) < 1) {
            $this->errors[] = "School / Campus is required";
        }

        if (strlen($this->record['school_postcode']) < 1) {
            $this->errors[] = "Campus Postcode is required";
        }

        if (strlen($this->record['phone']) < 1) {
            $this->errors[] = "Phone Number is required";
        }

        if (strlen($this->record['email']) < 1) {
            $this->errors[] = "Email is required";
        } else if (!filter_var($this->record['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email is invalid";
        }

        if (strlen($this->record['shipping_address_line_1']) < 1) {
            $this->errors[] = "Shipping Address Line 1 is required";
        }

        if (strlen($this->record['shipping_address_city']) < 1) {
            $this->errors[] = "Shipping Address City is required";
        }

        if (strlen($this->record['shipping_address_postcode']) < 1) {
            $this->errors[] = "Shipping Address Postcode is required";
        }

        if (strlen($this->record['shipping_address_country']) < 1) {
            $this->errors[] = "Shipping Address Country is required";
        } else {
            if (strcmp($this->record['shipping_address_country'], 'Australia') == 0 && strlen($this->record['shipping_address_state']) < 1) {
                $this->errors[] = "Shipping Address State is required";
            }
        }

        if (count($this->errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

}

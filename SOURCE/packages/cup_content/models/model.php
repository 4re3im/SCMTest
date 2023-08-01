<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('cart','core_commerce');
Loader::model('order/product','core_commerce');
Loader::model('series/model','cup_content');
Loader::model('title/model', 'cup_content');


class CupContentOrder extends Object {

	protected $id = FALSE;
	protected $orderID = FALSE;
	protected $invoiceID = FALSE;
	protected $orderObject = FALSE;
	protected $response = "";
	protected $response_nz = "";
	protected $response_digital = "";
	protected $status = FALSE;
	protected $vistaOrderID = "";
	protected $vistaOrderID_nz = "";
	protected $vistaOrderID_digital = "";
	protected $modifiedAt = FALSE;
	protected $createdAt = FALSE;
	
	protected $email = "";
	
	function __construct($id = false) {
		if($id){
			$db = Loader::db();
			$q = "select * from CupContentOrder where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				
				$this->id 			= $result['id'];
				$this->orderID		= $result['orderID'];
				$this->invoiceID	= $result['invoiceID'];
				$this->orderObject	= $result['orderObject'];
				$this->response		= $result['response'];
				$this->response_nz	= $result['response_nz'];
				$this->response_digital		= $result['response_digital'];
				$this->status		= $result['status'];
				$this->vistaOrderID = $result['vistaOrderID'];
				$this->vistaOrderID_nz = $result['vistaOrderID_nz'];
				$this->vistaOrderID_digital = $result['vistaOrderID_digital'];
				$this->modifiedAt	= $result['modifiedAt'];
				$this->createdAt	= $result['createdAt'];
				
				if(strlen($this->orderObject) > 0){
					$this->orderObject = unserialize($this->orderObject);
				}else{
					$this->order = false;
				}
			}
		}
	}
	
	public function fetchByID($id = false) {
		$object = new CupContentOrder($id);
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public static function fetchByOrderID($order_id){
		$db = Loader::db();
		$q = "select * from CupContentOrder where orderID = ?";	
		$result = $db->getRow($q, array($order_id));
		if($result){
			return new CupContentOrder($result['id']);
		}else{
			return FALSE;
		}
	}
	
	public static function fetchByInvoiceID($invoice_id){
		$db = Loader::db();
		$q = "select * from CupContentOrder where invoice = ?";	
		$result = $db->getRow($q, array($invoice_id));
		if($result){
			return new CupContentSeries($result['id']);
		}else{
			return FALSE;
		}
	}
	
	public function loadByID($requestID){
		$this->id 			= FALSE;
		$this->orderID		= FALSE;
		$this->invoiceID	= FALSE;
		$this->orderObject	= FALSE;
		$this->response		= "";
		$this->response_nz	= "";
		$this->response_digital = "";
		$this->status		= FALSE;
		$this->vistaOrderID = "";
		$this->vistaOrderID_nz = "";
		$this->vistaOrderID_digital = "";
		$this->modifiedAt	= FALSE;
		$this->createdAt	= FALSE;
	
		$db = Loader::db();
		$q = "select * from CupContentOrder where id = ?";	
		$result = $db->getRow($q, array($requestID));
		
		if($result){
		
			$this->id 			= $result['id'];
			$this->orderID		= $result['orderID'];
			$this->invoiceID	= $result['invoiceID'];
			$this->orderObject	= $result['orderObject'];
			$this->response		= $result['response'];
			$this->response_nz	= $result['response_nz'];
			$this->response_digital = $result['response_digital'];
			$this->status		= $result['status'];
			$this->vistaOrderID = $result['vistaOrderID'];
			$this->vistaOrderID_nz = $result['vistaOrderID_nz'];
			$this->vistaOrderID_digital = $result['vistaOrderID_digital'];
			$this->modifiedAt	= $result['modifiedAt'];
			$this->createdAt	= $result['createdAt'];
			
			if(strlen($this->orderObject) > 0){
				$this->orderObject = unserialize($this->orderObject);
			}else{
				$this->orderObject = false;
			}
			
			return true;
		}else{
			return false;
		}
	}
	
	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}
	
	public function getAssoc(){
		$temp = array(
					'id' => $this->id,
					'orderID' => $this->orderID,
					'invoiceID' => $this->invoiceID,
					'orderObject' => $this->orderObject,
					'response' => $this->response,
					'response_nz' => $this->response_nz,
					'response_digital' => $this->response_digital,
					'status' => $this->status,
					'vistaOrderID' => $this->vistaOrderID,
					'vistaOrderID_nz' => $this->vistaOrderID_nz,
					'vistaOrderID_digital' => $this->vistaOrderID_digital,
					'modifiedAt' => $this->modifiedAt,
					'createdAt' => $this->createdAt
	
				);
				
		if($temp['id'] === FALSE){
			$temp['id'] = '';
		}
		
		return $temp;
	}
	
	public function setOrder($order){
	
		$this->orderID = $order->getOrderID();
		$this->invoiceID = $order->getInvoiceNumber();
		$this->orderObject = $order;
	}
	
	public function save(){
		if($this->validataion()){
				
			if($this->id > 0){	//update
				
				$this->modifiedAt = date('Y-m-d H:i:s');
				
				$db = Loader::db();			
					
				$q = 	"update CupContentOrder set orderID = ?,
							invoiceID = ?, orderObject = ?, response = ?, 
							response_nz = ?, response_digital = ?,
							vistaOrderID = ?, vistaOrderID_nz = ?, vistaOrderID_digital = ?, 
							status = ?, modifiedAt = ? 
						WHERE id = ?";
				$v = array(	$this->orderID, 
							$this->invoiceID, serialize($this->orderObject), $this->response, 
							$this->response_nz, $this->response_digital,
							$this->vistaOrderID, $this->vistaOrderID_nz, $this->vistaOrderID_digital,
							$this->status, $this->modifiedAt,
							$this->id);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {				
					$this->loadByID($this->id);
					return true;
				}else{
					return false;
				}
			}else{	//insert
				return $this->saveNew();
			}
		}else{
			return false;
		}
	}
	
	public function saveNew(){
		$this->createdAt = date('Y-m-d H:i:s');
		$this->modifiedAt = $this->createdAt;
		
		$this->status = "pending";
				
		$db = Loader::db();
		$q = "INSERT INTO CupContentOrder (orderID, invoiceID, orderObject, response,  
									response_nz, response_digital,
									vistaOrderID, vistaOrderID_nz, vistaOrderID_digital,
									status, modifiedAt, createdAt)
					VALUES (?, ?, ?, ?, 
								?, ?, 
								?, ?, ?,
								?, ?, ?)";
					
		$v = array(	$this->orderID, $this->invoiceID, serialize($this->orderObject), $this->response, 
							$this->response_nz, $this->response_digital,
							$this->vistaOrderID, $this->vistaOrderID_nz, $this->vistaOrderID_digital, 
							$this->status, $this->modifiedAt, $this->createdAt);
							
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			$new_id = $db->Insert_ID();
			
			$this->loadByID($new_id);
			return true;

		}else{
			return false;
		}
	}
	
	public function delete(){
		if($this->id > 0){
			
			$db = Loader::db();
			
			$q = "DELETE FROM CupContentOrder WHERE id = ?";
				
			$result = $db->Execute($q, array($this->id));
			if($result){
				return true;
			}else{
				$this->errors[] = "Error occurs when deleting this Order Record";
				return false;
			}
		}else{
			$this->errors[] = "id is missing";
			return false;
		}
	}
	
	public function validataion(){
		
		return true;
	}

    public function generateOrderInfoJson(){
        $info = self::extractTransactionInfo($this);
        return json_encode($info);
    }
	
    public function generateGoogleTrackingCode(){
        $info = self::extractTransactionInfo($this);
        $json = json_encode($info);
        $html = <<<EOF
<script type="text/javascript">
    var _gaq = [];
    var transInfo = {$json};

try{
    _gaq.push(['_addTrans',transInfo['order_id'],'CUP AU/NZ',transInfo['total'],transInfo['tax'],transInfo['shipping'],transInfo['city'],transInfo['state'],transInfo['country']]);
    _gaq.push(['_set', 'currencyCode', transInfo['currency']]);

    for(var i = 0; i < transInfo['items'].length; i++){
        var item = transInfo['items'][i];
        _gaq.push(['_addItem',
            transInfo['order_id'],  // transaction ID - required
            item['isbn13'],            // SKU/code - required
            item['name'],           // product name
            item['series'],         // category or variation
            item['published_price'],          // unit price - required
            item['qty']             // quantity - required
          ]);

    }

    _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
}catch(e){
    //no handle
}
</script>
EOF;

        return $html;
    }

    public static function extractTransactionInfo($cup_order){
        $orderObject = $cup_order->orderObject;
        $totalCharged = $orderObject->getOrderTotal();

        $info = array();
        $items = array();
        $trailers = array();

        $total_value = 0.0; //no shipping, sum of items exclude GST price.
        $items_tax = 0.0;

        $cupResponses = array('AU' => $cup_order->response,
            'NZ' => $cup_order->response_nz,
            'D' => $cup_order->response_digital
        );

        foreach($cupResponses as $cat => $response){
            $xml = trim($response);
            if(strlen($xml) > 0){
                $xmlObject = new SimpleXMLElement($xml);
                $header = $xmlObject->Orders[0]->Basket[0]->Order[0]->Header[0];

                $info['currency'] = (string)$header->{ISOCurrencySymbol};

                $info['BillTo'] = array(
                    'country' => (string)$header->{BillTo}->{Location}->{Country},
                    'state' => (string)$header->{BillTo}->{Location}->{State},
                    'city' => (string)$header->{BillTo}->{Location}->{Town},
                    'postcode' => (string)$header->{BillTo}->{Location}->{PostalCode}
                );

                $info['DespValue'] = (string)$header->{DespValue};


                foreach($xmlObject->Orders[0]->Basket[0]->Order[0]->DetailLines[0]->Line as $line){
                    $item = array(
                        'pin' => (string)$line->{Pin} , //ISBN 10
                        'qty' => (string)$line->{DemandQty} ,
                        'name' => (string)$line->{Title} ,
                        'published_price' => (string)$line->{Calc}[0]->{PublishedPrice} ,
                        'published_value' => (string)$line->{Calc}[0]->{PublishedValue} ,
                        'net_price' => (string)$line->{Calc}[0]->{NetPrice} ,   // always same value as below !?
                        'net_value' => (string)$line->{Calc}[0]->{NetValue} ,   // always same value as above !?
                        'unit_tax' => floatval((string)$line->{Calc}[0]->{PublishedPrice}) - floatval((string)$line->{Calc}[0]->{PublishedValue}),
                        'series' => ''
                    );

                    $title = CupContentTitle::fetchByISBN10($item['pin']);
                    $item['isbn13'] = $title->isbn13;
                    $series = $title->getSeriesObject();
                    if($series){
                        $item['series'] = $series->name;
                    }

                    $total_value += $item['published_value'] * $item['qty'];
                    $items_tax += $item['unit_tax'] * $item['qty'];

                    $items[] =  $item;
                }

                $trailer = $xmlObject->Orders[0]->Basket[0]->Order[0]->Trailer[0];
                $trailers[] = array(
                    'order_net_value' => (string)$trailer->OrderNetValue[0],
                    'order_published_value' => (string)$trailer->OrderPublishedValue[0],
                    'order_tax_value' => (string)$trailer->OrderTaxValue[0],
                    'order_charge_total_value' => (string)$trailer->OrderChargeTotalValue[0],
                    'order_despatch_value' => (string)$trailer->OrderDespatchValue[0],
                    'order_total_value' => (string)$trailer->OrderTotalValue[0],
                    'unique_order_no' => (string)$trailer->UniqOrderNo[0]
                );
            }



        }

        $cart_summary = array(
            'order_net_value' => 0,
            'order_published_value' => 0,
            'order_tax_value' => 0,
            'order_charge_total_value' => 0,
            'order_despatch_value' => 0,
            'order_total_value' => 0,
            'unique_order_no' => 0
        );

        foreach($trailers as $key => $each){
            foreach($cart_summary as $key => $value){
                if(in_array($key, array('unique_order_no'))){
                    $cart_summary[$key] = $each[$key];
                }else{
                    $cart_summary[$key] = $value + $each[$key];
                }
            }
        }

        return array_merge(array(
            'order_id' => $orderObject->orderID,
            'currency' =>  $info['currency'],
            'country'=>  $info['BillTo']['country'],
            'state'=>  $info['BillTo']['state'],
            'city' =>  $info['BillTo']['city'],
            'postcode' =>  $info['BillTo']['postcode'],
            'items' => $items,
            'total' => $total_value,
            'tax' => $items_tax,
            'shipping' => $totalCharged - $total_value -  $items_tax
        ), $cart_summary);

    }
}
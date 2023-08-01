<?php

class VistaApi{
	//var $api_url = "https://ws.cambridge.edu.au/i-connect/AUStst/webservices/WSOrderInput/OrderAccept.asmx/PutXml";
	var $api_url = "";
	var $xml_doc = false;
	var $order = false;
	
	var $docType = 'INWN';
	var $option = 'P';
	var $currency = 'AUD';
	var $customerId = false;
	var $despatchCode = false; 	//10 20 30 04
	var $despatchValue = false;		//exclude GST
	var $invoiceID = false;
	var $items = false;
	
	var $billTo = array();
	var $shipTo = array();
	
	var $response = false;

	var $data = "";
	
	function __construct($order = false) {
		$this->order = $order;
	}
	
	function constructXML(){
	
		$doc = new DOMDocument();
		
		$xml_OrderBasket = $doc->appendChild( $doc->createElement('OrderBasket') );
		
		
		$xml_OrderBasket_Parameters = $xml_OrderBasket->appendChild( $doc->createElement('Parameters') );
		$xml_OrderBasket_Parameters = $xml_OrderBasket_Parameters->setAttribute('Option', $this->option);
		
		$xml_OrderBasket_Orders = $xml_OrderBasket->appendChild( $doc->createElement('Orders') );
			$xml_OrderBasket_Orders_Basket = $xml_OrderBasket_Orders->appendChild( $doc->createElement('Basket') );
				$xml_OrderBasket_Orders_Basket_Order = $xml_OrderBasket_Orders_Basket->appendChild( $doc->createElement('Order') );
				
				
					$xml_OrderBasket_Orders_Basket_Order_Header = $xml_OrderBasket_Orders_Basket_Order->appendChild( $doc->createElement('Header') );
					
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('RecordStatus') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('UniqOrderNo') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('CreatedDate') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('AmendedDate') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('SessionId', 'VISTA OZ') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('WebSiteId', 'AUS') 			
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('DevToken', 'TRADE') 
							);
		
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('ApprovalRequired', 'N') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('ISOCurrencySymbol', $this->currency)
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('OrderType') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('BillingType', 'P') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('BillingSubType', 'C') 
							);
							
						$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo = 
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('ShipTo') 
							);
							
							$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo->appendChild( 
										$doc->createElement('CustomerId', $this->customerId) 
							);
							
							
							if($this->option == 'P' && $this->shipTo !== false){
							
								//ShipTo > Personal
								$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Personal = 
								$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo->appendChild( 
											$doc->createElement('Personal') 
								);
								
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Personal->appendChild( 
											$doc->createElement('Email', 
													(isset($this->shipTo['email']) ? $this->shipTo['email'] : "")
												) 
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Personal->appendChild( 
											$doc->createElement('Telephone', 
													(isset($this->shipTo['telephone']) ? $this->shipTo['telephone'] : "")
												) 
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Personal->appendChild( 
											$doc->createElement('Fax', 
													(isset($this->shipTo['fax']) ? $this->shipTo['Fax'] : "")
												)
										);
								
								$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Company = 
								$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo->appendChild( 
											$doc->createElement('Company') 
								);
								
									if(isset($this->shipTo['business_name']) && strlen($this->shipTo['business_name']) > 0){
										$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Company->appendChild( 
												$doc->createElement('BusinessName', $this->shipTo['business_name'])
											);
									}
							
							
								$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location = 
								$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo->appendChild( 
											$doc->createElement('Location') 
								);
								
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location->appendChild( 
											$doc->createElement('AreaCode', 
													(isset($this->shipTo['location_area_code']) ? $this->shipTo['location_area_code'] : "")
												)
										);
									
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location->appendChild( 
											$doc->createElement('Town', 
													(isset($this->shipTo['location_town']) ? $this->shipTo['location_town'] : "")
												)
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location->appendChild( 
											$doc->createElement('State', 
													(isset($this->shipTo['location_state']) ? $this->shipTo['location_state'] : "")
												)
										);
									
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location->appendChild( 
											$doc->createElement('PostalCode', 
													(isset($this->shipTo['location_postcode']) ? $this->shipTo['location_postcode'] : "")
												)
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location->appendChild( 
											$doc->createElement('Country', 
													(isset($this->shipTo['location_country']) ? $this->shipTo['location_country'] : "")
												)
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location_AddressBlock =
									$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location->appendChild( 
											$doc->createElement('AddressBlock')
										);
										
										$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line1', 
													(isset($this->shipTo['location_address_line1']) ? $this->shipTo['location_address_line1'] : "")
												)
											);
											
										$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line2',
													(isset($this->shipTo['location_address_line2']) ? $this->shipTo['location_address_line2'] : "")
												)
											);
											
										$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line3', 
													(isset($this->shipTo['location_address_line3']) ? $this->shipTo['location_address_line3'] : "")
												)
											);
											
										$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line4', 
													(isset($this->shipTo['location_address_line4']) ? $this->shipTo['location_address_line4'] : "")
												)
											);
											
										$xml_OrderBasket_Orders_Basket_Order_Header_ShipTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line5', 
													(isset($this->shipTo['location_address_line5']) ? $this->shipTo['location_address_line5'] : "")
												)
											);
											
							
							}	//option P
								
							
						$xml_OrderBasket_Orders_Basket_Order_Header_BillTo =
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('BillTo') 
							);
							
							$xml_OrderBasket_Orders_Basket_Order_Header_BillTo->appendChild( 
										$doc->createElement('CustomerId', $this->customerId) 
							);
							
							
							$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Company = 
							$xml_OrderBasket_Orders_Basket_Order_Header_BillTo->appendChild( 
										$doc->createElement('Company') 
							);
							
								if($this->option == 'P'){
									if(isset($this->billTo['business_name']) && strlen($this->billTo['business_name']) > 0){
										$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Company->appendChild( 
													$doc->createElement('BusinessName', $this->billTo['business_name']) 
										);
									}
								}
								
							
							$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location = 
							$xml_OrderBasket_Orders_Basket_Order_Header_BillTo->appendChild( 
										$doc->createElement('Location') 
							);
							
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location->appendChild( 
											$doc->createElement('AreaCode', 
													(isset($this->billTo['location_area_code']) ? $this->billTo['location_area_code'] : "")
												) 
								);
								
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location->appendChild( 
											$doc->createElement('Town', 
													(isset($this->billTo['location_town']) ? $this->billTo['location_town'] : "")
												) 
								);
								
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location->appendChild( 
											$doc->createElement('State', 
													(isset($this->billTo['location_state']) ? $this->billTo['location_state'] : "")
												) 
								);
								
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location->appendChild( 
											$doc->createElement('PostalCode', 
													(isset($this->billTo['location_postcode']) ? $this->billTo['location_postcode'] : "")
												) 
								);
								
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location->appendChild( 
											$doc->createElement('Country', 
													(isset($this->billTo['location_country']) ? $this->billTo['location_country'] : "")
												) 
								);
								
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location_AddressBlock = 
								$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location->appendChild( 
											$doc->createElement('AddressBlock') 
								);
								
									$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line1', 
														(isset($this->billTo['location_address_line1']) ? $this->billTo['location_address_line1'] : "")
													) 
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line2', 
														(isset($this->billTo['location_address_line2']) ? $this->billTo['location_address_line2'] : "")
													) 
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line3', 
														(isset($this->billTo['location_address_line3']) ? $this->billTo['location_address_line3'] : "")
													) 
										);
										
									$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line4', 
														(isset($this->billTo['location_address_line4']) ? $this->billTo['location_address_line4'] : "")
													) 
										);
							
									$xml_OrderBasket_Orders_Basket_Order_Header_BillTo_Location_AddressBlock->appendChild( 
												$doc->createElement('Line5', 
														(isset($this->billTo['location_address_line5']) ? $this->billTo['location_address_line5'] : "")
													) 
										);
										
						/*
						$xml_OrderBasket_Orders_Basket_Order_Header_Texts = 
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
												$doc->createElement('Texts') 
										);
							
							$xml_OrderBasket_Orders_Basket_Order_Header_Texts->appendChild( 
												$doc->createElement('TextAttention', '') 
										);
						*/
							
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('BusinessTransactionType', $this->docType) 
							);
							
							
						/*
						$xml_OrderBasket_Orders_Basket_Order_Header_CreditCard =
						$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('CreditCard') 
							);
						*/
							
						
							
						if($this->option == 'P'){
							$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
										$doc->createElement('CustomerReference', $this->invoiceID) 	//$this->invoiceID
							);
							
							$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
											$doc->createElement('DespatchCode', $this->despatchCode) 
								);
								
							if($this->despatchValue !== false){
								$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
											$doc->createElement('OverrideDespatchValueInd', 'Y') 
								);
								$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
											$doc->createElement('DespValue', $this->despatchValue) 
								);
							}else{
								$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
											$doc->createElement('OverrideDespatchValueInd', 'Y') 
								);
								
								$xml_OrderBasket_Orders_Basket_Order_Header->appendChild( 
											$doc->createElement('DespValue', '0') 
								);
							}
						}
							
		
		
					$xml_OrderBasket_Orders_Basket_Order_DetailLines = 
					$xml_OrderBasket_Orders_Basket_Order->appendChild( $doc->createElement('DetailLines') );
		
						foreach($this->items as $item){
							$xml_OrderBasket_Orders_Basket_Order_DetailLines_Line =
							$xml_OrderBasket_Orders_Basket_Order_DetailLines->appendChild( $doc->createElement('Line') );
			
								$xml_OrderBasket_Orders_Basket_Order_DetailLines_Line->appendChild( 
										$doc->createElement('Pin', $item['Pin']) 
									);
									
								$xml_OrderBasket_Orders_Basket_Order_DetailLines_Line->appendChild( 
										$doc->createElement('DemandQty', $item['DemandQty']) 
									);
									
								if(isset($item['MailShot']) && $item['MailShot'] !== FALSE){
								$xml_OrderBasket_Orders_Basket_Order_DetailLines_Line->appendChild( 
										$doc->createElement('MailShot', $item['MailShot']) 
									);
								}
						}
		
					$xml_OrderBasket_Orders_Basket_Order_Trailer = 
					$xml_OrderBasket_Orders_Basket_Order->appendChild( $doc->createElement('Trailer') );
	
		
	
		$doc->formatOutput = true;
		$reqdata = $doc->saveXML();
		
		$this->xml_doc = $doc;
		return $reqdata;
	}
	
	public function request(){
		if($this->xml_doc !== false){
			$this->constructXML();
		}
		
		
		//echo "A\n";
		//var_dump($this->api_url);
		//echo "B\n";
		//print_r($this->xml_doc->saveXML());
		//echo "C\n";
	
		
		$request = curl_init($this->api_url);
		curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;"));
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_PORT, 443);
		//curl_setopt($request, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, 'input='.$this->xml_doc->saveXML());
		curl_setopt($request, CURLOPT_POST, 1);  
		curl_setopt($request, CURLOPT_URL, $this->api_url);
		$data = curl_exec($request);
		$status = curl_getinfo($request, CURLINFO_HTTP_CODE); 
		curl_close ($request);

		
		if($status == '200') {	
			$this->response = $data;
			//print_r($data);
			return true;
		}else{	
			//echo "API Connection error";
			//print_r($data);
			return false;
		}
	}
	
	public function response2array(){
	
	}
	
	public function respons2simpleXML(){
		return new SimpleXMLElement($this->response);
	}
}
<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('cart', 'core_commerce');
Loader::model('order/model', 'core_commerce');
Loader::model('order/product', 'core_commerce');
Loader::model('product/model', 'core_commerce');

Loader::model('vista_order_record/model', 'cup_content');

Loader::model('title/model', 'cup_content');
loader::library('vista_api', 'cup_content');

class CupContentEcommerceHelper {

    protected $titleObjects = array();
    protected $response = false;

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

    public function checkStockLevel() {
        $start_time = time();

        unset($_SESSION['_cup_']['checked_items']);

        //echo "Default Check\n";
        $stock = $this->vistaCheckOrder();


        $cart = CoreCommerceCart::get();
        $orderID = $cart->getOrderID();
        $billTo = $this->generateBillToArray($cart);
        $shipTo = $this->generateShipToArray($cart);
        $country = $billTo['location_country'];
        $stock_items = array();

        $errors = array();




        $pkg = Package::getByHandle('cup_content');
        $vista_config = false;
        $store_value = $pkg->config('VISTA_CONFIG');
        if ($store_value) {
            $vista_config = unserialize($store_value);
        }

        if ($vista_config === false || $vista_config['enabled'] == 0 || strlen($vista_config['api_url']) < 1) {
            //skip VISTA checking
        } else if ($stock === false) {
            $pkg = Package::getByHandle('cup_content');
            $notify_email = $pkg->config('CONTACT_FORM_EMAIL_RECEIVER');
            $from_email = $pkg->config('FROM_EMAIL_ADDRESS');

            if ($notify_email) {
                $mh = Loader::helper('mail');
                $mh->to($notify_email);
                $mh->from($from_email);
                $mh->addParameter('billTo', $billTo);
                $mh->addParameter('shipTo', $shipTo);
                $mh->addParameter('cart', $cart);
                $mh->load('notification_vista_connection_error', 'cup_content');
                @$mh->sendMail();
            }

            $errors[] = "There was a disruption while processing your order.<br/>
Customer service have been notified and will be in contact to complete payment. You do not need to place your order again.<br/>
If you have not been contacted within one business day please contact us on 03 8671 1400 or enquiries@cambridge.edu.au. ";
            $ch = Loader::helper('cup_content_html', 'cup_content');
            $url = $ch->url('/cart?e=' . urlencode(base64_encode(json_encode($errors))));
            header("Location: " . $url);
            exit;
        }

        //print_r($stock);
        //print_r($this->response);
        //exit();
        //echo "default check stock OK";
        //exit();
        if (strcmp($country, "Australia") == 0) {
            foreach ($cart->getProducts() as $product) {
                //$product->setQuantity(0);
                $product_name = $product->getProductName();
                $requested_qty = $product->getQuantity();
                $stock_qty = $product->getProductObject()->getProductQuantity();

                $productObj = $product->getProductObject();
                $titleObject = CupContentTitle::fetchByProductId($productObj->getProductID());
                $title_isbn10 = $titleObject->isbn10;

                if ($requested_qty > $stock_qty) {
                    if ($stock_qty > 0) {
                        $cart->removeProduct($product);
                        $errors[] = "Unfortunately there is no longer enough stock to process \"{$product_name}\", current stock is {$stock_qty}.  Please contact Customer Service for assistance: enquiries@cambridge.edu.au.";
                        //$errors[] = "Not enough stock to process \"{$product_name}\", current stock is 0. This Item has been removed";
                        $product->setQuantity($stock_qty);
                    } else {
                        $errors[] = "Unfortunately there is no longer enough stock to process \"{$product_name}\", current stock is {$stock_qty}.  Please contact Customer Service for assistance: enquiries@cambridge.edu.au.";
                        //$errors[] = "Not enough stock to process \"{$product_name}\", current stock is {$stock_qty}";
                        $product->setQuantity($stock_qty);
                    }
                } else {
                    $au_item = array(
                        'Pin' => $title_isbn10,
                        'DemandQty' => $requested_qty,
                        'MailShot' => false,
                        '_stock_region' => 'AU'
                    );
                    if ($titleObject->hasAccessCode || $titleObject->hasDownloadableFile) {
                        //$item['MailShot'] = '[D]';
                        $au_item['_is_digital'] = true;
                    }
                    $stock_items[] = $au_item;
                }
            }
        } else { //New Zealand handle differently
            //echo "\n\nNew Zealand Purchases\n\n";
            foreach ($cart->getProducts() as $product) {
                //$product->setQuantity(0);
                $product_name = $product->getProductName();
                $requested_qty = $product->getQuantity();
                $productObj = $product->getProductObject();
                $stock_qty = $productObj->getProductQuantity();
                $titleObject = CupContentTitle::fetchByProductId($productObj->getProductID());
                $title_isbn10 = $titleObject->isbn10;

                //echo "\t{$title_isbn10}\n";
                //print_r($stock[$title_isbn10]);
                //echo "\n\n";
                if ($stock[$title_isbn10]['DuedQty'] > 0) {
                    $nz_item = array(
                        'Pin' => $title_isbn10,
                        'DemandQty' => $stock[$title_isbn10]['DeliveredQty'],
                        'MailShot' => false,
                        '_stock_region' => 'NZ'
                    );
                    if ($titleObject->hasAccessCode || $titleObject->hasDownloadableFile) {
                        //$item['MailShot'] = '[D]';
                        $nz_item['_is_digital'] = true;
                    }


                    if ($stock[$title_isbn10]['DeliveredQty'] > 0) {
                        $stock_items[] = $nz_item;
                    }

                    $au_item = $nz_item;
                    $au_item['DemandQty'] = $stock[$title_isbn10]['DuedQty'];
                    $au_item['_stock_region'] = 'AU';

                    $tmp_stock = $this->vistaCheckOrder("Australia", array($au_item));
                    if ($tmp_stock[$title_isbn10]['DuedQty'] > 0) {
                        $tmp_stock_qty = $stock[$title_isbn10]['DeliveredQty'] + $tmp_stock[$title_isbn10]['DeliveredQty'];

                        $errors[] = "Unfortunately there is no longer enough stock to process \"{$product_name}\", current stock is {$tmp_stock_qty}.  Please contact Customer Service for assistance: enquiries@cambridge.edu.au.";
                        //$errors[] = "Not enough stock to process \"{$product_name}\", current stock is {$stock_qty}";
                        $product->setQuantity($tmp_stock_qty);
                    } else { //adequate au stock level
                        $stock_items[] = $au_item;
                    }
                } else { //adequate stock leve - All from NZ
                    $nz_item = array(
                        'Pin' => $title_isbn10,
                        'DemandQty' => $requested_qty,
                        'MailShot' => false,
                        '_stock_region' => 'NZ'
                    );
                    if ($titleObject->hasAccessCode || $titleObject->hasDownloadableFile) {
                        //$item['MailShot'] = '[D]';
                        $nz_item['_is_digital'] = true;
                    }

                    $stock_items[] = $nz_item;
                }
            }


            //print_r($stock_items);
            //print_r($errors);
            //exit();
        }

        //var_dump($cart->getProducts());
        //exit();

        $process_time = time() - $start_time;
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents('/tmp/tmpTimenote', "{$timestamp} Checking Process: {$process_time} seconds \n", FILE_APPEND);


        if (count($errors) > 0) {
            $ch = Loader::helper('cup_content_html', 'cup_content');
            $url = $ch->url('/cart?e=' . urlencode(base64_encode(json_encode($errors))));
            header("Location: " . $url);
            exit;
        } else {
            $_SESSION['_cup_']['checked_items'] = $stock_items;
            CupContentVistaOrderRecord::saveData($orderID, $stock_items);

            //print_r(CupContentVistaOrderRecord::loadData($orderID));
            //print_r($_SESSION['_cup_']['checked_items']);
            //exit();
        }

        return true;
    }

    public function vistaCheckOrder($default_country = false, $default_items = false) {

        $start_time = time();

        $pkg = Package::getByHandle('cup_content');
        $vista_config = false;
        $store_value = $pkg->config('VISTA_CONFIG');
        if ($store_value) {
            $vista_config = unserialize($store_value);
        }

        if ($vista_config === false || $vista_config['enabled'] == 0) {
            return false;
        }

        if (strlen($vista_config['api_url']) < 1) {
            return false;
        }

        //echo "CupContentEcommerceHelper::vistaCheckCart start\n";
        $cart = CoreCommerceCart::get();

        $billTo = $this->generateBillToArray($cart);
        $shipTo = $this->generateShipToArray($cart);


        $country = $billTo['location_country'];
        $currency = 'AUD';

        $despatchValue = false;
        $despatchValue = $this->generateDespatchValue($cart);

        //echo "despatsh value: {$despatchValue}";

        $items = $default_items;
        if (!$items) {
            $items = $this->generateItemsArray($cart);
        }

        $basketType = $this->checkBasketType($items);

        if ($default_country) {
            $country = $default_country;
        }

        $customer_id = '116213';
        $doctype = 'INWN';
        if (strcmp($country, 'New Zealand') == 0) {
            $currency = 'NZD';
            $customer_id = '116218';
            if ($basketType == "E") {
                $doctype = 'INWS';
            } else {
                $doctype = 'INWZ';
            }
        } else { //Australia
            $currency = 'AUD';
            $customer_id = '116213';
            if ($basketType == "E") {
                $doctype = 'INWS';
            } else {
                $doctype = 'INWN';
            }
        }


        $despatch_code = false;
        if (strcmp($doctype, 'INWS') != 0) {
            if (strcmp($shipTo['location_country'], 'New Zealand') == 0) {
                $despatch_code = '04';
            } else {
                $despatch_code = '30'; //for all Australia
            }
        } else {
            $despatch_code = '98';
        }

        $pobox_reg = array("/^\s*((g)?(p(ost)?.?\s*o(ff(ice)?)?.?\s+(b(in|ox))?)|b(in|ox))/i",
            "/^\s*((p(ost)?.?\s*(o(ff(ice)?)?)?.?\s+(b(in|ox))?)|B(in|ox))/i",
            // "^(p[\s|\.|,]*| ^post[\s|\.]*)(o[\s|\.|,]*| o(ff(ice)?)?[\s|\.]*)(box)"
            "/private\sbag/"
        );

        foreach ($pobox_reg as $each_reg) {
            if (preg_match($each_reg, strtolower($shipTo['location_address_line1']))) {
                $despatch_code = '20';
            }
        }

        if (strcmp($despatch_code, '20') != 0) {
            if (preg_match($each_reg, strtolower($shipTo['location_address_line2']))) {
                $despatch_code = '20';
            }
        }


        $api = new VistaApi();
        $api->api_url = $vista_config['api_url'];
        $api->option = "C";
        $api->docType = $doctype;
        $api->currency = $currency;
        $api->customerId = $customer_id;
        $api->despatchCode = $despatch_code;
        $api->despatchValue = $despatchValue;

        $api->invoiceID = false;

        $api->items = $items;
        $api->billTo = $billTo;
        $api->shipTo = $shipTo;

        $api->constructXML();



        if ($api->request()) {
            //echo "before update products\n";
            $this->updateProducts($api->response);
            //echo "After update products\n";
            $this->response = $api->response;

            $stockQty = array();
            $simple = new SimpleXMLElement($api->response);
            foreach ($simple->Orders->Basket->Order->DetailLines->Line as $line) {
                $item['isbn10'] = (string) $line->Pin;
                $item['DeliveredQty'] = intval((string) $line->Calc->DeliveredQty);
                $item['DuedQty'] = intval((string) $line->Calc->DuedQty);

                $stockQty[$item['isbn10']] = $item;
            }

            return $stockQty;
            //return true;
        }
        return false;
    }

    public function updateProducts($xml_raw) {
        $simple = new SimpleXMLElement($xml_raw);

        $currency = (string) $simple->Orders->Basket->Order->Header->ISOCurrencySymbol;
        $doctype = (string) $simple->Orders->Basket->Order->Header->BusinessTransactionType;

        foreach ($simple->Orders->Basket->Order->DetailLines->Line as $line) {
            $isbn10 = (string) $line->Pin;
            $inStock = (string) $line->InStock;
            $deliveredQty = (string) $line->Calc->DeliveredQty;
            $duedQty = (string) $line->Calc->DuedQty;
            $answerCode = (string) $line->AnswerCode;
            $publishedPrice = (string) $line->Calc->PublishedPrice;

            $deliveredQty = intval($deliveredQty);
            $duedQty = intval($duedQty);
            /*
              var_dump(array(
              $currency,$isbn10,$inStock,$answerCode,$publishedPrice
              ));
             */
            $titleObj = CupContentTitle::fetchByISBN10($isbn10);
            if ($titleObj) {
                $titleObj->availability = $answerCode;
                $titleObj->save();

                $stock_quantity = false;
                if ($duedQty > 0) {
                    $stock_quantity = 0;
                }
                $publishedPrice = false; //do not update price.

                /*
                  if(strcmp($currency, 'AUD') == 0){
                  $titleObj->updateProduct('AU', $publishedPrice, $stock_quantity);
                  }else{	//has to be NZ
                  $titleObj->updateProduct('NZ', $publishedPrice, $stock_quantity);
                  }
                 */
                if (strcmp($doctype, 'INWN') == 0) { //Australia
                    $titleObj->updateProduct('AU', $publishedPrice, $stock_quantity);
                } elseif (strcmp($doctype, 'INWZ') == 0) {  //New Zealand
                    $titleObj->updateProduct('NZ', $publishedPrice, $stock_quantity);
                }
            }
        }
    }

    public function vistaPostOrder($order = false) {
        //echo "[[vistaPostOrder]]";
        $this->response = array();


        $pkg = Package::getByHandle('cup_content');
        $vista_config = false;
        $store_value = $pkg->config('VISTA_CONFIG');
        if ($store_value) {
            $vista_config = unserialize($store_value);
        }

        if ($vista_config === false || $vista_config['enabled'] == 0) {
            return false;
        }

        if (strlen($vista_config['api_url']) < 1) {
            return false;
        }


        if ($order === false) {
            $cart = CoreCommerceCart::get();
        } else {
            $cart = $order;
        }

        $orderID = $cart->getOrderID();
        $billTo = $this->generateBillToArray($cart);
        $shipTo = $this->generateShipToArray($cart);

        //$items = $this->generateItemsArray($cart);
        //$items = $_SESSION['_cup_']['checked_items'];
        $items = CupContentVistaOrderRecord::loadData($orderID);
        $physical_items = $this->filterPhysicalItems($items);

        //print_r($physical_items);
        //exit();

        $au_items = $this->filterItems($physical_items, '_stock_region', 'AU');
        $nz_items = $this->filterItems($physical_items, '_stock_region', 'NZ');

        $digital_items = $this->filterDigitalItems($items);

        //$basketType = $this->checkBasketType($items);
        $country = $billTo['location_country'];


        $despatchValue = false;
        $despatchValue = $this->generateDespatchValue($cart);

        //echo "despatsh value: {$despatchValue}";

        $currency = 'AUD';
        $customer_id = '116213';
        //$physical_doctype = 'INWN';  //Australia
        if (strcmp($country, 'New Zealand') == 0) {
            //$physical_doctype = 'INWZ';
            $currency = 'NZD';
            $customer_id = '116218';
        }

        $doctype = false;
        /*
          $customer_id = '116213';
          $doctype = 'INWN';
          if(strcmp($country, 'New Zealand') == 0){
          $currency = 'NZD';
          $customer_id = '116218';
          if($basketType == "E"){
          $doctype = 'INWS';
          }else{
          $doctype = 'INWZ';
          }
          }else{	//Australia
          $currency = 'AUD';
          $customer_id = '116213';
          if($basketType == "E"){
          $doctype = 'INWS';
          }else{
          $doctype = 'INWN';
          }
          }
         */

        $despatch_code = false;
        $is_pobox_address = false;
        //$despatch_value = false;
        if (isset($shipTo['location_country'])) {
            if (strcmp($shipTo['location_country'], 'New Zealand') == 0) {
                $despatch_code = '07';
            } else {
                $despatch_code = '30'; //for all Australia
            }

            $pobox_reg = array("/^\s*((g)?(p(ost)?.?\s*o(ff(ice)?)?.?\s+(b(in|ox))?)|b(in|ox))/i",
                "/^\s*((p(ost)?.?\s*(o(ff(ice)?)?)?.?\s+(b(in|ox))?)|B(in|ox))/i",
                // "^(p[\s|\.|,]*| ^post[\s|\.]*)(o[\s|\.|,]*| o(ff(ice)?)?[\s|\.]*)(box)"
                "/private\sbag/"
            );

            foreach ($pobox_reg as $each_reg) {
                if (preg_match($each_reg, strtolower($shipTo['location_address_line1']))) {
                    $despatch_code = '20';
                    $is_pobox_address = true;
                }
            }

            if (!$is_pobox_address) {
                foreach ($pobox_reg as $each_reg) {
                    if (preg_match($each_reg, strtolower($shipTo['location_address_line2']))) {
                        $despatch_code = '20';
                        $is_pobox_address = true;
                    }
                }
            }
        }



        //echo "VISTA AU\n";
        if (is_array($au_items) && count($au_items) > 0) {
            if (strcmp($country, 'Australia') == 0) {
                if ($is_pobox_address) {
                    $despatch_code = '20';
                } else {
                    $despatch_code = '30';
                }
            } else {
                if ($is_pobox_address) {
                    $despatch_code = '06';
                } else {
                    $despatch_code = '05';
                }
            }


            $api = new VistaApi();
            $api->api_url = $vista_config['api_url'];
            $api->option = "P";
            $api->docType = 'INWN';
            $api->currency = $currency;
            $api->customerId = $customer_id;
            $api->despatchCode = $despatch_code;
            $api->despatchValue = $despatchValue;

            $api->invoiceID = $cart->getOrderID(); //$cart->getInvoiceNumber();

            $api->items = $au_items;
            $api->billTo = $billTo;
            $api->shipTo = $shipTo;
            //echo "Generate XML\n";
            $api->constructXML();
            //echo "\n\n";
            if ($api->request()) {
                //echo "[request OK]";
                $this->updateProducts($api->response);
                $this->response['physical_au'] = $api->response;
                //return true;
            } else {
                //$this->response['physical_au'] = false;
            }

            $despatchValue = 0; //Postage has been recorded, no more postage will be send.
        }


        //echo "VISTA NZ\n";
        if (is_array($nz_items) && count($nz_items) > 0) {
            //if(is_array($au_items) && count($au_items) > 0){
            if ($is_pobox_address) {
                $despatch_code = '06';
            } else {
                $despatch_code = '07';
            }
            //}else{
            //	if($is_pobox_address){
            //		$despatch_code = '20';
            //	}else{
            //		$despatch_code = '04';
            //	}
            //}


            $api = new VistaApi();
            $api->api_url = $vista_config['api_url'];
            $api->option = "P";
            $api->docType = 'INWZ';
            $api->currency = $currency;
            $api->customerId = $customer_id;
            $api->despatchCode = $despatch_code;
            $api->despatchValue = $despatchValue;

            $api->invoiceID = $cart->getOrderID(); //$cart->getInvoiceNumber();

            $api->items = $nz_items;
            $api->billTo = $billTo;
            $api->shipTo = $shipTo;
            $api->constructXML();

            file_put_contents('/tmp/vistaXMLLog.txt', $api->constructXML() . "", FILE_APPEND);

            if ($api->request()) {
                //echo "[request OK]";
                $this->updateProducts($api->response);
                $this->response['physical_nz'] = $api->response;
                //return true;
            } else {
                //$this->response['physical_nz'] = false;
            }
        }



        $process_time = time() - $start_time;
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents('/tmp/tmpTimenote', "{$timestamp} Posting Process: {$process_time} seconds \n", FILE_APPEND);

        //Digital products
        //echo "VISTA DIGITAL\n";
        if (is_array($digital_items) && count($digital_items) > 0) {
            $api = new VistaApi();
            $api->api_url = $vista_config['api_url'];
            $api->option = "P";
            $api->docType = 'INWS';
            $api->currency = $currency;
            $api->customerId = $customer_id;
            $api->despatchCode = "98";
            $api->despatchValue = "0";

            $api->invoiceID = $cart->getOrderID(); //$cart->getInvoiceNumber();

            $api->items = $digital_items;
            $api->billTo = $billTo;
            $api->shipTo = $billTo;

            $api->constructXML();

            file_put_contents('/tmp/vistaXMLLog.txt', $api->constructXML() . "", FILE_APPEND);

            if ($api->request()) {
                $this->updateProducts($api->response);
                $this->response['digital'] = $api->response;
                //return true;
            } else {
                //$this->response['digital'] = false;
            }
        }

        if (is_array($this->response) && count($this->response) > 0) {
            return true;
        }
        return false;
    }

    protected function generateBillToArray($cart_order) {
        $address_attr = $cart_order->getAttribute('billing_address');

        $email = $cart_order->getOrderEmail();
        $first_name = $cart_order->getAttribute('billing_first_name');
        $last_name = $cart_order->getAttribute('billing_last_name');
        $telphone = $cart_order->getAttribute('billing_phone');

        return $this->generateAddressArray($address_attr, $email, $first_name, $last_name, $telphone);
    }

    public function getVistaResponseOrderNumber() {
        if (!is_array($this->response) || count($this->response) < 1) {
            return false;
        }

        $result = array();
        foreach ($this->response as $idx => $xml_content) {
            try {
                $simple = new SimpleXMLElement($xml_content);
                $value = $simple->Orders->Basket->Order->Trailer->UniqOrderNo;
                $value = (string) $value;
                $result[$idx] = $value;
            } catch (Exception $e) {
                echo "$e\n";
                echo "Index: $idx \n";
                echo "$xml_content";
            }
        }

        return $result;
    }

    protected function generateShipToArray($cart_order) {
        $address_attr = $cart_order->getAttribute('shipping_address');

        if ($address_attr) {
            $email = $cart_order->getOrderEmail();
            $first_name = $cart_order->getAttribute('shipping_first_name');
            $last_name = $cart_order->getAttribute('shipping_last_name');
            $telphone = $cart_order->getAttribute('shipping_phone');

            return $this->generateAddressArray($address_attr, $email, $first_name, $last_name, $telphone);
        }
        return false;
    }

    protected function generateAddressArray($ecommerce_address_attribute, $email = "", $fist_name = "", $last_name = "", $telephone = "") {
        if ($ecommerce_address_attribute) {
            $address = array(
                'email' => $email,
                'first_name' => $fist_name,
                'last_name' => $last_name,
                'telphone' => $telephone,
                'address_line_1' => $ecommerce_address_attribute->getAddress1(),
                'address_line_2' => $ecommerce_address_attribute->getAddress2(),
                'address_town' => $ecommerce_address_attribute->getCity(),
                'address_state' => $ecommerce_address_attribute->getStateProvince(),
                'address_postcode' => $ecommerce_address_attribute->getPostalCode(),
                'address_country' => $ecommerce_address_attribute->getCountry(),
            );

            $address['address_state'] = strtr(strtolower($address['address_state']), array(
                'Australian Antarctic Territory' => 'AAT',
                'ACT' => 'ACT',
                'New South Wales' => 'NSW',
                'Northern Territory' => 'NT',
                'Queensland' => 'QLD',
                'South Australia' => 'SA',
                'Tasmania' => 'TAS',
                'Victoria' => 'VIC',
                'Western Australia' => 'WA',
                'aat' => 'AAT',
                'act' => 'ACT',
                'nsw' => 'NSW',
                'nt' => 'NT',
                'qld' => 'QLD',
                'sa' => 'SA',
                'tas' => 'TAS',
                'vic' => 'VIC',
                'wa' => 'WA',
            ));

            if (strcmp(strtolower($address['address_country']), 'au') == 0) {
                $address['address_country'] = "Australia";
            } elseif (strcmp(strtolower($address['address_country']), 'nz') == 0) {
                $address['address_country'] = "New Zealand";
            }

            $address['areacode'] = strtr($address['address_state'], array(
                'ACT' => '9ACT',
                'NSW' => '9ANS',
                'NT' => '9ANT',
                'QLD' => '9AQL',
                'SA' => '9ASA',
                'TAS' => '9ATA',
                'VIC' => '9AVI',
                'WA' => '9AWA'
            ));
            if (strcmp($address['address_country'], 'New Zealand') == 0) {
                $address['areacode'] = '9NZE';
            }

            $format_address = array(
                'email' => $address['email'],
                'telephone' => $address['telphone'],
                'fax' => '',
                'business_name' => $address['first_name'] . " " . $address['last_name'],
                'location_area_code' => $address['areacode'],
                'location_town' => $address['address_town'],
                'location_state' => $address['address_state'],
                'location_postcode' => $address['address_postcode'],
                'location_country' => $address['address_country'],
                'location_address_line1' => $address['address_line_1'],
                'location_address_line2' => $address['address_line_2'],
                'location_address_line3' => $address['address_town'] . ", " . $address['address_state'] . " " . $address['address_postcode'],
                'location_address_line4' => $address['address_country'],
                'location_address_line5' => ''
            );

            return $format_address;
        } else {
            return false;
        }
    }

    protected function generateItemsArray($cart) {
        $items = array();
        foreach ($cart->getProducts() as $product) {
            //$product->setQuantity(0);
            $product_name = $product->getProductName();
            $requested_qty = $product->getQuantity();
            $productObj = $product->getProductObject();
            $stock_qty = $productObj->getProductQuantity();
            $titleObject = CupContentTitle::fetchByProductId($productObj->getProductID());
            $title_isbn10 = $titleObject->isbn10;
            $item = array(
                'Pin' => $title_isbn10,
                'DemandQty' => $requested_qty,
                'MailShot' => false
            );

            if ($titleObject->hasAccessCode || $titleObject->hasDownloadableFile) {
                //$item['MailShot'] = '[D]';
                $item['_is_digital'] = true;
            }
            $items[] = $item;
        }
        if (count($items) > 0) {
            return $items;
        }
        return false;
    }

    protected function filterDigitalItems($items) {
        $list = array();
        foreach ($items as $each_item) {
            if (isset($each_item['_is_digital']) && $each_item['_is_digital']) {
                $list[] = $each_item;
            }
        }
        return $list;
    }

    protected function filterPhysicalItems($items) {
        $list = array();
        foreach ($items as $each_item) {
            if (!isset($each_item['_is_digital']) || $each_item['_is_digital'] === false) {
                $list[] = $each_item;
            }
        }
        return $list;
    }

    protected function filterItems($items, $key, $val) {
        $list = array();
        foreach ($items as $each_item) {
            if (isset($each_item[$key]) && strcmp($each_item[$key], $val) == 0) {
                $list[] = $each_item;
            }
        }
        return $list;
    }

    protected function generateOrderLineItems($cart) {
        $orderAttrs = array();
        $adjustments = $cart->getOrderLineItems();
        foreach ($adjustments as $each) {
            $orderAttrs[] = array(
                'name' => $each->getLineItemName(),
                'type' => $each->getLineItemType(),
                'price' => $each->getLineItemTotal()
            );
        }
        return $orderAttrs;
    }

    protected function generateDespatchValue($cart) {
        $orderAttrs = $this->generateOrderLineItems($cart);
        foreach ($orderAttrs as $each) {
            //echo "[{$each['name']}|{$each['type']}|{$each['price']}]";
            if ($each['type'] == '+') {
                //echo "#{{$each['name']}}#";
                //echo "\n\n";
                //echo abs($each['price'] - 13.50);
                //echo "\n\n";
                if (abs($each['price'] - 9.90) < 0.01) {
                    return number_format(9.0, 2, '.', '');
                } elseif (abs($each['price'] - 13.95) < 0.01) {
                    return number_format(12.13, 2, '.', '');
                }
            }
            /*
              if(strcmp($each['name'], 'Basic Shipping') == 0){
              $price_no_gst = $each['price'] / 11 * 10;
              return number_format($price_no_gst, 2, '.', '');
              }
             */
        }
        return false;
    }

    protected function checkBasketType($items) {
        $phy = false;
        $dig = false;

        foreach ($items as $item) {
            if (isset($item['MailShot']) && in_array($item['MailShot'], array('[D]'))) {
                $dig = true;
            } else {
                $phy = true;
            }
        }

        if ($phy && $dig) {
            //mixed basket
            return "P";
        } elseif ($phy) {
            return "P";
        } else {
            return "E";
        }
    }

}

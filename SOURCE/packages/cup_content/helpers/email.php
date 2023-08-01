<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('cart', 'core_commerce');
Loader::model('order/model', 'core_commerce');
Loader::model('order/current', 'core_commerce');
Loader::model('order/product', 'core_commerce');
Loader::model('product/model', 'core_commerce');

Loader::model('title/model', 'cup_content');
loader::library('vista_api', 'cup_content');

Loader::model('cup_content_order/model', 'cup_content');
Loader::model('title/model', 'cup_content');

class CupContentEmailHelper {

    public static function fetchOrderEmailData($order) {
        Loader::model('title/model', 'cup_content');
        $oinfo = array(
            //'orderObj' => $order,
            'orderID' => $order->getOrderID(),
            'invoiceNumber' => $order->getInvoiceNumber(),
            'totalAmount' => $order->getOrderDisplayTotal(),
            'orderDate' => $order->getOrderDateAdded()
        );


        // link to order history
        $pkg = Package::getByHandle('core_commerce');
        if ($pkg->config('PROFILE_MY_ORDERS_ENABLED') && $order->getOrderUserID() > 0) {
            $url = Loader::helper('navigation');
            $history = Page::getByPath('/profile/order_history');
            if ($history instanceof Page) {
                $orderHistoryLink = $url->getLinkToCollection($history, true);
                $oinfo['orderHistoryLink'] = $orderHistoryLink;
            }
        }

        $items = $order->getProducts();
        $i = 0;
        $oinfo['products'] = array();
        foreach ($items as $item) {
            $product = array();
            //$product['object'] = $item;
            $product['id'] = $item->getProductID();
            $product['name'] = $item->getProductName();
            $product['attributes'] = array();
            $attribs = $item->getProductConfigurableAttributes();
            $j = 0;
            foreach ($attribs as $ak) {
                //$products[$i]['attributeNames'][$j++] = ;
                $product['attributes'][$ak->getAttributeKeyName()] = $item->getAttribute($ak);
            }

            $product['quantity'] = $item->getQuantity();
            $product['unit_price'] = $item->getProductObject()->getProductPrice();
            $product['price'] = $item->getProductCartDisplayPrice();

            $titleObject = CupContentTitle::fetchByProductID($product['id']);
            if ($titleObject) {
                $product['isbn13'] = $titleObject->isbn13;
                $product['display_name'] = $titleObject->displayName;
                $product['edition'] = $titleObject->edition;
            }
            $i++;

            $oinfo['products'][] = $product;
        }
        //$mh->addParameter('products', $products);


        $items = $order->getOrderLineItems();
        $oinfo['adjustments'] = array();
        foreach ($items as $item) {
            $adjustment = array();
            $adjustment['name'] = $item->getLineItemName();
            $adjustment['type'] = $item->getLineItemType();
            $adjustment['total'] = $item->getLineItemDisplayTotal();

            $oinfo['adjustments'][] = $adjustment;
        }
        //$mh->addParameter('adjustments', $adjustments);

        $billing = array();
        $billing['first_name'] = $order->getAttribute('billing_first_name');
        $billing['last_name'] = $order->getAttribute('billing_last_name');
        $billing['email'] = $order->getOrderEmail();
        $billing['address1'] = $order->getAttribute('billing_address')->getAddress1();
        $billing['address2'] = $order->getAttribute('billing_address')->getAddress2();
        $billing['city'] = $order->getAttribute('billing_address')->getCity();
        $billing['state'] = $order->getAttribute('billing_address')->getStateProvince();
        $billing['zip'] = $order->getAttribute('billing_address')->getPostalCode();
        $billing['country'] = $order->getAttribute('billing_address')->getCountry();
        $billing['phone'] = $order->getAttribute('billing_phone');
        $oinfo['billing'] = $billing;

        if ($order->getAttribute('shipping_address')) {
            $shipping = array();
            $shipping['first_name'] = $order->getAttribute('shipping_first_name');
            $shipping['last_name'] = $order->getAttribute('shipping_last_name');
            $shipping['email'] = $order->getOrderEmail();
            $shipping['address1'] = $order->getAttribute('shipping_address')->getAddress1();
            $shipping['address2'] = $order->getAttribute('shipping_address')->getAddress2();
            $shipping['city'] = $order->getAttribute('shipping_address')->getCity();
            $shipping['state'] = $order->getAttribute('shipping_address')->getStateProvince();
            $shipping['zip'] = $order->getAttribute('shipping_address')->getPostalCode();
            $shipping['country'] = $order->getAttribute('shipping_address')->getCountry();
            $shipping['phone'] = $order->getAttribute('shipping_phone');
            $oinfo['shipping'] = $shipping;
        }

        $bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
        if ($bill_attr > 0) {
            $akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
            $keys = $bill_attr->getAttributeKeys();
            $billing_attrs = array();
            foreach ($keys as $ak) {
                if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
                    $billing_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
                }
            }
            $oinfo['billing_attrs'] = $billing_attrs;
        }

        $ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
        if ($ship_attr > 0) {
            $akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
            $keys = $ship_attr->getAttributeKeys();
            $shipping_attrs = array();
            foreach ($keys as $ak) {
                if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
                    $shipping_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
                }
            }
            $oinfo['shipping_attrs'] = $shipping_attrs;
        }

        return $oinfo;
    }

}

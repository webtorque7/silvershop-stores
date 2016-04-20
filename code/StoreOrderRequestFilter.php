<?php

class ShopStoreRequestFilter implements RequestFilter
{
    /**
     * sets the current shopping cart to the correct store cart
     */
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        if(class_exists('Fluent')){
            $locale = Fluent::current_locale();
            $store = ShopStore::current_store();

            if($store && $store->exists() && $memberID = Member::currentUserID()){
                // check in my session first
                $orderID = Session::get('shoppingcartid_' . $locale);
                $order = Order::get()->byID($orderID);

                if(empty($order)){
                    $order = Order::create();
                    $order->StoreID = $store->ID;
                    $order->MemberID = $memberID;
                    $order->write();

                    // save it to my session
                    Session::set('shoppingcartid_' . $locale, $order->ID);
                }

                // set it to shop session
                ShoppingCart::singleton()->setCurrent($order);
            }
        }
    }

    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
    }
}
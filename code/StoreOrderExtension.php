<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 20/04/2016
 * Time: 3:43 PM
 */
class StoreOrder extends DataExtension
{
    private static $has_one = array(
        'Store' => 'ShopStore'
    );

    public function onStartOrder(){
        if (class_exists('Fluent')) {
            $locale = Fluent::current_locale();
            $store = ShopStore::current_store();
            if ($store && $store->exists()) {
                $this->StoreID = $store->ID;
                $this->write();
            }

            // save it to my session
            Session::set('shoppingcartid_' . $locale, $this->ID);
        }
    }
}
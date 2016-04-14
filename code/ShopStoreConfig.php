<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/04/2016
 * Time: 1:11 PM
 */
class ShopStoreConfig extends DataObject{
    private static $db = array();

    public function getCurrentConfig()
    {
        if(class_exists('Fluent')) {
            $locale = Fluent::current_locale();
            $country = array_search($locale, ShopConfig::config()->country_locale_mapping);
            $store = ShopStore::get()->filter(array('Country' => $country))->first();
            if ($store->exists() && $store->ShopStoreConfig()->exists()) {
                return $store->ShopStoreConfig();
            }
        }

        return ShopStoreConfig::create();
    }
}
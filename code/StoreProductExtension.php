<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 19/04/2016
 * Time: 11:15 AM
 */
class StoreProductExtension extends DataExtension
{
    private static $db = array();

    private static $has_many = array(
        'StorePrices' => 'StorePrice'
    );

    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldToTab('Root.Pricing', StorePriceField::create('StorePrices', 'Store Prices', ShopStore::get()));
    }

    public function findLocalPrice(){
        $currentStore = ShopStore::current();
        if($currentStore && $currentStore->exists()){
            $StoreCountry = $currentStore->CurrentStoreCountry();

            if($StoreCountry && $StoreCountry->exists()){
                $localPrice = StorePrice::findOrCreate($currentStore->ID, $this->owner->ID, $StoreCountry->Currency);
                return $localPrice;
            }
        }
    }

    public function updateSellingPrice(&$price){
        //TODO create json file to store and fetch these to improve performance
        $localPrice = $this->owner->findLocalPrice();
        if($localPrice && $localPrice->exists()){
            $price = $localPrice->Price;
        }

        return $price;
    }
}
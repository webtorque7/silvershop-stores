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
        if($this->owner->Variations()->exists()){
            $fields->removeByName(array('StorePrices', 'StoreProductStocks'));

        }
        else{
            $fields->addFieldToTab('Root.Pricing', StorePriceField::create('StorePrices', 'Store Prices'));
        }
    }

    public function findLocalPrice(){
        // user see price in the store determined by geoip and not the store currently on
//        $shopCountry = singleton('ShopStore')->CurrentStoreCountry();

        $userShopCountry = $this->owner->ShopCountry();
        if($userShopCountry && $userShopCountry->exists()){
            $userStore = $userShopCountry->ShopStore();

            if($userStore && $userStore->exists()){
                $localPrice = StorePrice::findOrCreate($userStore->ID, $this->owner, $userShopCountry->Currency);
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
    }
}
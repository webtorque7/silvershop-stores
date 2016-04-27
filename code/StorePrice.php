<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 19/04/2016
 * Time: 11:02 AM
 */
class StorePrice extends DataObject
{
    private static $db = array(
        'Price' => 'Currency',
        'Currency' => 'Varchar'
    );

    private static $has_one = array(
        'Product' => 'Product',
        'Store' => 'ShopStore'
    );

    public static function findOrCreate($storeID, $productID, $currency){
        $price = StorePrice::get()->filter(array(
            'StoreID' => $storeID,
            'ProductID' => $productID,
            'Currency' => $currency
        ))->first();

        if(empty($price)){
            $price = StorePrice::create();
            $price->StoreID = $storeID;
            $price->ProductID = $productID;
            $price->Currency = $currency;
            $price->write();
        }

        return $price;
    }
}
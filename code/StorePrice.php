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
        'ProductVariation' => 'ProductVariation',
        'Store' => 'ShopStore'
    );

    public static function findOrCreate($storeID, $product, $currency){
        if($product->ClassName == 'ProductVariation'){
            $price = StorePrice::get()->filter(array(
                'StoreID' => $storeID,
                'ProductVariationID' => $product->ID,
                'Currency' => $currency
            ))->first();

            if(empty($price)){
                $price = StorePrice::create();
                $price->StoreID = $storeID;
                $price->ProductVariationID = $product->ID;
                $price->Currency = $currency;
                $price->write();
            }
        }
        else{
            $price = StorePrice::get()->filter(array(
                'StoreID' => $storeID,
                'ProductID' => $product->ID,
                'Currency' => $currency
            ))->first();

            if(empty($price)){
                $price = StorePrice::create();
                $price->StoreID = $storeID;
                $price->ProductID = $product->ID;
                $price->Currency = $currency;
                $price->write();
            }
        }

        return $price;
    }
}
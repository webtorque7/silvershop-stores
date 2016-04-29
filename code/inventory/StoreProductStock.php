<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 29/04/2016
 * Time: 11:20 AM
 */
class StoreProductStock extends DataObject
{
    private static $db = array(
        'Stock' => 'Int'
    );

    private static $has_one = array(
        'Product' => 'Product',
        'StoreWarehouse' => 'StoreWarehouse'
    );

    public static function findOrCreate($warehouseID, $productID){
        $productStock = StoreProductStock::get()->filter(array(
            'StoreWarehouseID' => $warehouseID,
            'ProductID' => $productID
        ))->first();

        if(empty($productStock)){
            $productStock = StoreProductStock::create();
            $productStock->StoreWarehouseID = $warehouseID;
            $productStock->ProductID = $productID;
            $productStock->write();
        }

        return $productStock;
    }
}
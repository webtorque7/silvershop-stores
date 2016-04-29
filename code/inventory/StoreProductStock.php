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
        'ProductVariation' => 'ProductVariation',
        'StoreWarehouse' => 'StoreWarehouse'
    );

    public static function findOrCreate($warehouseID, $item){
        $idField = 'ProductID';
        if($item->ClassName == 'ProductVariation'){
            $idField = 'ProductVariationID';
        }

        $productStock = StoreProductStock::get()->filter(array(
            'StoreWarehouseID' => $warehouseID,
            $idField => $item->ID
        ))->first();

        if(empty($productStock)){
            $productStock = StoreProductStock::create();
            $productStock->StoreWarehouseID = $warehouseID;
            $productStock->$idField = $item->ID;
            $productStock->write();
        }

        return $productStock;
    }
}
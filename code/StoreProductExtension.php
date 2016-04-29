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
        'StorePrices' => 'StorePrice',
        'StoreProductStocks' => 'StoreProductStock'
    );

    public function updateCMSFields(FieldList $fields) {
        if($this->owner->Variations()->exists()){
            $fields->removeByName(array('StorePrices', 'StoreProductStocks'));
            $fields->addFieldToTab('Root.Pricing',new LabelField('VariationPrice','Store Prices - Because you have one or more variations, the prices can be set in the "Variations" tab.'));
            $fields->addFieldToTab('Root.Inventory',new LabelField('VariationStock','Warehouse Stocks - Because you have one or more variations, the stocks can be set in the "Variations" tab.'));
        }
        else{
            $fields->addFieldToTab('Root.Pricing', StorePriceField::create('StorePrices', 'Store Prices'));
            $fields->addFieldToTab('Root.Inventory', StoreStockField::create('StoreProductStocks', 'Warehouse Stocks'));
        }
    }

    public function findLocalStock(){
        $currentStore = ShopStore::current();
        if($currentStore && $currentStore->exists()){
            $warehouse = $currentStore->StoreWarehouse();

            if($warehouse && $warehouse->exists()){
                $localStock = StoreProductStock::findOrCreate($warehouse->ID, $this->owner->ID);
                return $localStock;
            }
        }
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
    }
}
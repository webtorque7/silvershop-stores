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
        $fields->addFieldToTab('Root.Pricing', GridField::create('StorePrices', 'Store Prices', $this->owner->StorePrices(), GridFieldConfig_RelationEditor::create()));
    }

    public function currentStorePrice(){
        $currentShop = ShopStore::current();
        if($currentShop && $currentShop->exists()){
            $storePrice = $this->owner->StorePrices()->filter(array('StoreID' => $currentShop->ID))->first();
            return $storePrice;
        }
    }

    public function updateSellingPrice($price){
        //TODO create json file to store and fetch these to improve performance
        $storePrice = $this->owner->currentStorePrice();
        if($storePrice && $storePrice->exists()){
            $price = $storePrice->Price;
        }

        return $price;
    }
}
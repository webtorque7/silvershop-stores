<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19/04/2016
 * Time: 1:34 PM
 */

class StoreProductVariationExtension extends DataExtension
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
        $price = $this->owner->Price;
        $storePrice = $this->owner->currentStorePrice();
        if($storePrice && $storePrice->exists()){
            $price = $storePrice->Price;
        }

        //price passed in does not check for store price so redo the check here
        if ($price == 0 && ($parentProduct = $this->owner->Product())) {
            $price = $parentProduct->sellingPrice();
        }

        return $price;
    }

//    public function getPriceString(){
//        $storePrice = $this->owner->currentStorePrice();
//        if($storePrice && $storePrice->exists()){
//            //TODO
//        }
//
//        return '$' . $this->owner->sellingPrice();
//    }
}
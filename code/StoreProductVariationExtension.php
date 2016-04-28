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
        $price = $this->owner->Price;
        $localPrice = $this->owner->findLocalPrice();
        if($localPrice && $localPrice->exists()){
            $price = $localPrice->Price;
        }

        //price passed in does not check for store price so redo the check here
        if ($price == 0 && ($parentProduct = $this->owner->Product())) {
            $price = $parentProduct->sellingPrice();
        }
    }
}
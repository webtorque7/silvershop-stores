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
        $currentCountry = $this->owner->currentCountryCode();
        $storePrice = $this->StorePrices()->filter(array('Country' => $currentCountry))->first();
        return $storePrice;
    }

    public function getBasePrice(){
        $storePrice = $this->currentStorePrice();
        if($storePrice && $storePrice->exists()){
            return $storePrice->Price;
        }

        return $this->BasePrice;
    }

    public function getPriceString(){
        $storePrice = $this->currentStorePrice();
        if($storePrice && $storePrice->exists()){
            return $storePrice->StorePriceString();
        }

        return '$' . $this->BasePrice;
    }
}
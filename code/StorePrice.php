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
        'Country' => 'Varchar'
    );

    private static $has_one = array(
        'Product' => 'Product',
        'Store' => 'ShopStore'
    );

    private static $summary_fields = array(
        'Store.Country' => 'Store',
        'StorePriceString' => 'Price'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(array('ProductID', 'Price', 'Country'));
        $fields->addFieldsToTab('Root.Main', array(
            DropdownField::create('StoreID', 'Store', ShopStore::get()->map('ID', 'Country'))
                ->setEmptyString('Select the store country'),
            TextField::create('Price', 'Store Price')
                ->setDescription('Base price to sell this product at this store.')
                ->setMaxLength(12)
        ));
        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create('Price', 'StoreID');
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $store = $this->Store();
        if($store && $store->exists()){
            $this->Country = $store->Country;
        }
    }

    public function StorePriceString(){
        $store = $this->Store();
        $price = $this->Price;
        if($store && $store->exists()){
            $currency = $store->Currency;
            $symbol = $store->CurrencySymbol();
            $price = $symbol . $this->Price . ' ' . $currency;
        }
        return $price;
    }
}
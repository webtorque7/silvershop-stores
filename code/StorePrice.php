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
        'Price' => 'Currency'
    );

    private static $has_one = array(
        'Product' => 'Product',
        'Store' => 'ShopStore'
    );

    private static $summary_fields = array(
        'Store.Title' => 'Store',
        'Price' => 'Price'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(array('ProductID', 'StoreID', 'Price'));
        $fields->addFieldsToTab('Root.Main', array(
            DropdownField::create('StoreID', 'Store', ShopStore::get()->map('ID', 'Country'))
                ->setEmptyString('Select the store'),
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
}
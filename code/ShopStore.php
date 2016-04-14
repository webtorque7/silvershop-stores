<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/04/2016
 * Time: 4:09 PM
 */
class ShopStore extends DataObject
{
    private static $db = array(
        'Country' => 'Varchar',
        'Currency' => 'Varchar'
    );

    public static $has_many = array(
        'Orders' => 'Order',
    );

    public static $many_many = array(
        'Products' => 'Product'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(array('Country', 'Currency', 'Orders', 'Products'));
        $allowedCountries = SiteConfig::current_site_config()->getCountriesList();

        $fields->addFieldsToTab('Root.Main', array(
            DropdownField::create('Country', 'Country', $allowedCountries)
                ->setEmptyString('Select the country this shop is open to'),
            DropdownField::create('Currency', 'Currency', $this->config()->currencies)
                ->setEmptyString('Select the currency for product pricing')
        ));

        if ($this->exists()) {
            $config = GridFieldConfig_RelationEditor::create();
            $config->removeComponentsByType($config->getComponentByType('GridFieldAddNewButton'));

            $fields->addFieldToTab('Root.Orders',
                GridField::create('Orders', 'Orders', $this->Orders(), $config)
            );

            $fields->addFieldToTab('Root.Products',
                GridField::create('Products', 'Products', $this->Products(), $config)
            );
        }

        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create('Country', 'Currency');
    }

    public function Symbol()
    {
        $symbols = $this->config()->currency_symbols;
        return ($this->Currency && isset($symbols[$this->Currency])) ? $symbols[$this->Currency] : '$';
    }
}
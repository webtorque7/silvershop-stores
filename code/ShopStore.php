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

    private static $has_one = array(
        'TermsPage' => 'SiteTree',
        'CustomerGroup' => 'Group',
        'DefaultProductImage' => 'Image'
    );

    private static $has_many = array(
        'Orders' => 'Order',
    );

    private static $many_many = array(
        'Products' => 'Product'
    );

    public function getCMSFields()
    {
        Requirements::css(STORE_MODULE_DIR . "/css/store-cms.css");
        $fields = parent::getCMSFields();
        $fields->removeByName(array(
            'Main',
            'Country',
            'Currency',
            'TermsPageID',
            'CustomerGroupID',
            'DefaultProductImage',
            'Orders',
            'Products',
        ));

        $fields->addFieldsToTab('Root.Settings', array(
            CompositeField::create(
                DropdownField::create('Country', 'Country', $this->config()->country_locale_mapping)
                    ->setEmptyString('Select the country this shop is open to'),
                DropdownField::create('Currency', 'Currency', array_combine(array_keys($this->config()->currencies), array_keys($this->config()->currencies)))
                    ->setEmptyString('Select the currency for product pricing')
            )->addExtraClass('cms-field-highlight'),
            TreeDropdownField::create('TermsPageID', 'Terms and Conditions Page', 'SiteTree'),
            TreeDropdownField::create('CustomerGroupID', 'Group to add new customers to', 'Group'),
            UploadField::create('DefaultProductImage', 'Default Product Image')
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

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function getCMSValidator()
    {
        return RequiredFields::create('Country', 'Currency');
    }

    public function CurrencySymbol()
    {
        $currencies = $this->config()->currencies;
        return ($this->Currency && isset($currencies[$this->Currency])) ? $currencies[$this->Currency] : '$';
    }

    public function getCurrentConfig()
    {
        if (class_exists('Fluent')) {
            $locale = Fluent::current_locale();
            $country = array_search($locale, $this->config()->country_locale_mapping);
            $store = ShopStore::get()->filter(array('Country' => $country))->first();
            if ($store->exists()) {
                return $store;
            }
        }

        return SiteConfig::current_site_config();
    }
}
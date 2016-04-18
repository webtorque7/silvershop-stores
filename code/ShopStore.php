<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/04/2016
 * Time: 4:09 PM
 */
class ShopStore extends DataObject
{
    private static $singular_name = 'Store';
    private static $plural_name = 'Stores';

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

    private static $summary_fields = array(
        'Country' => 'Country',
        'Currency' => 'Currency'
    );

    public function getCMSFields()
    {
        Requirements::css(STORE_MODULE_DIR . "/css/store-cms.css");
        $fields = parent::getCMSFields();
        $fields->removeByName(array(
            'Main'
        ));

        $fields->addFieldsToTab('Root.Settings.Main', array(
            CompositeField::create(
                DropdownField::create(
                    'Country',
                    'Country',
                    array_combine(array_keys($this->config()->country_locale_mapping), array_keys($this->config()->country_locale_mapping))
                )->setEmptyString('Select the country this shop is open to'),
                DropdownField::create(
                    'Currency',
                    'Currency',
                    array_combine(array_keys($this->config()->currencies), array_keys($this->config()->currencies))
                )->setEmptyString('Select the currency for product pricing')
            )->addExtraClass('cms-field-highlight'),
            UploadField::create('DefaultProductImage', 'Default Product Image'),
            TreeDropdownField::create('CustomerGroupID', 'New customer default group', 'Group'),
        ));

        $fields->addFieldsToTab('Root.Settings.Links', array(
            TreeDropdownField::create('TermsPageID', 'Terms and Conditions Page', 'SiteTree')
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

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if (!DataObject::get_one('ShopStore')) {
            $store = ShopStore::create();
            $store->Country = $this->config()->default_country;
            $store->Currency = $this->config()->default_currency;
            $store->write();
        }
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $dependencyClasses = $this->config()->dependency_classes;
        foreach($dependencyClasses as $class){
            if(class_exists($class) && !empty($this->Country)){
                $existingObjectForCountry = DataObject::get($class, "Country = '" . $this->Country . "'");
                if(empty($existingObjectForCountry->first())){
                    $object = Object::create($class);
                    $object->Country = $this->Country;
                    $object->write();
                }
            }
        }
    }

    public function CurrencySymbol()
    {
        $currency = $this->Currency ? $this->Currency : $this->config()->default_currency;
        $currencies = $this->config()->currencies;
        return $currencies[$currency];
    }

    public function getCurrentConfig()
    {
        if (class_exists('Fluent')) {
            $locale = Fluent::current_locale();
            $country = array_search($locale, $this->config()->country_locale_mapping);
            $store = ShopStore::get()->filter(array('Country' => $country))->first();
            if ($store && $store->exists()) {
                return $store;
            }
        }

        return SiteConfig::current_site_config();
    }
}
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
        'Title' => 'Varchar(200)'
    );

    private static $has_one = array(
        'TermsPage' => 'SiteTree',
        'CustomerGroup' => 'Group',
        'DefaultProductImage' => 'Image'
    );

    private static $has_many = array(
        'StoreCountries' => 'StoreCountry',
        'Orders' => 'Order',
        'Discounts' => 'Discount'
    );

    private static $summary_fields = array(
        'Title' => 'Title',
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(array('Main', 'StoreCountries'));

        $fields->addFieldsToTab('Root.Settings.Main', array(
            TextField::create('Title', 'Title'),
            GridField::create(
                'StoreCountries',
                'Store Countries',
                $this->StoreCountries(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType('GridFieldAddExistingAutocompleter')
                    ->removeComponentsByType('GridFieldDeleteAction')
                    ->addComponent(new GridFieldDeleteAction(false))
            ),
            UploadField::create('DefaultProductImage', 'Default Product Image'),
            TreeDropdownField::create('CustomerGroupID', 'New customer default group', 'Group'),
        ));

        $fields->addFieldsToTab('Root.Settings.Links', array(
            TreeDropdownField::create('TermsPageID', 'Terms and Conditions Page', 'SiteTree')
        ));

        if ($this->exists()) {
            $fields->addFieldToTab('Root.Discounts',
                GridField::create('Discounts', 'Discounts', $this->Discounts(),
                    $discountConfig = GridFieldConfig_RelationEditor::create())
            );
            $discountConfig->removeComponentsByType($discountConfig->getComponentByType('GridFieldAddNewButton'));
        }

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if (!DataObject::get_one('ShopStore')) {
            //create default store
            $store = ShopStore::create();
            $store->write();

            //use default country and currency for default store
            $country = StoreCountry::create();
            $country->Country = $this->config()->default_country;
            $country->write();

            $store->StoreCountries()->add($country);
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
        foreach ($dependencyClasses as $class) {
            if (class_exists($class)) {
                $existingObjectForStore = DataObject::get($class, "ShopStoreID = '" . $this->ID . "'")->first();
                if (empty($existingObjectForStore)) {
                    $object = Object::create($class);
                    $object->ShopStoreID = $this->ID;
                    $object->write();
                }
            }
        }

        if(!$this->Title){
            $countries = array();
            foreach ($this->StoreCountries() as $country) {
                array_push($countries, $country->Country);
            }
            $this->Title = 'Store - ' . implode(', ', $countries);
        }
    }

    public function CurrentStoreCountry()
    {
        $locale = Fluent::current_locale();
        $country = array_search($locale, $this->config()->country_locale_mapping);
        return StoreCountry::get()->filter(array('Country' => $country))->first();
    }

    public function getCurrentConfig()
    {
        if (class_exists('Fluent')) {
            $storeCountry = $this->CurrentStoreCountry();
            if (!empty($storeCountry) && $storeCountry->ShopStoreID) {
                return $storeCountry->ShopStore();
            }
        }
    }

    public static function current()
    {
        return self::getCurrentConfig();
    }
}
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
        'DefaultProductImage' => 'Image',
        'StoreWarehouse' => 'StoreWarehouse'
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
        $fields->removeByName(array('Main', 'Orders', 'Discounts', 'StoreCountries'));

        $fields->addFieldsToTab('Root.Settings.Main', array(
            TextField::create('Title', 'Shop Name')
                ->setDescription('please give a meaningful name for this store so it can be refered to throughout the CMS.
                <br>You can generate a default title by saving the field as blank after adding its countries. eg. Store - NZ'),
            DropdownField::create('StoreWarehouseID', 'Store Warehouse', StoreWarehouse::get()->Map()),
            UploadField::create('DefaultProductImage', 'Default Product Image'),
            TreeDropdownField::create('CustomerGroupID', 'New customer default group', 'Group'),
        ));

        $fields->addFieldsToTab('Root.Settings.Links', array(
            TreeDropdownField::create('TermsPageID', 'Terms and Conditions Page', 'SiteTree')
        ));

        if ($this->exists()) {
            $fields->addFieldToTab('Root.Settings.Main', GridField::create(
                'StoreCountries',
                'Store Countries',
                $this->StoreCountries(),
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType('GridFieldAddExistingAutocompleter')
                    ->removeComponentsByType('GridFieldDeleteAction')
                    ->addComponent(new GridFieldDeleteAction(false))
            ), 'StoreWarehouseID');
        }
        else{
            $fields->addFieldToTab(
                'Root.Settings.Main',
                LiteralField::create('SaveReminder',
                '<p class="warning">Please save before adding the countries this store is open to.</p>'
                ), 'StoreWarehouseID');
        }

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if (!DataObject::get_one('ShopStore')) {
            $defaultCountry = $this->config()->default_country;
            //create default store
            $store = ShopStore::create();
            $store->Title = 'Store - ' . $defaultCountry;
            $store->write();
        }
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

        if (!$this->Title) {
            $countries = array();
            foreach ($this->StoreCountries() as $country) {
                array_push($countries, $country->Country);
            }
            if (!empty($countries)) {
                $this->Title = 'Store - ' . implode(', ', $countries);
            }
        }
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        $countries = $this->StoreCountries();
        foreach ($countries as $country) {
            $country->delete();
        }
    }

    public static function getCurrentConfig()
    {
        $store = ShopStore::get()->first();
        singleton('ShopStore')->extend('updateCurrentConfig', $store);
        return $store;
    }

    public static function current()
    {
        return self::getCurrentConfig();
    }
}
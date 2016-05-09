<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/04/2016
 * Time: 10:59 AM
 */

require_once 'Zend/Locale.php';

class StoreCountry extends DataObject
{
    private static $db = array(
        'Country' => 'Varchar',
        'Currency' => 'Varchar',
        'Symbol' => 'Varchar'
    );

    private static $has_one = array(
        'ShopStore' => 'ShopStore'
    );

    private static $summary_fields = array(
        'Country' => 'Country',
        'Currency' => 'Currency',
        'Symbol' => 'Symbol'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('ShopStoreID');
        $fields->addFieldsToTab('Root.Main', array(
            DropdownField::create(
                'Country',
                'Country',
                array_combine(array_keys(ShopStore::config()->country_locale_mapping), array_keys(ShopStore::config()->country_locale_mapping))
            ),
            TextField::create('Currency', 'Currency')
                ->setDescription('Please use a ISO4217 currency code.'),
            TextField::create('Symbol', 'Symbol')
                ->setDEscription('If you are unsure about the Currency and Symbol fields save them as blank and the system will try and find the default currency code and symbol for the country selected.'),
        ));
        return $fields;
    }

    public function getCMSValidator() {
        return RequiredFields::create('Country');
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        if (!DataObject::get_one('ShopStore')) {
            $defaultCountry = $this->config()->default_country;
            //create default store country
            $country = StoreCountry::create();
            $country->Country = $defaultCountry;
            $country->write();
        }
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->Country && !$this->Currency) {
            $map = Zend_Locale::getTranslationList('CurrencyToRegion');
            $this->Currency = isset($map[$this->Country]) ? $map[$this->Country] : '';
        }

        if ($this->Currency && !$this->Symbol) {
            $map = Zend_Locale::getTranslationList('CurrencySymbol');
            $this->Symbol = isset($map[$this->Currency]) ? $map[$this->Currency] : '';
        }
    }
}
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
            TextField::create('Symbol', 'Symbol'),
            TextField::create('Currency', 'Currency')
        ));
        return $fields;
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
<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/04/2016
 * Time: 4:46 PM
 */
class StorePriceField extends FormField
{
    protected $relationship, $source;

    public function __construct($name, $title = '', $source)
    {
        $this->relationship = $name;
        $this->source = $source;
        parent::__construct($name, $title);
    }

    public function Field($properties = array())
    {
        Requirements::css(STORE_MODULE_DIR . '/css/StorePriceField.css');

        $attributes = array_merge($this->getAttributes(), $properties);
        if ($this->form && $this->form->getRecord()) {

            $record = $this->form->getRecord();
            if ($record->hasMethod($this->relationship)) {

                $storeData = ArrayList::create();
                foreach ($this->source as $store) {
                    $storeArray = array('StoreTitle' => $store->Title, 'StoreID' => $store->ID);

                    $currencyData = ArrayList::create();
                    foreach ($store->StoreCountries() as $countryData) {

                        $storePrice = StorePrice::findOrCreate($store->ID, $record->ID, $countryData->Currency);

                        $currencyData->push(ArrayData::create(array(
                            'Country' => $countryData->Country,
                            'Currency' => $countryData->Currency,
                            'Price' => $storePrice->Price
                        )));
                    }

                    $storeArray['Currencies'] = $currencyData;

                    $storeData->push(ArrayData::create($storeArray));
                }
                $attributes['Stores'] = $storeData;
            }
        }

        return parent::Field($attributes);
    }

    public function saveInto(DataObjectInterface $record)
    {
        $name = $this->name;
        if ($name && $record) {
            //ensure record has an ID
            if (!$record->ID) {
                $record->write();
            }
            $relation = $record->hasMethod($name) ? $record->$name() : null;
            if ($relation && ($relation instanceof RelationList || $relation instanceof UnsavedRelationList)) {
                if (is_array($this->value)) {
                    foreach ($this->value as $storeID => $value) {
                        foreach ($value as $currency => $price) {
                            $storePrice = StorePrice::findOrCreate($storeID, $record->ID, $currency);
                            $storePrice->Price = $price;
                            $storePrice->write();
                        }
                    }
                }
            }
        }
    }
}
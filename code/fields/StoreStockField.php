<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/04/2016
 * Time: 4:46 PM
 */
class StoreStockField extends FormField
{
    protected $relationship;

    public function __construct($name, $title = '')
    {
        $this->relationship = $name;
        parent::__construct($name, $title);
    }

    public function Field($properties = array())
    {
        Requirements::css(STORE_MODULE_DIR . '/css/StoreStockField.css');

        $attributes = array_merge($this->getAttributes(), $properties);
        if ($this->form && $this->form->getRecord()) {

            $record = $this->form->getRecord();
            if ($record->hasMethod($this->relationship)) {

                $warehouseData = ArrayList::create();
                $stores = ShopStore::get();
                foreach ($stores as $store) {
                    $warehouse = $store->StoreWarehouse();
                    if($warehouse && $warehouse->exists()){

                        $storeProductStock = StoreProductStock::findOrCreate($warehouse->ID, $record->ID);

                        $warehouseData->push(ArrayData::create(array(
                            'WarehouseTitle' => $warehouse->Title,
                            'WarehouseID' => $warehouse->ID,
                            'Stock' => $storeProductStock->Stock
                        )));
                    }
                }
                $attributes['Warehouses'] = $warehouseData;
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
                    foreach ($this->value as $warehouseID => $stock) {
                        $storeProductStock = StoreProductStock::findOrCreate($warehouseID, $record->ID);
                        $storeProductStock->Stock = $stock;
                        $storeProductStock->write();
                    }
                }
            }
        }
    }
}
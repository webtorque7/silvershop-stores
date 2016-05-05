<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 20/04/2016
 * Time: 11:39 AM
 */
class StoreDiscountConstraint extends DiscountConstraint
{
    private static $has_one = array(
        'Store' => 'ShopStore'
    );

    public function updateCMSFields(FieldList $fields) {
        if($this->owner->isInDB()){
            $fields->addFieldToTab("Root.Main.Constraints.Main",
                DropdownField::create('StoreID', 'Store', ShopStore::get()->map())
                    ->setEmptyString('Select the store this discount can be applied to')
            );
        }
    }

    public function check(Discount $discount) {
        if(!$discount->StoreID){
            return true;
        }

        $currentShop = ShopStore::current();
        if($currentShop && $currentShop->exists()){
            if($discount->StoreID == $currentShop->ID){
                return true;
            }
            else{
                $this->error("Coupon cannot be used in the current store you are in.");
                return false;
            }
        }

        $this->error("Cannot detect your current store.");
        return false;
    }
}
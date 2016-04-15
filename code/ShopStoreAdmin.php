<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/04/2016
 * Time: 11:16 AM
 */
class ShopStoreAdmin extends ModelAdmin
{
    private static $managed_models = array(
        'ShopStore' => array(
            'title' => 'Stores'
        ),
        'Member' => array(
            'title' => 'Shop Members'
        )
    );
    public static $url_segment = 'store-admin';
    public static $menu_title = 'Stores';

    public function getList() {
        $context = $this->getSearchContext();
        $params = $this->getRequest()->requestVar('q');

        if(is_array($params)) {
            $params = array_map('trim', $params);
        }

        $list = $context->getResults($params);

        if ($this->modelClass === 'Member') {
            $list = $list
                ->innerJoin('Group_Members', '"Group_Members"."MemberID" = "Member"."ID"')
                ->leftJoin('Group', '"Group_Members"."GroupID" = "CurrentGroup"."ID"', 'CurrentGroup')
                ->leftJoin('Group', '"CurrentGroup"."ParentID" = "Parent"."ID"', 'Parent')
                ->where("\"CurrentGroup\".\"Code\" = 'shoppers' OR \"Parent\".\"Code\" = 'shoppers'");
        }

        $this->extend('updateList', $list);

        return $list;
    }

    public function getExportFields() {
        $fields = parent::getExportFields();

        if ($this->modelClass === 'Member') {
            $fields = array(
                'Reference' => 'Reference',
                'FirstName' => 'FirstName',
                'Surname' => 'Surname',
                'Email' => 'Email',
                'Phone' => 'Phone',
                'Mobile' => 'Mobile',
                'Birthday' => 'Birthday',
                'Country' => 'Country',
                'HearAboutUs' => 'HearAboutUs',
                'GroupList' => 'Groups',
                'getMemberType' => "MemberType",
                'QuarterlyBottles' => 'QuarterlyBottles',
                'NumOfShipmentMonths' => 'NumOfShipmentMonths',
                'WineClubShipmentMonths' => 'WineClubShipmentMonths',
                'WineryNotes' => 'WineryNotes',
                'WineSelectionNotes' => 'WineSelectionNotes',
                'FindShippingNotes' => 'ShippingNotes',
                'RelatedEmployee' => 'RelatedEmployee',
                'CaseSize' => "CaseSize",
                'DefaultShippingOption' => 'DefaultShippingOption',

                'DefaultAddress.Address' => 'Default Address',
                'DefaultAddress.Suburb' => 'Default Suburb',
                'DefaultAddress.City' => 'Default City',
                'DefaultAddress.OtherRegion' => 'Default Region/State',
                'DefaultAddress.PostalCode' => 'Default Postal Code',
                'DefaultAddress.Country' => 'Default Country',

                'DefaultShippingAddress.Address' => 'Shipping Address',
                'DefaultShippingAddress.Suburb' => 'Shipping Suburb',
                'DefaultShippingAddress.City' => 'Shipping City',
                'DefaultShippingAddress.OtherRegion' => 'Shipping Region/State',
                'DefaultShippingAddress.PostalCode' => 'Shipping Postal Code',
                'DefaultShippingAddress.Country' => 'Shipping Country',

                'JoinedDate' => 'JoinedDate',
                'LastVisited' => 'LastVisited',
                'UserConfirmedShipment.Nice' => 'UserConfirmedShipment',
                'ShipmentOnHold.Nice' => 'ShipmentOnHold',
                'SignupComplete.Nice' => 'SignupComplete',
                'BillingId' => 'BillingId',
                'CreditCardType' => 'CreditCardType',
                'CardHolderName' => 'CardHolderName'
            );
        }

        return $fields;
    }
}
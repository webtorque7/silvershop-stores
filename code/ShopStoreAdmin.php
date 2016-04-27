<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/04/2016
 * Time: 11:16 AM
 */
class ShopStoreAdmin extends ModelAdmin
{
    private static $url_segment = 'store-admin';
    private static $menu_title = 'Stores';
    private static $managed_models = array(
        'ShopStore' => array(
            'title' => 'Stores'
        ),
        'Order' => array(
            'title' => 'Orders'
        ),
        'Member' => array(
            'title' => 'Shop Members'
        )
    );

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        $gridFieldName = $this->modelClass;
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        if ($this->modelClass === 'Order') {
            if ($gridField) {
                $gridField->getConfig()
                    ->removeComponentsByType('GridFieldAddNewButton')
                    ->addComponent(new GridFieldCourierExportButton('before'));
            }
        }

        return $form;
    }

    public function getList()
    {
        $list = parent::getList();

        if ($this->modelClass === 'Order') {
            $context = $this->getSearchContext();
            $params = $this->request->requestVar('q');

            if (!isset($params['Status'])) {
                $params['Status'] = Order::config()->placed_status;
            }

            $list = $context->getResults($params);

            if ($this->modelClass === 'Order') {
                $list = $list->filter('ClassName', 'Order');
            }

        } elseif ($this->modelClass === 'Member') {
            $list = $list
                ->innerJoin('Group_Members', '"Group_Members"."MemberID" = "Member"."ID"')
                ->leftJoin('Group', '"Group_Members"."GroupID" = "CurrentGroup"."ID"', 'CurrentGroup')
                ->leftJoin('Group', '"CurrentGroup"."ParentID" = "Parent"."ID"', 'Parent')
                ->where("\"CurrentGroup\".\"Code\" = 'shoppers' OR \"Parent\".\"Code\" = 'shoppers'");

        }

        $this->extend('updateList', $list);
        return $list;
    }

    public function getSearchContext()
    {

        if ($this->modelClass === 'Order') {
            $context = singleton($this->modelClass)->getDefaultSearchContext();

            if ($status = $context->getFields()->fieldByName('Status')) {
                $status->setSource(singleton('Order')->dbObject('Status')->enumValues());
                //default status if not set
                $params = $this->request->requestVar('q');
                if (!isset($params['Status'])) {
                    $status->setValue(Order::config()->placed_status);
                } else {
                    $status->setValue($params['Status']);
                }
            }

            if ($type = $context->getFields()->fieldByName('OrderShippingType')) {
                if (isset($params['OrderShippingType'])) {
                    //Debug::dump($params['OrderShippingType']);exit;
                    $type->setValue($params['OrderShippingType']);
                }
            }

            // Namespace fields, for easier detection if a search is present
            foreach ($context->getFields() as $field) {
                $field->setName(sprintf('q[%s]', $field->getName()));
            }
            foreach ($context->getFilters() as $filter) {
                $filter->setFullName(sprintf('q[%s]', $filter->getFullName()));
            }

            $this->extend('updateSearchContext', $context);

            return $context;
        }

        return parent::getSearchContext();
    }

    public function getExportFields()
    {
        if ($this->modelClass === 'WineClubOrder') {
            $fields = array(
                'Reference' => 'Reference',
                'FormattedDate' => 'Placed',
                'FirstName' => 'First Name',
                'Surname' => 'Surname',
                'OrderType' => 'Order Type',
                'OrderShippingType' => 'Shipping Type',
                'MemberTypeName' => 'Member Type',
                'QuarterlyBottlesAmount' => 'Quarterly Bottles',
                'FullShippingAddress' => 'Shipping Address',
                'ProductsBought' => 'Products Purchased',
                'LatestEmail' => 'Customer Email',
                'Total' => 'Total',
                'Status' => 'Status',
            );
        } else {
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
            } else {
                $fields = parent::getExportFields();
            }
        }

        return $fields;
    }
}
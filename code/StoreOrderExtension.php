<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 05/05/2016
 * Time: 9:41 AM
 */
class StoreOrderExtension extends DataExtension
{
    private static $has_one = array(
        'Store' => 'ShopStore',
        'StoreCountry' => 'StoreCountry'
    );
}
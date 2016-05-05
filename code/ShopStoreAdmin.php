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
        'StoreWarehouse' => array(
            'title' => 'Warehouses'
        )
    );
}
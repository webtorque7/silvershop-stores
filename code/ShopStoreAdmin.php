<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/04/2016
 * Time: 11:16 AM
 */
class ShopStoreAdmin extends ModelAdmin
{
    public static $managed_models = array('ShopStore');
    public static $url_segment = 'store-admin';
    public static $menu_title = 'Stores';
}
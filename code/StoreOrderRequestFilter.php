<?php

class ShopStoreRequestFilter implements RequestFilter
{
    private $session_prefix = 'shoppingcartid_';
    /**
     * sets the current shopping cart to the correct store cart
     */
    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        if (Fluent::is_frontend(true) && !Director::is_cli()) {
            $store = $this->findCurrentStore($request);

            if ($store && $store->exists()) {
                $sessionKey = $this->session_prefix . $store->ID;
                ShoppingCart::$cartid_session_name = $sessionKey;
            }
        }
    }

    /**
     * workaround because Fluent::current_locale relies on a controller and this is before controllers are setup.
     */
    public function findCurrentStore(SS_HTTPRequest $request)
    {
        $url = $request->getURL();
        $parts = explode('/', $url);

        $alias = '';

        if ($request->getVar('l')) {
            $alias = $request->getVar('l');
        }
        else {
            $alias = isset($parts[0]) ? $parts[0] : '';
        }

//        $locale = array_search($alias, Fluent::config()->aliases);
//        $country = array_search($locale, ShopStore::config()->country_locale_mapping);

        $country = strtoupper($alias); //alias of a locale should be the country code in lowercase

        $storeCountry = StoreCountry::get()->filter(array('Country' => $country))->first();
        if (!empty($storeCountry) && $storeCountry->ShopStoreID) {
            return $storeCountry->ShopStore();
        }
    }

    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
    }
}
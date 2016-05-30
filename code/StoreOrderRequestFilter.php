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
            $locale = $this->getLocale($request);
            $store = $this->findCurrentStore($locale);

            if ($store && $store->exists()) {
                $sessionKey = $this->session_prefix . $store->ID . '-' . $locale;
                ShoppingCart::$cartid_session_name = $sessionKey;
            }
        }
    }

    public function getLocale(SS_HTTPRequest $request){
        $url = $request->getURL();
        $parts = explode('/', $url);

        $alias = '';
        $locale = '';

        if ($request->getVar('l')) {
            $alias = $request->getVar('l');
            $locale = array_search($alias, Fluent::config()->locales);
        }
        else {
            $alias = isset($parts[0]) ? $parts[0] : '';
            $locale = array_search($alias, Fluent::config()->aliases);
        }

        return $locale;
    }

    /**
     * workaround because Fluent::current_locale relies on a controller and this is before controllers are setup.
     */
    public function findCurrentStore($locale)
    {
        $country = array_search($locale, ShopStore::config()->country_locale_mapping);

        if($country){
            $storeCountry = StoreCountry::get()->filter(array('Country' => $country))->first();
            if (!empty($storeCountry) && $storeCountry->ShopStoreID) {
                return $storeCountry->ShopStore();
            }
        }
    }

    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
    }
}
<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/06/2016
 * Time: 1:03 PM
 */
class CurrencyRate extends DataObject
{
    private static $db = array(
        'Rate' => 'Decimal(10,4)',
        'Currency' => 'Varchar(3)'
    );

    private static $has_one = array(
        'CurrencyConverter' => 'CurrencyConverter'
    );

    private static $summary_fields = array(
        'Currency' => 'Currency',
        'Rate' => 'Rate'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('CurrencyConverterID');
        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('Currency', 'Currency')
                ->setDescription('Please use a ISO4217 currency code.'),
            NumericField::create('Rate', 'Rate')
        ));

        return $fields;
    }
}
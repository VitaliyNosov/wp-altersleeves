<?php

namespace Mythic\Functions\Finance;

use Mythic\Functions\User\MC2_User_Functions;
use Mythic\Helpers\MC2_Currency;

/**
 * Class MC2_Wise
 *
 * @package Mythic\Utils
 *
 * //@todo drop in dependency for https://github.com/alcohol/iso4217/
 */
class MC2_Transferwise_Functions {


    /**
     * @param bool $readable
     *
     * @return string[]
     */
    public static function availableCurrencies( $readable = true ) : array {
        $codes = [
            'AUD',
            'BGN',
            'CAD',
            'CHF',
            'CZK',
            'DKK',
            'EUR',
            'GBP',
            'HRK',
            'HKD',
            'HUF',
            'JPY',
            'NOK',
            'NZD',
            'PLN',
            'PHP',
            'RON',
            'SEK',
            'SGD',
            'USD',
        ];
        if( !$readable ) return $codes;
        $currencies = [];
        foreach( $codes as $code ) $currencies[ $code ] = MC2_Currency::codeToCurrency( $code );

        return $currencies;
    }

}
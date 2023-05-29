<?php

namespace Mythic_Core\Functions;

class MC_Currency_Functions {
    
    /**
     * @param $countries
     *
     * @return mixed
     */
    public static function forceCountryCurrencies( $countries ) {
        // Euros
        $euro_countries = [ 'SE', 'CH', 'HU' ];
        foreach( $euro_countries as $euro_country ) $countries[ $euro_country ] = 'EUR';
        
        // Dollars
        $dollar_countries = [ 'GB', 'HK', 'US', 'RU' ];
        foreach( $dollar_countries as $dollar_country ) $countries[ $dollar_country ] = 'USD';
        
        return $countries;
    }
    
    /**
     * @param string $country_code
     *
     * @return string
     */
    public static function countryCodeToCurrency( $country_code = '' ) : string {
        switch( $country_code ) {
            case 'AL' :
            case 'AD' :
            case 'AM' :
            case 'AT' :
            case 'BY' :
            case 'BE' :
            case 'BA' :
            case 'BG' :
            case 'CH' :
            case 'CY' :
            case 'CZ' :
            case 'DE' :
            case 'DK' :
            case 'EE' :
            case 'ES' :
            case 'FO' :
            case 'FI' :
            case 'GB' :
            case 'GE' :
            case 'GI' :
            case 'GR' :
            case 'HU' :
            case 'HR' :
            case 'IE' :
            case 'IS' :
            case 'IT' :
            case 'LT' :
            case 'LU' :
            case 'LV' :
            case 'MC' :
            case 'MK' :
            case 'MT' :
            case 'NO' :
            case 'NL' :
            case 'PO' :
            case 'PT' :
            case 'RO' :
            case 'RU' :
            case 'SE' :
            case 'SI' :
            case 'SK' :
            case 'SM' :
            case 'TR' :
            case 'UA' :
            case 'VA' :
                return 'EUR';
            default :
                return 'USD';
        }
    }
    
}
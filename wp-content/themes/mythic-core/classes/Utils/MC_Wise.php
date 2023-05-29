<?php

namespace Mythic_Core\Utils;

/**
 * Class MC_Wise
 *
 * @package Mythic_Core\Utils
 *
 * //@todo drop in dependency for https://github.com/alcohol/iso4217/
 */
class MC_Wise {
    
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
        foreach( $codes as $code ) $currencies[ $code ] = self::codeToCurrency( $code );
        
        return $currencies;
    }
    
    /**
     * @param string $code
     *
     * @return string
     */
    public static function codeToCurrency( $code = '' ) : string {
        if( empty( $code ) ) return '';
        $currencies = self::codesAndCurrencies();
        if( isset( $currencies[ $code ] ) ) return $currencies[ $code ];
        
        return '';
    }
    
    /**
     * @return string[]
     */
    public static function codesAndCurrencies() : array {
        return [
            "AFN" => "Afghani",
            "ALL" => "Lek",
            "DZD" => "Algerian Dinar",
            "EUR" => "Euro",
            "AOA" => "Kwanza",
            "ARS" => "Argentine Peso",
            "AMD" => "Armenian Dram",
            "AWG" => "Aruban Florin",
            "AZN" => "Azerbaijanian Manat",
            "BSD" => "Bahamian Dollar",
            "BHD" => "Bahraini Dinar",
            "BDT" => "Taka",
            "BBD" => "Barbados Dollar",
            "BYN" => "Belarussian Ruble",
            "BZD" => "Belize Dollar",
            "BMD" => "Bermudian Dollar",
            "BTN" => "Ngultrum",
            "INR" => "Indian Rupee",
            "BOB" => "Boliviano",
            "BOV" => "Mvdol",
            "BAM" => "Convertible Mark",
            "BWP" => "Pula",
            "BRL" => "Brazilian Real",
            "BND" => "Brunei Dollar",
            "BGN" => "Bulgarian Lev",
            "BIF" => "Burundi Franc",
            "CVE" => "Cabo Verde Escudo",
            "KHR" => "Riel",
            "XAF" => "CFA Franc BEAC",
            "CAD" => "Canadian Dollar",
            "KYD" => "Cayman Islands Dollar",
            "CLF" => "Unidad de Fomento",
            "CLP" => "Chilean Peso",
            "CNY" => "Yuan Renminbi",
            "COP" => "Colombian Peso",
            "COU" => "Unidad de Valor Real",
            "KMF" => "Comoro Franc",
            "CDF" => "Congolese Franc",
            "CRC" => "Costa Rican Colon",
            "HRK" => "Kuna",
            "CUC" => "Peso Convertible",
            "CUP" => "Cuban Peso",
            "CZK" => "Czech Koruna",
            "DKK" => "Danish Krone",
            "DJF" => "Djibouti Franc",
            "DOP" => "Dominican Peso",
            "EGP" => "Egyptian Pound",
            "SVC" => "El Salvador Colon",
            "ERN" => "Nakfa",
            "ETB" => "Ethiopian Birr",
            "FKP" => "Falkland Islands Pound",
            "FJD" => "Fiji Dollar",
            "GMD" => "Dalasi",
            "GEL" => "Lari",
            "GHS" => "Ghana Cedi",
            "GIP" => "Gibraltar Pound",
            "GTQ" => "Quetzal",
            "GNF" => "Guinea Franc",
            "GYD" => "Guyana Dollar",
            "HTG" => "Gourde",
            "HNL" => "Lempira",
            "HKD" => "Hong Kong Dollar",
            "HUF" => "Forint",
            "ISK" => "Iceland Krona",
            "IDR" => "Rupiah",
            "XDR" => "SDR (Special Drawing Right)",
            "IRR" => "Iranian Rial",
            "IQD" => "Iraqi Dinar",
            "ILS" => "New Israeli Sheqel",
            "JMD" => "Jamaican Dollar",
            "JPY" => "Yen",
            "JOD" => "Jordanian Dinar",
            "KZT" => "Tenge",
            "KES" => "Kenyan Shilling",
            "KPW" => "North Korean Won",
            "KRW" => "Won",
            "KWD" => "Kuwaiti Dinar",
            "KGS" => "Som",
            "LAK" => "Kip",
            "LBP" => "Lebanese Pound",
            "LSL" => "Loti",
            "LRD" => "Liberian Dollar",
            "LYD" => "Libyan Dinar",
            "MOP" => "Pataca",
            "MGA" => "Malagasy Ariary",
            "MWK" => "Kwacha",
            "MYR" => "Malaysian Ringgit",
            "MVR" => "Rufiyaa",
            "MRU" => "Ouguiya",
            "MUR" => "Mauritius Rupee",
            "XUA" => "ADB Unit of Account",
            "MXN" => "Mexican Peso",
            "MXV" => "Mexican Unidad de Inversion (UDI)",
            "MDL" => "Moldovan Leu",
            "MNT" => "Tugrik",
            "MZN" => "Mozambique Metical",
            "MMK" => "Kyat",
            "NAD" => "Namibia Dollar",
            "NPR" => "Nepalese Rupee",
            "NIO" => "Cordoba Oro",
            "NGN" => "Naira",
            "NZD" => "New Zealand Dollar",
            "AUD" => "Australian Dollar",
            "USD" => "US Dollar",
            "NOK" => "Norwegian Krone",
            "OMR" => "Rial Omani",
            "PKR" => "Pakistan Rupee",
            "PAB" => "Balboa",
            "PGK" => "Kina",
            "PYG" => "Guarani",
            "PEN" => "Nuevo Sol",
            "PHP" => "Philippine Peso",
            "PLN" => "Zloty",
            "QAR" => "Qatari Rial",
            "MKD" => "Denar",
            "RON" => "Romanian Leu",
            "RUB" => "Russian Ruble",
            "RWF" => "Rwanda Franc",
            "SHP" => "Saint Helena Pound",
            "XCD" => "East Caribbean Dollar",
            "WST" => "Tala",
            "STN" => "Dobra",
            "SAR" => "Saudi Riyal",
            "XOF" => "CFA Franc BCEAO",
            "RSD" => "Serbian Dinar",
            "SCR" => "Seychelles Rupee",
            "SLL" => "Leone",
            "SGD" => "Singapore Dollar",
            "ANG" => "Netherlands Antillean Guilder",
            "XSU" => "Sucre",
            "SBD" => "Solomon Islands Dollar",
            "SOS" => "Somali Shilling",
            "ZAR" => "Rand",
            "SSP" => "South Sudanese Pound",
            "LKR" => "Sri Lanka Rupee",
            "SDG" => "Sudanese Pound",
            "SRD" => "Surinam Dollar",
            "SZL" => "Lilangeni",
            "SEK" => "Swedish Krona",
            "CHE" => "WIR Euro",
            "CHF" => "Swiss Franc",
            "CHW" => "WIR Franc",
            "SYP" => "Syrian Pound",
            "TWD" => "New Taiwan Dollar",
            "TJS" => "Somoni",
            "TZS" => "Tanzanian Shilling",
            "THB" => "Baht",
            "TOP" => "Pa’anga",
            "TTD" => "Trinidad and Tobago Dollar",
            "TND" => "Tunisian Dinar",
            "TRY" => "Turkish Lira",
            "TMT" => "Turkmenistan New Manat",
            "UGX" => "Uganda Shilling",
            "UAH" => "Hryvnia",
            "AED" => "UAE Dirham",
            "GBP" => "Pound Sterling",
            "USN" => "US Dollar (Next day)",
            "UYI" => "Uruguay Peso en Unidades Indexadas (URUIURUI)",
            "UYU" => "Peso Uruguayo",
            "UZS" => "Uzbekistan Sum",
            "VUV" => "Vatu",
            "VEF" => "Bolivar",
            "VND" => "Dong",
            "XPF" => "CFP Franc",
            "MAD" => "Moroccan Dirham",
            "YER" => "Yemeni Rial",
            "ZMW" => "Zambian Kwacha",
            "ZWL" => "Zimbabwe Dollar",
        ];
    }
    
}
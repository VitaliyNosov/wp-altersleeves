<?php

namespace Mythic_Core\Utils;

use Exception;
use GuzzleHttp\Client;
use Mythic_Core\Settings\MC_Data_Settings;
use Mythic_Core\Settings\MC_Site_Settings;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;

/**
 * Class MC_Sendinblue
 *
 * @package Mythic_Core\Utils
 */
class MC_Sendinblue {
    
    public $api_key = '';
    public $partner_key = '';
    public $api_instance;
    
    public function __construct( $data = [] ) {
        $api_key = MC_Data_Settings::value( 'sendinblue_api_key', '' );
        if( empty( $api_key ) ) return;
        $this->setApiKey( $api_key );
        $config = Configuration::getDefaultConfiguration()->setApiKey( 'api-key', $api_key );
        $this->setApiInstance( new ContactsApi( new Client(), $config ) );
        if( empty( $data ) ) return;
        $this->createContact( $data );
    }
    
    /**
     * @return string
     */
    public function getApiKey() : string {
        return $this->api_key;
    }
    
    /**
     * @param string $api_key
     */
    public function setApiKey( string $api_key ) {
        $this->api_key = $api_key;
    }
    
    /**
     * @return string
     */
    public function getPartnerKey() : string {
        return $this->partner_key;
    }
    
    /**
     * @param string $partner_key
     */
    public function setPartnerKey( string $partner_key ) {
        $this->partner_key = $partner_key;
    }
    
    /**
     * @return mixed
     */
    public function getApiInstance() {
        return $this->api_instance;
    }
    
    /**
     * @param mixed $api_instance
     */
    public function setApiInstance( $api_instance ) {
        $this->api_instance = $api_instance;
    }
    
    /**
     * @param array $data
     *
     * @return string|null
     */
    public function createContact( $data = [] ) : ?string {
        if( empty( $data ) || empty( $data['email'] ) ) return null;
        $createContact = new CreateContact( $data );
        
        try {
            return $this->getApiInstance()->createContact( $createContact );
        } catch( Exception $e ) {
            echo 'Exception when calling ContactsApi->createContact: ', $e->getMessage(), PHP_EOL;
        }
        
        return null;
    }
    
    /**
     * @param array $data
     *
     * @return array|mixed
     */
    public static function parseData( $data = [] ) : array {
        $result = [
            'email'      => $data['email'] ?? '',
            'attributes' => $data['attributes'] ?? [],
            'listIds'    => $data['listIds'] ?? [],
        ];
        if( !in_array( 26, $result['listIds'] ) && !empty( $data['marketing_permission'] ) ) $result['listIds'][] = 26;
        $list_ids        = $result['listIds'];
        $parsed_list_ids = [];
        foreach( $list_ids as $list_id ) {
            $parsed_list_ids[] = (int) $list_id;
        }
        $result['listIds'] = $parsed_list_ids;
        
        if( !empty( $data['name'] ) ) {
            $names = MC_Vars::splitStringBySpace( $data['name'] );
            if( is_array( $names ) ) {
                if( !empty( $names[0] ) ) $result['attributes']['FIRSTNAME'] = $names[0];
                if( !empty( $names[1] ) ) $result['attributes']['LASTNAME'] = $names[1];
            }
        }
        unset( $result['name'] );
        
        return $result;
    }
    
    /**
     * @return mixed|null
     */
    public static function newsletterListId() {
        return MC_Site_Settings::value( 'newsletter_sendinblue_list', 0 );
    }
    
    /**
     * @param array $data
     */
    public static function createContactFromData( $data = [] ) {
        new MC_Sendinblue( $data );
    }
    
}
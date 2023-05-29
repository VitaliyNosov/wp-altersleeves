<?php

namespace Mythic\Functions\Storage;

use CURLFile;

class MC2_Opendrive_Functions {
    
    /**
     *  Constants
     */
    const API_SERVER = 'https://dev.opendrive.com/api/';
    
    private $session;
    private $email = '';
    private $password = '';
    
    /**
     * Opendrive constructor.
     */
    public function __construct() {
        $email    = 'james@altersleeves.com';
        $password = 'DNaHt2XhiZYgQYYeeupHYWsq';
        if( empty( $email ) || empty( $password ) ) return;
        $this->set_email( $email );
        $this->set_password( $password );
        $this->set_session( $this->create_session() );
    }
    
    /**
     * @return string
     */
    public function get_email() : string {
        return $this->email;
    }
    
    /**
     * @param string $email
     */
    public function set_email( string $email ) {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function get_password() : string {
        return $this->password;
    }
    
    /**
     * @param string $password
     */
    public function set_password( string $password ) {
        $this->password = $password;
    }
    
    /**
     * @return string
     */
    public function get_session() : string {
        return $this->session;
    }
    
    /**
     * @param mixed $session
     */
    public function set_session( string $session ) {
        $this->session = $session;
    }
    
    /**
     * @return string
     */
    public function create_session() {
        $email    = $this->get_email();
        $password = $this->get_password();
        
        $ch = curl_init( 'https://dev.opendrive.com/api/v1/session/login.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode( [
                                                       'username'   => $email,
                                                       'passwd'     => $password,
                                                       'version'    => '10',
                                                       'partner_id' => '',
                                                   ] ),
        ] );
        $response = curl_exec( $ch );
        if( empty( $response ) ) die( curl_error( $ch ) );
        
        return json_decode( $response, true )['SessionID'];
    }
    
    /**
     * @param string $folderName
     * @param string $parentFolder
     * @param string $description
     *
     * @return string
     */
    public function create_folder( $folderName = '', $parentFolder = '0', $description = '' ) {
        $session  = $this->get_session();
        $postData = [
            'session_id'            => $session,
            //string (required) - Session ID.
            'folder_name'           => $folderName,
            //string (required) - Folder name Valid folder name required (max 255).
            'folder_sub_parent'     => $parentFolder,
            //string (required) - Folder sub parent(folder_id, 0 - for root folder).
            'folder_is_public'      => 2,
            //int (required) - (0 = private, 1 = public, 2 = hidden).
            'folder_public_upl'     => 0,
            //int - Public upload (0 = disabled, 1 = enabled).
            'folder_public_display' => 0,
            //int - Public display (0 = disabled, 1 = enabled).
            'folder_public_dnl'     => 1,
            //int - Public download (0 = disabled, 1 = enabled).
            'folder_description'    => $description
            //string - Folder description.
        ];
        $ch       = curl_init( 'https://dev.opendrive.com/api/v1/folder.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode( $postData ),
        ] );
        $response = curl_exec( $ch );
        if( $response === false ) {
            die( curl_error( $ch ) );
        }
        
        $responseData = json_decode( $response, true );
        
        /* If new folder then return the ID */
        if( array_key_exists( 'FolderID', $responseData ) ) {
            return $responseData['FolderID'];
        }
        
        $folders = $this->get_folder_content( $parentFolder )['Folders'];
        foreach( $folders as $folder ) {
            if( $folder['Name'] == $folderName ) {
                return $folder['FolderID'];
            }
        }
        
        return '';
    }
    
    /**
     * @param string $folderId
     *
     * @return array
     */
    public function get_folder_content( $folderId = '' ) {
        $session      = $this->get_session();
        $url          = 'https://dev.opendrive.com/api/v1/folder/list.json/'.$session.'/'.$folderId;
        $response     = file_get_contents( $url );
        $responseData = json_decode( $response, true );
        
        return $responseData;
    }
    
    /**
     * @param string $file_path
     * @param string $folderId
     * @param bool   $link
     * @param bool   $removePrexisting
     *
     * @return array|mixed|object
     */
    public function upload_file( $file_path = '', $folderId = '0', $link = true, $removePrexisting = true ) {
        $finalResponseData = [];
        if( $file_path == '' ) {
            return $finalResponseData;
        }
        
        $session_id = $this->get_session();
        $file_name  = basename( $file_path );
        
        $file_chunk_size = filesize( $file_path );
        $file_size       = $file_chunk_size;
        
        // 0. Create file
        $postData = [
            'session_id' => $session_id,
            'folder_id'  => $folderId,                    //0 - root folder, otherwise valid folder id
            'file_name'  => $file_name,
            'file_size'  => $file_size,
        ];
        
        // 1. Setup cURL, create file
        $ch = curl_init( self::API_SERVER.'v1/upload/create_file.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode( $postData ),
        ] );
        $response = curl_exec( $ch );
        
        if( $response === false ) {
            die( curl_error( $ch ) );
        }
        $responseData = json_decode( $response, true );
        curl_close( $ch );
        
        /* If file already exists, remove it and reupload */
        if( array_key_exists( 'error', $responseData ) ) {
            if( !$removePrexisting ) return '';
            if( $responseData['error']['code'] == 409 ) {
                $files = $this->get_folder_content( $folderId )['Files'];
                foreach( $files as $file ) {
                    $odFileName = $file['Name'];
                    if( !$odFileName == $file_name ) continue;
                    $odFileId = $file['FileId'];
                    $this->get_folder_content( $odFileId, $folderId );
                }
            }
        }
        
        // 1. Setup cURL, create file
        $ch = curl_init( self::API_SERVER.'v1/upload/create_file.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode( $postData ),
        ] );
        $response = curl_exec( $ch );
        if( $response === false ) {
            die( curl_error( $ch ) );
        }
        $responseData = json_decode( $response, true );
        curl_close( $ch );
        
        // 2. Open File for Upload
        $file_id   = $responseData['FileId'];
        $file_time = isset( $responseData['DirUpdateTime'] ) ? $responseData['DirUpdateTime'] : time();
        $postData  = [
            'session_id' => $session_id,
            'file_id'    => $file_id,
            'file_size'  => $file_size,
        ];
        $ch        = curl_init( self::API_SERVER.'v1/upload/open_file_upload.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode( $postData ),
        ] );
        $response = curl_exec( $ch );
        if( $response === false ) {
            die( curl_error( $ch ) );
        }
        $responseData = json_decode( $response, true );
        curl_close( $ch );
        
        // 3. Send Chunk
        $temp_location = $responseData['TempLocation'];
        $cfile         = new CURLFile( $file_path, 'application/octet-stream', 'file' );
        $postData      = [
            'session_id'    => $session_id,
            'file_id'       => $file_id,
            'temp_location' => $temp_location,
            'chunk_offset'  => 0,
            'chunk_size'    => $file_chunk_size,
            'file_data'     => $cfile,
        ];
        $ch            = curl_init( self::API_SERVER.'v1/upload/upload_file_chunk.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Expect:',
            ],
            CURLOPT_POSTFIELDS     => $postData,
        ] );
        $response = curl_exec( $ch );
        if( $response === false ) {
            die( curl_error( $ch ) );
        }
        curl_close( $ch );
        
        // 5. Close File Upload
        $postData = [
            'session_id'    => $session_id,
            'file_id'       => $file_id,
            'file_size'     => $file_size,
            'temp_location' => $temp_location,
            'file_time'     => $file_time,
        ];
        $ch       = curl_init( self::API_SERVER.'v1/upload/close_file_upload.json' );
        curl_setopt_array( $ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode( $postData ),
        ] );
        $response = curl_exec( $ch );
        if( $response === false ) {
            die( curl_error( $ch ) );
        }
        $finalResponseData = json_decode( $response, true );
        curl_close( $ch );
        
        if( $link ) return $finalResponseData['StreamingLink'];
        
        return $finalResponseData;
    }
    
    /**
     * @param string $fileId
     * @param string $folderId
     *
     * @return array
     */
    public function remove_file( string $fileId = '', string $folderId = '' ) : array {
        if( $fileId == '' && $folderId == '' ) return [];
        
        $session = $this->get_session();
        $url     = 'https://dev.opendrive.com/api/v1/file.json/'.$session.'/'.$fileId;
        $url     .= '?access_folder_id='.$folderId;
        
        $ch = curl_init( $url );
        curl_setopt_array( $ch, [
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
        ] );
        
        $response     = curl_exec( $ch );
        return json_decode( $response, true );
    }
    
}
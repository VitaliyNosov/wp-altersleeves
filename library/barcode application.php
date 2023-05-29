<?php

use Intervention\Image\ImageManagerStatic;

if( empty( $barcode ) || empty( $packaging_image ) ) return;

$barcode_path = ABSPATH.'/resources/img/barcodes/'.$barcode['file'];
$barcode_path = str_replace( '', '.gif', $barcode_path );

$barcode_image_url = 'https://barcode.tec-it.com/barcode.ashx?data='.$barcode['gtin'].'&code=UPCA&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0&hidehrt=False';

$barcode_content = file_get_contents( $barcode_image_url );
$fp              = fopen( $barcode_path, "w" );
fwrite( $fp, $barcode_content );
fclose( $fp );

$barcode_image = ImageManagerStatic::make( $barcode_path );
$barcode_image->resize( 350, null, function( $constraint ) {
    $constraint->aspectRatio();
} );
$barcode_image->save();

$packaging_image = ImageManagerStatic::make( $packaging_image );
$packaging_image->insert( $barcode_image, 'bottom-left', 198, 172 );
$packaging_image->save( str_replace( '.gif', '', $barcode_path ) );




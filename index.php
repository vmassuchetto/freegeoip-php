<?php

define( 'DEBUG', false );

if ( defined( 'DEBUG' ) && DEBUG ) {
    error_reporting( E_ALL );
    ini_set( 'display_errors', 'on' );
}

if ( !$db = new SQLite3( dirname( __FILE__ ) . '/db/ipdb.sqlite' ) )
    exit(1);

function ip2int( $ip ) {
    if ( !$r = ip2long( $ip ) )
        return 0;
    $r += 4294967296;
    return $r;
}

// Options

$ip = empty( $_GET['ip'] ) ? $_SERVER['REMOTE_ADDR'] : $_GET['ip'];

// Query

$stmt = $db->prepare( "
    SELECT
        city_location.country_code,
        country_blocks.country_name,
        city_location.region_code,
        region_names.region_name,
        city_location.city_name,
        city_location.postal_code,
        city_location.latitude,
        city_location.longitude,
        city_location.metro_code,
        city_location.area_code
    FROM city_blocks
    NATURAL JOIN city_location
    INNER JOIN country_blocks ON 1=1
        AND city_location.country_code = country_blocks.country_code
    LEFT OUTER JOIN region_names ON 1=1
        AND city_location.country_code = region_names.country_code
        AND city_location.region_code = region_names.region_code
    WHERE 1=1
        AND city_blocks.ip_start <= :ip
    ORDER BY city_blocks.ip_start DESC
    LIMIT 1
" );
$stmt->bindValue( ':ip' , ip2int( $ip ) );
$result = $stmt->execute();

// JSON Results

echo json_encode( $result->fetchArray( SQLITE3_ASSOC ) );

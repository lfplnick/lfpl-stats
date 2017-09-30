<?php

require_once __DIR__ . '/../../includes/AutoLoader.php';

if (!isset($_GET["whatitdo"])) { die( "It don't do" ); }

$itdo = explode( '/', $_GET["whatitdo"] );

$method = $_SERVER['REQUEST_METHOD'];
$apiSet = array_shift( $itdo );
$baseResource = array_shift( $itdo );
// $ds = new DailyStatistic( array() );

if ( !isset( $apiSet ) || !isset( $baseResource ) ){
    http_response_code( 400 );
    die( 'Unknown resource.' );
}

switch( strtolower( $apiSet ) ){
    case 'stats':
        switch( strtolower( $baseResource ) )
        {
            case 'ds':
                $handler = new DailyStatsHandler( $method, $itdo );
                break;

            default:
                http_response_code( 400 );
                die( 'Unknown resource.' );
                break;
        }
        break;

    case 'sys':
        switch( strtolower( $baseResource))
        {
            case 'branch':
                $handler = new BranchesHandler( $method, $itdo );
                break;

            // case 'sp':
            //     $handler = new ServicePointHandler( $method, $itdo );
            //     break;

            default:
                http_response_code( 400 );
                die( 'Unknown resource.' );
                break;
        }
        break;

    default:
        http_response_code( 400 );
        die( 'Unknown resource.' );
        break;
}

if( $handler->requestIsGood() === RequestHandler::REQUEST_GOOD ){
    $handler->handle();
} else {
    http_response_code( 400 );
    die( 'Unknown resource.' );
}

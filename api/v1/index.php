<?php

require "StatsHandler.php";



if (!isset($_GET["whatitdo"])) { die( "It don't do" ); }

$itdo = explode( '/', $_GET["whatitdo"] );

$apiSet = array_shift( $itdo );
switch( strtolower( $apiSet ) ):
    case 'stats':
        $handler = new StatsHandler( $itdo );
        $handler->handle();
        break;

    default:
        http_response_code( 404 );
        break;
endswitch;


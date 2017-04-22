<?php

require_once __DIR__ . '/../autoload.php';

class AutoLoader{
    static function autoload( $className ) {
        global $sgClasses;
        $filename = false;

        if( isset( $sgClasses[ $className ] ) ) {
            $filename = $sgClasses[ $className ];
        }

        if ( !$filename ) {
            return;
        }

        require $filename;
    }

}

spl_autoload_register( [ 'AutoLoader', 'autoload' ] );
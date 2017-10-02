<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../autoload.php';

class autoloadTest extends TestCase{
    public function testAllClassesResolve(){
        global $sgClasses;
        $this->assertTrue( is_array( $sgClasses ), '$sgClasses is not an array' );

        foreach( $sgClasses as $className => $file ) {
            $this->assertFileExists( $file, "{$className} cannot be loaded" );
        }
    }
}
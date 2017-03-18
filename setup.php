<?php

include 'includes/InitializeDatabase.php';

// This data will eventually be supplied by the user at runtime.
include 'setupdata.php';


echo "Hi there! Let's get you set up.<br>";

$dbInit = new InitializeDatabase( $sgDbAdminUser, $sgDbAdminPw );
if ( !is_a($dbInit, 'InitializeDatabase') ) {
  die( "Hmm, I wasn't able to create the InitializeDatabase object. Terminating script...<br>" );
}

$dbInit->setHost( $sgHost );
$dbInit->setPort( $sgPort );

echo "Setting up {$sgDbName} database and adding tables...";
if ( !$dbInit->createDatabase( $sgDbName ) ) {
  die( "<br>Well that didn't work out so well. Terminating script...<br>" );
}
echo "  Done!<br>";

echo "Setting active database to {$sgDbName}...";
$dbInit->setDbName( $sgDbName );
echo "  Done!<br>";

echo "Creating new user account for {$sgDbStatsUser}...";
$dbInit->setStatsUsername( $sgDbStatsUser );
$dbInit->setStatsPassword( $sgDbStatsPw );

if ( !$dbInit->createStatsUser() ) {
  die( "<br>I wouldn't try using that. Terminating script...<br>");
}
echo "  Done!<br>";

echo "Seeding tables...";
if ( !$dbInit->seedDB() ) {
  die( "<br>Hopefully you didn't need that. Terminating script...<br>" );
}
echo "  Done!<br>";

echo "<br>All done here! Have fun!<br>";
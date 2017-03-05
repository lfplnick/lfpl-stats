<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

# Todo - We need to check if MySQL driver is installed during install process.


class InitializeDatabase {

    /**
     * @var string $dbName Name of database to be initialized
     */
    private $dbName;

    /**
     * @var PDO $db Connection to database
     */
    private $db;

    /**
     * @var string $adminUsername Username for database administrative account
     */
    private $adminUsername;

    /**
     * @var string $adminPassword Password for database administrative account
     */
    private $adminPassword;

    /**
     * @var string $statsUsername Username for application's database account
     */
    private $statsUsername;

    /**
     * @var string $statsPassword Password for application's database account
     */
    private $statsPassword;

}


if (!isset($_POST["admin-user"])) {
    http_response_code(403);
    echo("Must supply username");
    exit;
}

if (!isset($_POST["admin-pw"])) {
    http_response_code(403);
    echo("Must supply password");
    exit;
}

$setStatsUser = true;
if (!isset($_POST["stats-user"]) || ( !isset( $_POST['stats-pw'] ) ) ) {
    $setStatsUser = false;
}

if (!isset($_POST["db"])) {
    http_response_code(403);
    echo("Must supply database name");
    exit;
}

$adminUsername = $_POST["admin-user"];
$adminPassword = $_POST["admin-pw"];

if ( $setStatsUser ) {
    $statsUsername = $_POST["stats-user"];
    $statsPassword = $_POST['stats-pw'];
}

$dbName = $_POST["db"];


try {
    $db = new PDO("mysql:host=localhost", $adminUsername, $adminPassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die( "Connection failed: {$e->getMessage()}" );
} catch (Exception $e) {
    die( "Unhandled exception: {$e->getMessage()}" );
}

try {
    createDatabase($db, $dbName);
} catch (PDOException $e) {
    die( "Unable to create database: {$e->getMessage()}" );
}

try {
    $db = new PDO("mysql:host=localhost;dbname={$dbName}", $adminUsername, $adminPassword);
    createUser($db, $statsUsername, $statsPassword, $dbName);
} catch (PDOException $e) {
    die( "Unable to create new user: {$e->getMessage()}" );
}

$db = null;



function createDatabase(PDO $conn, $dbName) {
    #$sql = "CREATE DATABASE {$dbName}";
    $sql = file_get_contents("tables.sql");
    $sql = str_replace(':db_name', $dbName, $sql);

    $result = $conn->exec($sql);
    #$hasResults = $statement->execute();
    
    echo 'Database created' . var_dump($result);
    #if (!$hasResults) {
    #    echo "Couldn't execute SQL to create database.";
    #}

    #echo 'Database created successfully';
}

function createUser( PDO $conn, $user, $password, $dbName ) {
    $createSql = "CREATE USER '{$user}'@'%' IDENTIFIED BY '{$password}';";
    $createResult = $conn->exec( $createSql );

    echo 'Create SQL:<br/>';
    var_dump($createResult);

    # should translate to "GRANT ALL PRIVILEGES ON `lfpl_stats`.* TO 'statsuser'@'%';"
    $grantSql = "GRANT ALL PRIVILEGES ON * TO '{$user}'@'%';";
    $grantResult = $conn->exec( $grantSql );

    echo 'Grant SQL:<br/>';
    var_dump( $grantResult );
}

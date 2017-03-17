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
     * @var PDO $conn Connection to database
     */
    private $conn;

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



    public function __construct( $dbName, $adminUsername = null, $adminPassword = null ) {
        $this->setDbName( $dbName );

        if ( isset($adminUsername) ) {
            $this->adminUsername = $adminUsername;
        }

        if ( !isset($adminPassword) ) {
            $this->adminPassword = $adminPassword;
        }
    }



    /**
     * Getters and setters. Note that we do NOT supply a method for getting
     * admin or stats usernames and passwords. Just seems like a bad idea.
     */
    public function setDbName( $dbName ) {
        $this->dbName = $dbName;
    }

    public function getDbName() {
        return $this->dbName;
    }

    public function setAdminUsername( $username ) {
        $this->adminUsername = $username;
    }

    public function adminUsernameIsSet() {
        return isset( $this->adminUsername );
    }

    public function setAdminPassword( $password ) {
        $this->adminPassword = $password;
    }

    public function adminPasswordIsSet() {
        return isset( $this->adminPassword );
    }

    public function setStatsUsername( $username ) {
        $this->statsUsername = $username;
    }

    public function statsUsernameIsSet() {
        return isset( $this->statsUsername );
    }

    public function setStatsPassword( $password ) {
        $this->statsPassword = $password;
    }

    public function statsPasswordIsSet() {
        return isset( $this->statsPassword );
    }

    /**
     * Connect to database on localhost specified by dbName.
     *
     * @param string $dbName Name of database to connect to.
     * @return bool Returns true if connection is successful.
     */
    private function connectToDatabase( $dbName = '' ) {
        $connectionString = 'mysql:host=localhost';
        if ( $dbName !== '' ) {
            $connectionString .= ";dbName={$dbName}";
        }

        $this->conn = new PDO( $connectionString, $this->adminUsername, $this->adminPassword );
        $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        return true;
    }

    /**
     * Actually creates the database
     *
     * @return bool Returns true if database is created successfully.
     */
    public function createDatabase() {
        $this->connectToDatabase();

        $sql = file_get_contents("tables.sql");
        $sql = str_replace(':db_name', $this->dbName, $sql);

        $result = $conn->exec($sql);
        return $result;
    }

   /**
    * Creates user which will interact on behalf of the application.
    *
    * @return bool Returns true if user is created successfully and granted
    *  correct permissions.
    */
   public function createUser() {
        $this->connectToDatabase($this->dbName);

        $createSql = "CREATE USER '{$this->statsUsername}'@'%' IDENTIFIED BY '{$this->statsPassword}';";
        $createResult = $this->conn->exec( $createSql );
        $createSuccess = ( $createResult === 0 );

        if ( $createSuccess ) {
            $grantSql = "GRANT ALL PRIVILEGES ON * TO '{$this->user}'@'%';";
            $grantResult = $this->conn->exec( $grantSql );
            $grantSuccess = ( $grantResult === 0 );
        }

        return $createSuccess && $grantSuccess;
    }
}




/*
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
    $conn = new PDO("mysql:host=localhost", $adminUsername, $adminPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die( "Connection failed: {$e->getMessage()}" );
} catch (Exception $e) {
    die( "Unhandled exception: {$e->getMessage()}" );
}

try {
    createDatabase($conn, $dbName);
} catch (PDOException $e) {
    die( "Unable to create database: {$e->getMessage()}" );
}

try {
    $conn = new PDO("mysql:host=localhost;dbname={$dbName}", $adminUsername, $adminPassword);
    createUser($conn, $statsUsername, $statsPassword, $dbName);
} catch (PDOException $e) {
    die( "Unable to create new user: {$e->getMessage()}" );
}

$conn = null;



*/

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

# Todo - We need to check if MySQL driver is installed during install process.

if (!isset($_POST["user"])) {
    http_response_code(403);
    echo("Must supply username");
    exit;
}

if (!isset($_POST["pw"])) {
    http_response_code(403);
    echo("Must supply password");
    exit;
}

if (!isset($_POST["db"])) {
    http_response_code(403);
    echo("Must supply database name");
    exit;
}

$username = $_POST["user"];
$password = $_POST["pw"];
$dbName = $_POST["db"];


try {
    $db = new PDO("mysql:host=localhost", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    CreateDatabase($db, $dbName);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Unhandled exception: ' . $e->getMessage();
}

$db = null;



function CreateDatabase(PDO $conn, $dbName) {
    #$sql = "CREATE DATABASE {$dbName}";
    $sql = file_get_contents("tables.sql");
    $sql = str_replace(':db_name', $dbName, $sql);

    $result = $conn->exec($sql);
    #$hasResults = $statement->execute();
    
    var_dump($result);
    #if (!$hasResults) {
    #    echo "Couldn't execute SQL to create database.";
    #}

    #echo 'Database created successfully';
}

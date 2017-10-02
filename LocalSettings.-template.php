<?php

class LocalSettings
{
    // MySQL server hostname or IP
    public static $dbHost = "localhost";

    // MySQL port number
    public static $dbPort = 3306;

    // Database name
    public static $dbName = "lfplstats";

    // Database user for working with stats. Must have most privileges for
    //  dbName database.
    public static $dbUser = "statsuser";

    // Password for database user.
    public static $dbPw  = "secret";
}

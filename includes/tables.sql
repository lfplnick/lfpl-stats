CREATE DATABASE :db_name;
USE :db_name;

CREATE TABLE stat_branches(
    -- 
    branches_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    branches_name varchar(255) NOT NULL,
    branches_abbr varchar(255)
);

CREATE TABLE stat_desks(
    desks_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    desks_type varchar(255) NOT NULL
);

CREATE TABLE stat_service_points(
    sp_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    sp_name varchar(255) NOT NULL,
    branches_id int unsigned NOT NULL,
    desks_id int unsigned NOT NULL
);

CREATE TABLE stat_daily_stats(
    ds_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ds_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dst_id int unsigned NOT NULL,
    sp_id int unsigned NOT NULL
);

CREATE TABLE stat_daily_stat_types(
    dst_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    dst_name varchar(255) NOT NULL,
    dst_desc varchar(255)
);

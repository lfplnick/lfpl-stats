CREATE DATABASE :db_name;
USE :db_name;

CREATE TABLE stat_branches(
    -- 
    branches_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    branches_name varchar(255) NOT NULL,
    branches_abbr varchar(255)
)ENGINE=InnoDB;

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

CREATE TABLE stat_outreach_primary_type(
    opt_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    opt_name varchar(255) NOT NULL,
    opt_enabled boolean NOT NULL
)ENGINE=InnoDB;


CREATE TABLE stat_outreach_secondary_type(
    ost_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ost_name varchar(255) NOT NULL,
    opt_id int unsigned NOT NULL,
    ost_enabled boolean NOT NULL,
    FOREIGN KEY fk_primary_type(opt_id)
     REFERENCES stat_outreach_primary_type(opt_id)
     ON DELETE CASCADE
     ON UPDATE CASCADE
)ENGINE=InnoDB;


CREATE TABLE stat_outreach_stats(
    os_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    branches_id int unsigned NOT NULL,

    --
    -- Primary Type ID
    --
    -- Foreign key to stat_outreach_primary_type.opt_id. Should not actually
    -- allow this to be NULL, but if primary type is deleted it is set to null
    -- rather than deleting record.
    --
    opt_id int unsigned,

    --
    -- Secondary Type ID
    --
    -- Foreign key to stat_outreach_secondary_type.ost_id. This one can be NULL.
    --
    ost_id int unsigned,
    os_datetime DATETIME NOT NULL,
    os_attendance int unsigned NOT NULL,
    os_name varchar(255) NOT NULL,
    FOREIGN KEY fk_branch(branches_id)
     REFERENCES stat_branches(branches_id)
     ON DELETE CASCADE
     ON UPDATE CASCADE,
    FOREIGN KEY fk_primary_type(opt_id)
     REFERENCES stat_outreach_primary_type(opt_id)
     ON DELETE SET NULL
     ON UPDATE CASCADE,
    FOREIGN KEY fk_secondary_type(ost_id)
     REFERENCES stat_outreach_secondary_type(ost_id)
     ON DELETE SET NULL
     ON UPDATE CASCADE
)ENGINE=InnoDB;


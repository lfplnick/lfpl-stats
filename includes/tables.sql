CREATE DATABASE :db_name;
USE :db_name;

--
-- Library branches
--
-- This could probably be called 'Location', but the various libraries are
-- typically called branches, so we'll use that until this starts being used in
-- non-library organizations.
CREATE TABLE stat_branches(

    --
    -- Branch ID
    branches_id smallint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Branch name
    branches_name varchar(255) NOT NULL,

    --
    -- Branch abbreviation.
    --
    -- Most branches have a digit abbreviation which may be easier to use for
    -- identifying a branch, or at least less ambiguous than the spelling.
    branches_abbr varchar(255),

    --
    -- Branch enabled
    --
    -- Tells whether or not the branch is currently in operation. If set to 0
    -- (false) then all service points at the branch should also be disabled
    -- (i.e. sp_enabled = 0).
    branches_enabled boolean NOT NULL DEFAULT 1,

    -- This can be used for identifying branches, thus should be unique.
    UNIQUE ( branches_abbr )

)ENGINE=InnoDB;


--
-- Desk types
--
-- Usually either reference or circulation, but there are a few other options
-- typically found at libraries.
CREATE TABLE stat_desks(

    --
    -- Desk type ID
    desks_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Desk type
    desks_type varchar(255) NOT NULL

)ENGINE=InnoDB;


--
-- Service points
--
-- These are the actual service points in a particular library branch. For
-- example, "2nd Floor Reference Desk" or "That one desk close to the
-- computers". The type of service point is defined in the stat_desks table and
-- the branch is in the stat_branches table.
CREATE TABLE stat_service_points(

    --
    -- Service point ID
    sp_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Service point name
    sp_name varchar(255) NOT NULL,

    --
    -- Branch ID
    --
    -- Foreign key to the stat_branches.branches_id.
    branches_id smallint unsigned NOT NULL,

    --
    -- Desk type ID
    --
    -- Foreign key to the stat_desks.desks_id. This should only ever be NULL if
    -- the desk type is deleted.
    desks_id int unsigned,

    --
    -- Service point enabled
    --
    -- Tells whether or not the service point is currently in use. If set to 0
    -- (false) then any statistic that tries to record using the service point
    -- should fail.
    sp_enabled boolean NOT NULL DEFAULT 1,

    --
    -- Link to branch. If a branch is deleted then all of its service points are
    -- also removed.
    FOREIGN KEY fk_branch(branches_id)
     REFERENCES stat_branches(branches_id)
     ON DELETE CASCADE
     ON UPDATE CASCADE,

    --
    -- Link to desk type. If a desk type is removed then the service point type
    -- is set to NULL so that it can be reassigned a new desk type. Note that
    -- until the service point is updated it will still point to the old
    -- historic service point thereby using the old desk type.
    FOREIGN KEY fk_desks(desks_id)
     REFERENCES stat_desks(desks_id)
     ON DELETE SET NULL
     ON UPDATE CASCADE

)ENGINE=InnoDB;


--
-- Question types
--
-- These entries represent the types of questions that patrons ask. Or, rather,
-- the type of questions that the library wants to track.
--
-- Stat types are tied to the daily stats table. If there are any records in the
-- stat_daily_stats table that refer to a type in this table then the type is
-- locked and cannot be deleted. It should also not be updated, but that needs
-- to be enforced in-app.
--
-- If the type needs to be retired at that point then the dst_enabled flag
-- should be set to 0. If a type needs to updated then a new entry should be
-- created and the old one retired.
CREATE TABLE stat_daily_stat_types(

    --
    -- Question type ID (or "Daily Stat Type ID" if you prefer)
    dst_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Name of question type. Examples include  "Directional", "Informational",
    -- or possibly "Way Too Personal".
    dst_name varchar(255) NOT NULL,

    --
    -- Optional description of question type. This is just in case the first 255
    -- characters don't do the question type justice.
    dst_desc varchar(255),

    --
    -- Type enabled
    --
    -- Tells whether or not the type is currently in use. If set to 0 (false)
    -- then any statistic that tries to record using the type should fail.
    dst_enabled boolean NOT NULL DEFAULT 1

)ENGINE=InnoDB;


--
-- Daily Statistics
--
-- Table of statistics representing question types answered by staff throughout
-- the day. Each entry is timestamped and is represented by the service point
-- at which the question was answered and the type of question that was asked.
CREATE TABLE stat_daily_stats(

    --
    -- Daily stat ID
    ds_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Timestamp
    --
    -- This is meant to be the time that the statistic was recorded and should
    -- not be overridden. No backdating stats, please.
    ds_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    --
    -- Question type
    --
    -- Foreign key to stat_daily_stat_types.dst_id. This is the type of question
    -- asked. Generally either directional or informational, but others can be
    -- added.
    dst_id int unsigned NOT NULL,

    --
    -- Service point ID
    --
    -- This tells which entry in the service point table holds service point
    -- information for this daily stat record. Service point records should not
    -- be deleted or updated unless there are no daily stats linked to them.
    --
    -- Rather than deleting a referenced service point it should be disabled.
    -- Rather than updating a referenced service point it a new service point
    -- should be created and the old one disabled.
    sp_id int unsigned NOT NULL,

    --
    -- Link to question type. Question types (or daily statistic type) should
    -- not be deleted unless there are no records in this daily stats table that
    -- refer to them.
    FOREIGN KEY fk_dst(dst_id)
     REFERENCES stat_daily_stat_types(dst_id),

    --
    -- Link to service point. Service points should not be deleted or updated
    -- unless there are no records in this daily stats table that refer to them.
    FOREIGN KEY fk_sp(sp_id)
     REFERENCES stat_service_points(sp_id)

)ENGINE=InnoDB;


--
-- Outreach type
--
-- This is the primary, broad type of outreach.
CREATE TABLE stat_outreach_primary_type(

    --
    -- Outreach type ID
    opt_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Outreach type name
    opt_name varchar(255) NOT NULL,

    --
    -- Type enabled
    --
    -- Tells whether or not the type should be available for selection by staff.
    -- This is included so that outreach types can be retired without removing
    -- them from the database. If this is set to 0 (false) then secondary types
    -- that refer to the primary type should also be hidden from staff.
    opt_enabled boolean NOT NULL DEFAULT 1

)ENGINE=InnoDB;


--
-- Outreach secondary (sub-) type
--
-- More specific than the primary types, the subtypes are specific to an
-- individual primary type.
CREATE TABLE stat_outreach_secondary_type(

    --
    -- Secondary type ID
    ost_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Secondary type name
    ost_name varchar(255) NOT NULL,

    --
    -- Primary type ID
    --
    -- Foreign key to stat_outreach_primary_type.opt_id. If primary type is
    -- deleted then all associated secondary types are also deleted. When staff
    -- are entering statistics this is used to determine which set of secondary
    -- types to display.
    opt_id int unsigned NOT NULL,

    --
    -- Type enabled
    --
    -- This is the same idea as with the primary type. The type should be
    -- disabled rather than deleted so that historic data remains intact.
    ost_enabled boolean NOT NULL DEFAULT 1,

    --
    -- Link to branch.
    FOREIGN KEY fk_primary_type(opt_id)
     REFERENCES stat_outreach_primary_type(opt_id)
     ON DELETE CASCADE
     ON UPDATE CASCADE

)ENGINE=InnoDB;


--
-- Outreach statistics
--
-- Main table for storing outreach statistics.
CREATE TABLE stat_outreach_stats(

    --
    -- Outreach statistic ID
    os_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,

    --
    -- Branch ID
    --
    -- Foreign key to stat_branches.branches_id. This is the branch that had the
    -- outreach.
    branches_id smallint unsigned NOT NULL,

    --
    -- Primary Type ID
    --
    -- Foreign key to stat_outreach_primary_type.opt_id. Should not actually
    -- allow this to be NULL, but if primary type is deleted it is set to null
    -- rather than deleting record.
    opt_id int unsigned,

    --
    -- Secondary Type ID
    --
    -- Foreign key to stat_outreach_secondary_type.ost_id. This one can be NULL.
    ost_id int unsigned,

    --
    -- Date and time of outreach
    --
    -- This is both the date and time that the outreach was held. Staff aren't
    -- expected to enter statistics as soon as the outreach starts, so we take
    -- this from the user rather than doing a timestamp.
    os_datetime DATETIME NOT NULL,

    --
    -- Outreach attendance
    --
    -- How many people were there? This is actually a much more invoved question
    -- than it seems, but a full discussoin is outside the scope of this
    -- comment.
    os_attendance int unsigned NOT NULL,

    --
    -- Outreach name
    --
    -- If the outreach doesn't have a name then a brief description should be
    -- entered. Ideally a series of the same outreaches should have the same
    -- name so it's easier to group them together.
    os_name varchar(255) NOT NULL,

    --
    -- Link to branch. If a branch is deleted then all of its records are
    -- removed. MAKE SURE SITE ADMINS KNOW THIS BEFORE ALLOWING DELETION!
    FOREIGN KEY fk_branch(branches_id)
     REFERENCES stat_branches(branches_id)
     ON DELETE CASCADE
     ON UPDATE CASCADE,

    --
    -- Link to primary outreach type. If a primary type is removed then the
    -- stat's primary type is set to NULL. Admins should be encouraged to
    -- disable types rather than delete them.
    FOREIGN KEY fk_primary_type(opt_id)
     REFERENCES stat_outreach_primary_type(opt_id)
     ON DELETE SET NULL
     ON UPDATE CASCADE,

    --
    -- Link to secondary outreach type. If the secondary type is removed then
    -- the stat's secondary type is set to NULL. Admins should be encouraged
    -- to disable types rather than delete them.
    FOREIGN KEY fk_secondary_type(ost_id)
     REFERENCES stat_outreach_secondary_type(ost_id)
     ON DELETE SET NULL
     ON UPDATE CASCADE

)ENGINE=InnoDB;

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
    branches_abbr varchar(255)

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
);


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
    -- Foreign key to the stat_desks.desks_id.
    desks_id int unsigned NOT NULL
);


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
    -- Foreign key to stat_service_points.sp_id. Tells where the question was
    -- answered.
    sp_id int unsigned NOT NULL
);


--
-- Question types
--
-- These entries represent the types of questions that patrons ask. Or, rather,
-- the type of questions that the library wants to track.
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
    dst_desc varchar(255)
);


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
    opt_enabled boolean NOT NULL
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
    ost_enabled boolean NOT NULL,

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

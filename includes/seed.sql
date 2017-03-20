INSERT INTO stat_branches ( branches_name, branches_abbr ) VALUES
 ( 'Main', '0' ),            -- 1
 ( 'Bon Air', '1' ),         -- 2
 ( 'Westport', '2' ),        -- 3
 ( 'Crescent Hill', '3' ),   -- 4
 ( 'Fairdale', '4' ),        -- 5
 ( 'Middletown', '5' ),      -- 6
 ( 'St. Matthews', '7' ),    -- 7
 ( 'Fern Creek', '8' ),      -- 8
 ( 'Highlands', '11' ),      -- 9
 ( 'Iroquois', '13' ),       -- 10
 ( 'Jeffersontown', '15' ),  -- 11
 ( 'Okolona', '18' ),        -- 12
 ( 'Portland', '21' ),       -- 13
 ( 'Shawnee', '22' ),        -- 14
 ( 'Southwest', '25' ),      -- 15
 ( 'Western', '26' ),        -- 16
 ( 'Newburg', '27' ),        -- 17
 ( 'Shively', '29' );        -- 18

INSERT INTO stat_desks ( desks_type ) VALUES
 ( 'Reference' ),            -- 1
 ( 'Circulation' ),          -- 2
 ( 'Child Reference' ),      -- 3
 ( 'Child Circulation' ),    -- 4
 ( 'Teen Reference' ),       -- 5
 ( 'Teen Circulation' ),     -- 6
 ( 'Computer Lab' );         -- 7

INSERT INTO stat_daily_stat_types ( dst_name, dst_desc ) VALUES
 ( 'Directional - Easy', 'Directional questions that take less than two minutes to answer.' ),
 ( 'Directional - Medium', 'Directional questions that take between two and ten minutes to answer.' ),
 ( 'Directional - Difficult', 'Directional questions that take more than ten minutes to answer.' ),
 ( 'Informational - Easy', 'Informational questions that take less than two minutes to answer.' ),
 ( 'Informational - Medium', 'Informational questions that take between two and ten minutes to answer.' ),
 ( 'Informational - Difficult', 'Informational questions that take more than ten minutes to answer.' );

INSERT INTO stat_historic_service_points ( branches_name, sp_name, desks_type ) VALUES
 ( 'Main', 'Circulation', 'Circulation' ),
 ( 'Main', 'MIS I', 'Reference' ),
 ( 'Main', 'MIS II', 'Reference' ),
 ( 'Main', 'Child Circ', 'Child Circulation' ),
 ( 'Main', 'Teen Circ', 'Teen Circulation' ),
 ( 'Main', 'CLC', 'Computer Lab' ),
 ( 'Main', 'Computer Assistant', 'Computer Lab' ),
 ( 'Bon Air', 'Circulation', 'Circulation' ),
 ( 'Bon Air', 'Reference', 'Reference' ),
 ( 'Westport', 'Circulation', 'Circulation' ),
 ( 'Westport', 'Reference', 'Reference' ),
 ( 'Crescent Hill', 'Circulation', 'Circulation' ),
 ( 'Crescent Hill', 'Reference', 'Reference' ),
 ( 'Crescent Hill', 'Child Reference', 'Child Reference' ),
 ( 'Fairdale', 'Circulation', 'Circulation' ),
 ( 'Fairdale', 'Reference', 'Reference' ),
 ( 'Middletown', 'Circulation', 'Circulation' ),
 ( 'Middletown', 'Reference', 'Reference' ),
 ( 'St. Matthews', 'Circulation', 'Circulation' ),
 ( 'St. Matthews', 'Reference', 'Reference' ),
 ( 'St. Matthews', 'Child Circ', 'Child Circulation' ),
 ( 'Fern Creek', 'Circulation', 'Circulation' ),
 ( 'Fern Creek', 'Reference', 'Reference' ),
 ( 'Highlands', 'Circulation', 'Circulation' ),
 ( 'Highlands', 'Reference', 'Reference' ),
 ( 'Highlands', 'YA Outpost', 'Teen Circulation' ),
 ( 'Iroquois', 'Circulation', 'Circulation' ),
 ( 'Iroquois', 'Reference', 'Reference' ),
 ( 'Iroquois', 'CLC', 'Computer Lab' ),
 ( 'Jeffersontown', 'Circulation', 'Circulation' ),
 ( 'Jeffersontown', 'Reference', 'Reference' ),
 ( 'Okolona', 'Circulation', 'Circulation' ),
 ( 'Okolona', 'Reference', 'Reference' ),
 ( 'Portland', 'Circulation', 'Circulation' ),
 ( 'Portland', 'Reference', 'Reference' ),
 ( 'Shawnee', 'Circulation', 'Circulation' ),
 ( 'Shawnee', 'Reference', 'Reference' ),
 ( 'Southwest', 'Circulation', 'Circulation' ),
 ( 'Southwest', 'Reference', 'Reference' ),
 ( 'Western', 'Circulation', 'Circulation' ),
 ( 'Western', 'Reference', 'Reference' ),
 ( 'Western', 'Archives', 'Reference' ),
 ( 'Newburg', 'Circulation', 'Circulation' ),
 ( 'Newburg', 'Reference', 'Reference' ),
 ( 'Shively', 'Circulation', 'Circulation' ),
 ( 'Shively', 'Reference', 'Reference' );

INSERT INTO stat_service_points ( sp_name, branches_id, desks_id, hsp_id ) VALUES
 ( 'Circulation', 1, 2, 1 ),
 ( 'MIS I', 1, 1, 2 ),
 ( 'MIS II', 1, 1, 3 ),
 ( 'Child Circ', 1, 4, 4 ),
 ( 'Teen Circ', 1, 6, 5 ),
 ( 'CLC', 1, 7, 6 ),
 ( 'Computer Assistant', 1, 7, 7 ),
 ( 'Circulation', 2, 2, 8 ),
 ( 'Reference', 2, 1, 9 ),
 ( 'Circulation', 3, 2, 10 ),
 ( 'Reference', 3, 1, 11 ),
 ( 'Circulation', 4, 2, 12 ),
 ( 'Reference', 4, 1, 13 ),
 ( 'Child Reference', 4, 3, 14 ),
 ( 'Circulation', 5, 2, 15 ),
 ( 'Reference', 5, 1, 16 ),
 ( 'Circulation', 6, 2, 17 ),
 ( 'Reference', 6, 1, 18 ),
 ( 'Circulation', 7, 2, 19 ),
 ( 'Reference', 7, 1, 20 ),
 ( 'Child Circ', 7, 4, 21 ),
 ( 'Circulation', 8, 2, 22 ),
 ( 'Reference', 8, 1, 23 ),
 ( 'Circulation', 9, 2, 24 ),
 ( 'Reference', 9, 1, 25 ),
 ( 'YA Outpost', 9, 6, 26 ),
 ( 'Circulation', 10, 2, 27 ),
 ( 'Reference', 10, 1, 28 ),
 ( 'CLC', 10, 7, 29 ),
 ( 'Circulation', 11, 2, 30 ),
 ( 'Reference', 11, 1, 31 ),
 ( 'Circulation', 12, 2, 32 ),
 ( 'Reference', 12, 1, 33 ),
 ( 'Circulation', 13, 2, 34 ),
 ( 'Reference', 13, 1, 35 ),
 ( 'Circulation', 14, 2, 36 ),
 ( 'Reference', 14, 1, 37 ),
 ( 'Circulation', 15, 2, 38 ),
 ( 'Reference', 15, 1, 39 ),
 ( 'Circulation', 16, 2, 40 ),
 ( 'Reference', 16, 1, 41 ),
 ( 'Archives', 16, 1, 42 ),
 ( 'Circulation', 17, 2, 43 ),
 ( 'Reference', 17, 1, 44 ),
 ( 'Circulation', 18, 2, 45 ),
 ( 'Reference', 18, 1, 46 );

INSERT INTO stat_outreach_primary_type ( opt_name ) VALUES
 ( 'Adult Outreach' ),  -- 1
 ( 'Youth Outreach' );  -- 2

INSERT INTO stat_outreach_secondary_type ( ost_name, opt_id ) VALUES
 ( 'Homebound', 1 ),
 ( 'AOS Stop', 1 ),
 ( 'Community Event', 1 ),
 ( 'Community Group', 1 ),
 ( 'Bookmobile Stop', 2 ),
 ( 'School Visit', 2 ),
 ( 'Daycare Visit', 2 );

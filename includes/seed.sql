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

INSERT INTO stat_service_points ( sp_name, branches_id, desks_id ) VALUES
 ( 'Circulation', 1, 2 ),
 ( 'MIS I', 1, 1 ),
 ( 'MIS II', 1, 1 ),
 ( 'Child Circ', 1, 4 ),
 ( 'Teen Circ', 1, 6 ),
 ( 'CLC', 1, 7 ),
 ( 'Computer Assistant', 1, 7 ),
 ( 'Circulation', 2, 2 ),
 ( 'Reference', 2, 1 ),
 ( 'Circulation', 3, 2 ),
 ( 'Reference', 3, 1 ),
 ( 'Circulation', 4, 2 ),
 ( 'Reference', 4, 1 ),
 ( 'Child Reference', 4, 3 ),
 ( 'Circulation', 5, 2 ),
 ( 'Reference', 5, 1 ),
 ( 'Circulation', 6, 2 ),
 ( 'Reference', 6, 1 ),
 ( 'Circulation', 7, 2 ),
 ( 'Reference', 7, 1 ),
 ( 'Child Circ', 7, 4 ),
 ( 'Circulation', 8, 2 ),
 ( 'Reference', 8, 1 ),
 ( 'Circulation', 9, 2 ),
 ( 'Reference', 9, 1 ),
 ( 'YA Outpost', 9, 6 ),
 ( 'Circulation', 10, 2 ),
 ( 'Reference', 10, 1 ),
 ( 'CLC', 10, 7 ),
 ( 'Circulation', 11, 2 ),
 ( 'Reference', 11, 1 ),
 ( 'Circulation', 12, 2 ),
 ( 'Reference', 12, 1 ),
 ( 'Circulation', 13, 2 ),
 ( 'Reference', 13, 1 ),
 ( 'Circulation', 14, 2 ),
 ( 'Reference', 14, 1 ),
 ( 'Circulation', 15, 2 ),
 ( 'Reference', 15, 1 ),
 ( 'Circulation', 16, 2 ),
 ( 'Reference', 16, 1 ),
 ( 'Archives', 16, 1 ),
 ( 'Circulation', 17, 2 ),
 ( 'Reference', 17, 1 ),
 ( 'Circulation', 18, 2 ),
 ( 'Reference', 18, 1 );

INSERT INTO stat_outreach_primary_type ( opt_name, opt_enabled ) VALUES
 ( 'Adult Outreach', 1),  -- 1
 ( 'Youth Outreach', 1);  -- 2

INSERT INTO stat_outreach_secondary_type ( ost_name, opt_id, ost_enabled ) VALUES
 ( 'Homebound', 1, 1),
 ( 'AOS Stop', 1, 1),
 ( 'Community Event', 1, 1),
 ( 'Community Group', 1, 1),
 ( 'Bookmobile Stop', 2, 1),
 ( 'School Visit', 2, 1),
 ( 'Daycare Visit', 2, 1);

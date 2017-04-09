<?php
use PHPUnit\Framework\TestCase;

class DailyStatsHandlerTest extends TestCase{

/***********************
 * Dates
 ***********************/
    /**
     * Valid dates
     */
    const VALID_START_DATE = '20170204';
    const VALID_END_DATE = '20170214';
    const VALID_DATE_RANGE = self::VALID_START_DATE . '-' . self::VALID_END_DATE;
    const SAME_DAY_DATE_RANGE = self::VALID_START_DATE . '-' . self::VALID_START_DATE;

    /**
     * Invalid dates
     */
    const INVALID_DATE = '20171304';
    const INVALID_START_DATE = self::INVALID_DATE . '-' . self::VALID_END_DATE;
    const INVALID_END_DATE =  self::VALID_START_DATE . '-' . self::INVALID_DATE;
    const REVERSED_DATE_RANGE =  self::VALID_END_DATE . '-' . self::VALID_START_DATE;

    /**
     * Other params
     */
    const BRANCH_ABBR = '02';

    /**
     * Valid requests
     */
    private $validRequests = [
        [
            "request" => "branch/" . self::BRANCH_ABBR,
            "counts" => false
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/counts",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_START_DATE,
            "counts" => false
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::SAME_DAY_DATE_RANGE,
            "counts" => false
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_START_DATE . "/counts",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::SAME_DAY_DATE_RANGE . "/counts",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_DATE_RANGE,
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_DATE_RANGE . "/counts",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/",
            "counts" => false
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/counts/",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_START_DATE . "/",
            "counts" => false
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::SAME_DAY_DATE_RANGE . "/",
            "counts" => false
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_START_DATE . "/counts/",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::SAME_DAY_DATE_RANGE . "/counts/",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_DATE_RANGE . "/",
            "counts" => true
        ],
        [ 
            "request" => "branch/" . self::BRANCH_ABBR . "/" . self::VALID_DATE_RANGE . "/counts/",
            "counts" => true
         ]
    ];

    /**
     * Invalid requests
     */
    private $invalidRequests = [
        "branch/" . self::BRANCH_ABBR . "/" . self::INVALID_DATE,
        "branch/" . self::BRANCH_ABBR . "/" . self::INVALID_START_DATE,
        "branch/" . self::BRANCH_ABBR . "/" . self::INVALID_END_DATE,
        "branch/" . self::BRANCH_ABBR . "/" . self::REVERSED_DATE_RANGE,
        ""
    ];



/***********************
 * Helper functions
 ***********************/
    public function random_str( $length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+=-`~[]{};\':",./<>?'){
        $str = '';
        $max = mb_strlen( $keyspace, '8bit' ) - 1;
        for ($i = 0; $i < $length; $i++ ){
            $str .= $keyspace[ random_int( 0, $max ) ];
        }
        return $str;
    }


/***********************
 * Test valid requests
 ***********************/
    /**
     * @test Valid requests are parsed
     */
    public function valid_get_requests_are_good(){
        foreach( $this->validRequests as $request ){
            $params = explode( '/', $request["request"] );
            $dsHandler = new DailyStatsHandler( 'get', $params );

            $this->assertTrue( $dsHandler->requestIsGood(), "*** Request not marked good for request: {$request['request']}" );
            $this->assertFalse( $dsHandler->requestIsNoGood(), "*** Request marked no good for request: {$request['request']}" );
            $this->assertEquals( 'branch', $dsHandler->getPrimaryResource(), "*** Primary resource wrong for request: {$request['request']}" );
            $this->assertEquals( self::BRANCH_ABBR, $dsHandler->getBranchAbbr(), "*** Branch abbreviation wrong for request: {$request['request']}");
            $this->assertEquals( $request['counts'], $dsHandler->getCountsOnly(), "*** Count request wrong for request: {$request['request']}");
        }
    }

    /**
     * @test Branch stats request for current day is correctly parsed
     */
    public function request_for_todays_branch_stats_is_correctly_parsed(){
        $params = explode( '/', 'branch/' . self::BRANCH_ABBR );
        $dsHandler = new DailyStatsHandler( 'get', $params );

        $this->assertTrue( $dsHandler->requestIsGood(), '*** Request no good' );
        $this->assertEquals( 'branch', $dsHandler->getPrimaryResource(), '*** Primary resource wrong' );
        $this->assertEquals( self::BRANCH_ABBR, $dsHandler->getBranchAbbr(), '*** Branch abbreviation wrong');

        $today = date( 'Y-m-d' );
        $this->assertEquals( $today, $dsHandler->getStartDate(), '*** Wrong start date' );
        $this->assertEquals( $today, $dsHandler->getEndDate(), '*** Wrong end date' );

        return $dsHandler;
    }


/***********************
 * Test invalid requests
 ***********************/
    /**
     * @test Invalid requests not marked good
     */
    public function invalid_get_requests_no_good(){
        foreach( $this->invalidRequests as $request ){
            $params = explode( '/', $request );
            $dsHandler = new DailyStatsHandler( 'get', $params );

            $this->assertTrue( $dsHandler->requestIsNoGood(), "*** Invalid request not marked no good for request: {$request}");
            $this->assertFalse( $dsHandler->requestIsGood(), "*** Invalid request marked good for request: {$request}");
        }
    }


/***********************
 * Test isDateRange
 ***********************/
    /**
     * @depends request_for_todays_branch_stats_is_correctly_parsed
     * @test Make sure isDateRange detects valid single date.
     */
    public function isDateRange_detects_valid_single_date( $dsHandler ){
        $params = explode( '/', $this->validRequests[0]['request'] );
        $dsHandler = new DailyStatsHandler( 'get', $params );

        $this->assertTrue( $dsHandler->isDateRange( self::VALID_START_DATE ), '*** Valid date flagged invalid' );
        $this->assertFalse( $dsHandler->isDateRange( self::INVALID_DATE ), '*** Invalid date flagged valid' );
        $this->assertTrue( $dsHandler->isDateRange( self::VALID_DATE_RANGE ), '*** Valid date range (normal) flagged invalid' );
        $this->assertFalse( $dsHandler->isDateRange( self::INVALID_START_DATE ), '*** Invalid date range (bad start date) flagged valid' );
        $this->assertFalse( $dsHandler->isDateRange( self::INVALID_END_DATE ), '*** Invalid date range (bad end date) flagged valid' );
        $this->assertFalse( $dsHandler->isDateRange( self::REVERSED_DATE_RANGE ), '*** Invalid date range (reversed dates) flagged valid' );
        $this->assertTrue( $dsHandler->isDateRange( self::SAME_DAY_DATE_RANGE ), '*** Valid date range (same day) flagged invalid' );
        $this->assertFalse( $dsHandler->isDateRange( '' ), '*** Empty date range flagged as valid' );
        $this->assertFalse( $dsHandler->isDateRange( null ), '*** null date range flagged as valid' );
    }



/***********************
 * Fuzzing Tests
 ***********************/
    /**
     * @group fuzzing
     * @test Throw a bunch of random-ish strings at isDateRange and see what
     *  falls through.
     */
    public function isDateRange_passes_fuzzing( ){
        $params = explode( '/', $this->validRequests[0]['request'] );
        $dsHandler = new DailyStatsHandler( 'get', $params );

        // Test length of valid date (8 chars)
        for ($i=0; $i < 1000; $i++) { 
            $test = $this->random_str( 8 );
            $this->assertFalse( $dsHandler->isDateRange( $test ), 'Allowed random string through: ' . $test );
        }

        // Test length of valid date range (17 chars)
        for ($i=0; $i < 1000; $i++) { 
            $test = $this->random_str( 17 );
            $this->assertFalse( $dsHandler->isDateRange( $test ), 'Allowed random string through: ' . $test );
        }

        // Test random lengths
        for ($i=0; $i < 1000; $i++) { 
            $test = $this->random_str( random_int( 1, 32 ) );
            $this->assertFalse( $dsHandler->isDateRange( $test ), 'Allowed random string through: ' . $test );
        }
    }


/***********************
 * Benchmarking Tests
 ***********************/
    /**
     * @group benchmarking
     * @test Benchmark for isDateRange
     */
    public function isDateRange_benchmark( ){
        $params = explode( '/', $this->validRequests[0]['request'] );
        $dsHandler = new DailyStatsHandler( 'get', $params );

        for ($i=0; $i < 10000; $i++) { 
            $this->assertTrue( $dsHandler->isDateRange( self::VALID_DATE_RANGE ) );
        }
    }
}
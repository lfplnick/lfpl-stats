<?php

class DailyStatsHandler extends StatsHandler {
    protected $primaryResource;
    protected $branchAbbr;

    /**
     * @var DailyStatistic $stat
     */
    protected $stat;

    /**
     * bool $countsOnly Whether we need all stat data or only counts
     */
    protected $countsOnly = false;

    /**
     * string $dateStart Start date for queries
     */
    protected $dateStart;

    /**
     * string $dateEnd End date for queries
     */
    protected $dateEnd;

    /**
     * DailyStatistic $ds
     */
    protected $ds;

    public function __construct( $method, $request ){
        $this->dateStart = date( 'Y-m-d' );
        $this->dateEnd = $this->dateStart;
        $this->baseResource = 'dailystats';
        parent::__construct( $method, $request );
    }

    public function getPrimaryResource(){ return $this->primaryResource; }
    public function getBranchAbbr(){ return $this->branchAbbr; }
    public function getStat(){ return $this->stat; }
    public function getStartDate(){ return $this->dateStart; }
    public function getEndDate(){ return $this->dateEnd; }
    public function getCountsOnly(){ return $this->countsOnly; }

    public function handle(){
        switch( strtolower( $this->method ) ){
            case 'get':
                $this->handleGetRequest();
                break;

            case 'post':
                $this->handlePostRequest();
                break;

            default:
                $this->responseCode = 405;
                $this->sendResponse();

        }
    }

    protected function getParameters(){
        $nextParam = $this->nextParam();

    	if( !$nextParam ){
            $this->requestGood = self::REQUEST_NOGOOD;
            return;
        }

        switch ( $nextParam ) {
            case 'branch':
                // set primary resource
                $this->primaryResource = 'branch';

                if( $this->method === 'get'){ $this->parseGetBranch(); }
                elseif( $this->method === 'post' ){ $this->parsePostBranch(); }
                else{ $this->requestGood = self::REQUEST_NOGOOD; }
                break;
            // End 'branch' case

            case 'stat':
                // Not yet implemented
                $this->requestGood = self::REQUEST_NOGOOD;
                break;
            // End 'stat' case
            
            default:
                $this->requestGood = self::REQUEST_NOGOOD;
                break;
            // End default case
        }// End primary resource switch
    }// end getParameters()

    private function parsePostBranch(){
        // this should only be called if method is 'post' and primary resource
        // is 'branch'
        if( ( $this->method !== 'post' ) || ( $this->primaryResource !== 'branch' ) ){
            return false;
        }

        $nextParam = $this->nextParam();
        if( $nextParam === false ){
            $this->initStatistic();
            if( !( $this->ds instanceof DailyStatistic ) ){
                $this->requestGood = self::REQUEST_NOGOOD;
                return;
            }
        }

        if( $this->nextParam() === false ){
            $this->requestGood = self::REQUEST_GOOD;
            return;
        }

        // good requests shouldn't get this far
        $this->requestGood = self::REQUEST_NOGOOD;

    }// end parsePostBranch

    private function parseGetBranch(){
        // this function should only be called if method is 'get' and primary
        // resource is 'branch'
        if( ( $this->method !== 'get' ) || ( $this->primaryResource !== 'branch' ) ){
            return false;
        }

        // next param should be branch abbreviation, stop parsing if it isn't
        $this->branchAbbr = $this->nextParam();
        if( !$this->branchAbbr ){
            $this->requestGood = self::REQUEST_NOGOOD;
            return;
        }


        // Figure out what next parameter is. It should either be:
        //  1) empty (i.e. false)
        //  2) a date range (or single date)
        //  3) 'counts'
        $nextParam = $this->nextParam();

        // 1) empty
        if( $nextParam === false ){
            $this->requestGood = self::REQUEST_GOOD;
            return;
        }

        // 2) date range
        elseif( $this->isDateRange( $nextParam ) ){
            $this->dateStart = $this->parseStartDate( $nextParam );
            $this->dateEnd = $this->parseEndDate( $nextParam );

            // Allowing queries for multi-day statistic details could result in
            // a large amount of data being transferred.
            if ( $this->dateStart !== $this->dateEnd ) {
                $this->countsOnly = true;
            }
        }

        // 3) 'counts'
        elseif( strtolower( $nextParam ) === 'counts' ) {
            $this->countsOnly = true;
        }

        // None of the above == bad request
        else {
            $this->requestGood = self::REQUEST_NOGOOD;
            return;
        }


        // Rinse and repeat. Next parameter should either be empty or
        //  'counts'.
        $nextParam = $this->nextParam();
        if( $nextParam === false ){
            $this->requestGood = self::REQUEST_GOOD;
            return;
        } elseif ( strtolower( $nextParam ) === 'counts') {
            $this->countsOnly = true;
        } else {
            $this->requestGood = self::REQUEST_NOGOOD;
            return;
        }


        // Last check, parameter list should be empty.
        if ( $this->nextParam() === false ) {
            $this->requestGood = self::REQUEST_GOOD;
            return;
        }


        // Good request would have returned by now.
        $this->requestGood = self::REQUEST_NOGOOD;

    }



    public static function isDateRange( $test ){
        $regex = '/(\d{8})(?:\-(\d{8}))?/';
        $matches = [];

        // This should fail on either 0 (no match) or false (error)
        if( !preg_match( $regex, $test, $matches ) ){ return false; }

        // Make sure second date is later than (or equal to) first date
        if( ( count( $matches ) === 3 ) && ( $matches[1] > $matches[2] ) ){
            return false;
        }

        // Numeric strings should be actual dates
        $testDate;
        for( $iDate = 1; $iDate < count( $matches ); $iDate++ ){
            $year = substr( $matches[ $iDate ], 0, 4 );
            $month = substr( $matches[ $iDate ], 4, 2 );
            $day = substr( $matches[ $iDate ], 6, 2);

            if( !checkdate( $month, $day, $year ) ){ return false; }
        }
        
        return true;
    }

    protected function parseStartDate( $dateRange ){
        return $this->helper_parseDateRange( $dateRange, 'start' );
    }


    protected function parseEndDate( $dateRange ){
        return $this->helper_parseDateRange( $dateRange, 'end' );
    }

    protected function initStatistic(){
        $checksGood = array_key_exists( 'servicepoint', $_POST );
        $checksGood = $checksGood && array_key_exists( 'dstype', $_POST );

        if( $checksGood !== true ){
            return false;
        }

        $sp_id = $_POST['servicepoint'];
        $dst_id = $_POST['dstype'];

        if( !$this->servicePointEnabled( $sp_id ) ){
            $this->responseCode = 400;
            $this->response = 'Service point not enabled.';
            $this->sendResponse();
            exit();
        }

        if( !$this->dailyStatTypeEnabled( $dst_id ) ){
            $this->responseCode = 400;
            $this->response = 'Daily stat type not enabled.';
            $this->sendResponse();
            exit();
        }

        $dsArgs = array(
            'servicepointid' => $sp_id,
            'stattypeid' => $dst_id
        );
        $this->ds = new DailyStatistic( $dsArgs );
    }



/***********************
 * Method handlers
 ***********************/
    /**
     * Handler for GET requests
     */
    protected function handleGetRequest(){

        $this->getConnection();

        $select = $groupby = '';
        if( $this->countsOnly ){
            if( $this->dateStart === $this->dateEnd ){
                $select = 'SELECT hour(ds_timestamp) AS hour, dst_name, count( ds_id ) AS count FROM stat_daily_stats';
                $groupby = ' GROUP BY hour, dst_name';
            } else {
                $select = 'SELECT date(ds_timestamp) AS date, dst_name, count( ds_id ) AS count FROM stat_daily_stats';
                $groupby = ' GROUP BY date, dst_name';
            }
        } else {
            $select = <<<SQL
                SELECT
                    `stat_daily_stats`.`ds_id`,
                    `stat_daily_stats`.`ds_timestamp`,
                    `stat_daily_stat_types`.`dst_name`,
                    `stat_daily_stats`.`sp_id`,
                    `stat_service_points`.`sp_name`,
                    `stat_service_points`.`branches_id`,
                    `stat_branches`.`branches_name`
                FROM stat_daily_stats
SQL;
        }

        $sql = <<<SQL
            LEFT JOIN stat_daily_stat_types
            ON `stat_daily_stats`.`dst_id` = `stat_daily_stat_types`.`dst_id`

            LEFT JOIN stat_service_points
            ON `stat_daily_stats`.`sp_id` = `stat_service_points`.`sp_id`

            LEFT JOIN stat_branches
            ON `stat_service_points`.`branches_id` = `stat_branches`.`branches_id`

            WHERE (
                ds_timestamp > :dateStart
            AND ds_timestamp <= :dateEnd
            AND `stat_branches`.`branches_abbr` = :branchAbbr
            )
SQL;
        $sql = $select . $sql . $groupby . ';';

        $statement = $this->conn->prepare( $sql );

        $endTime = $this->dateEnd . ' 23:59:59';

        $goForExecute = $statement->bindParam( ':dateStart', $this->dateStart, PDO::PARAM_STR );
        $goForExecute = $goForExecute && $statement->bindParam( ':dateEnd', $endTime, PDO::PARAM_STR );
        $goForExecute = $goForExecute && $statement->bindParam( ':branchAbbr', $this->branchAbbr, PDO::PARAM_STR );

        $result = false;

        if( $goForExecute ){
            $result = $statement->execute();
        }

        if( !$goForExecute || !$result ) {
            $this->responseCode = 500;
            $this->response = 'Could not execute query string.';
            $this->sendResponse();
        }

        $records = $statement->fetchAll( PDO::FETCH_ASSOC );

        $this->responseCode = 200;
        header( 'Content-Type: application/json' );
        echo( json_encode($records) );
        

    }// end handleGetRequest()

    /**
     * Handler for POST requests
     */
    protected function handlePostRequest(){
        $result = $this->ds->record();

        if( $result === false ){
            $this->responseCode = 500;
            $this->response = 'Unable to record statistic.';
        } else {
            $this->responseCode = 200;
        }

        $this->sendResponse();
    }

    /**
     * Takes dateRange string ("YYYYMMDD" or "YYYYMMDD-YYYYMMDD") and pulls out
     * either the first date (start) or the second date (end). When there is
     * only one date then first and second dates are the same.
     *
     * Uses isDateRange to validate dateRange string before attempting to parse.
     *
     * @param string $dateRange Date range string, either in "YYYYMMDD" or
     *  "YYYYMMDD-YYYYMMDD" format.
     * @param string $startOrEnd Tells which part of date range string to parse.
     *  This can be either 'start' or 'end', anything else will return false.
     * @return [string|bool] Returns parsed date as "YYYY-MM-DD" formatted
     *  string. Returns false if there is an error.
     */
    protected function helper_parseDateRange( $dateRange, $startOrEnd ){
        if( !$this->isDateRange( $dateRange ) ){ return false; }

        $offset = 0;
        $startOrEnd = strtolower( $startOrEnd );

        if( ( $startOrEnd === 'end' ) && ( strlen( $dateRange ) === 17 ) ){
            $offset = 9;
        }

        $year = substr( $dateRange, 0 + $offset, 4 );
        $month = substr( $dateRange, 4 + $offset, 2 );
        $day = substr( $dateRange, 6 + $offset, 2);

        return "{$year}-{$month}-{$day}";
    }


}

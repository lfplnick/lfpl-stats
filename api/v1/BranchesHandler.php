<?php

class BranchesHandler extends RequestHandler
{

    /**
     * @var Branch The Branch object that request pertains to
     */
    protected $branch;

    private function notImplemented()
    {
        $this->responseCode = 501;
        $this->response = 'Method not yet implemented.';
        $this->sendResponse();
    }

    public function __construct( $method, $request )
    {
        parent::__construct( $method, $request );
    }

    public function handle()
    {
        switch( strtolower( $this->method ) ) 
        {
            case 'get':
                $this->handleGetRequest();
                break;

            default:
                $this->responseCode = 405;
                $this->sendResponse();

        }
    }// end handle()

    protected function getParameters()
    {
        $nextParam = $this->nextParam();

        if( $nextParam === false ) // this needs to be a strict comparison, i.e. not in switch
        {
            $this->requestGood = self::REQUEST_NOGOOD;            
        } else {
            switch ( $nextParam )
            {
                case 'list':
                    $this->baseResource = 'branch';
                    $this->requestGood = self::REQUEST_GOOD;
                    break;

                default:
                    $this->branch = ( new Branch )
                        ->setId( $nextParam )
                        ->search()
                    ;

                    if( $this->branch === false
                      || $this->branch->doesNotExist() )
                    {
                        $this->requestGood = self::REQUEST_NOGOOD;
                    } else {
                        $this->getBranchParameters();
                    }
                    break;

            }// end switch nextParam
        }// end else nextParam !== false
    }// end getParameters()

    /**
     * Parses parameters for service point base resource.
     */
    protected function getBranchParameters()
    {
        if( $this->method === 'get' )
        {
            $nextParam = $this->nextParam();
            switch( $nextParam )
            {
                case false:
                    $this->requestGood = self::REQUEST_NOGOOD;
                    break;

                case 'sp': // generate a list of service points for the given branch
                    $this->baseResource = 'service point';
                    if( $this->nextParam() === false )
                    {
                        $this->requestGood = self::REQUEST_GOOD;
                    } else {
                        $this->requestGood = self::REQUEST_NOGOOD;
                    }
                    break;

                case 'info': // fetch branch info, i.e. id, name, abbr
                    $this->baseResource = 'branch info';
                    $this->requestGood = self::REQUEST_GOOD;
                    break;

                default:
                    $this->requestGood = self::REQUEST_NOGOOD;
                    break;
            }// switch nextParam
        }// method === 'get'
    }// end getBranchParameters

/***********************
 * Method handlers
 ***********************/
    /**
     * Handler for GET requests
     */
    protected function handleGetRequest()
    {
        if( $this->baseResource === 'branch' )
        {
            $this->getConnection();
            $sql = <<<SQL
                SELECT `branches_name`, `branches_abbr`
                FROM `stat_branches`
                WHERE `branches_enabled` = 1;
SQL;

            $statement = $this->conn->prepare( $sql );
            
            $result = $statement->execute();
            if( !$result ){
                $this->responseCode = 500;
                $this->response = 'Could not execute query string.';
                $this->sendResponse();
            }

            $branches = $statement->fetchAll( PDO::FETCH_ASSOC );

            $this->responseCode = 200;
            header( 'Content-Type: application/json', true, $this->responseCode );
            echo( json_encode( $branches ) );


        }// baseResource === 'branch'
        elseif( $this->baseResource === 'service point' )
        {
            $this->getConnection();

            $sql = <<<SQL
                SELECT `ssp`.`sp_id`, `ssp`.`sp_name`, `sd`.`desks_type`
                FROM `stat_service_points` AS `ssp`
                LEFT JOIN `stat_desks` AS `sd` ON `ssp`.`desks_id` = `sd`.`desks_id`
                LEFT JOIN `stat_branches` AS `sb` ON `ssp`.`branches_id` = `sb`.`branches_id`
                WHERE `sb`.`branches_id` = :branchId ;
SQL;

            $statement = $this->conn->prepare( $sql );
            $goForExecute = $statement->bindValue(
                ':branchId',
                $this->branch->getId(),
                PDO::PARAM_INT
            );

            if( $goForExecute === false )
            {
                $this->responseCode = 400;
                $this->response = 'Could not execute query string.';
                $this->sendResponse();
            }

            $statement->execute();

            $this->response = json_encode(
                $statement->fetchAll( PDO::FETCH_ASSOC )
            );
            $this->responseCode = 200;
            $this->sendResponse( 'json' );
        }// baseResource === 'service point'
        elseif ( $this->baseResource === 'branch info' )
        {
            $this->response = array();
            $this->response['branches_id'] = $this->branch->getId();
            $this->response['branches_name'] = $this->branch->getName();
            $this->response['branches_abbr'] = $this->branch->getAbbr();
            $this->response['branches_enabled'] = $this->branch->enabled();

            $this->response = json_encode( $this->response );
            $this->responseCode = 200;
            $this->sendResponse( 'json' );
        }
        else
        {
            $this->responseCode = 400;
            $this->sendResponse();
        }
    }//handleGetRequest
}

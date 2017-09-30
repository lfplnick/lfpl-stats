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

            case 'post':
                $this->handlePostRequest();
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
            if( $this->method === 'post' )
            {
                $this->baseResource = 'create branch';
                $this->getBranchParameters();
            } else {
                $this->requestGood = self::REQUEST_NOGOOD;
            }
        } else {
            switch ( $nextParam )
            {
                case 'list':
                    $nextParam = $this->nextParam();
                    if( $nextParam === false )
                    {
                        $this->baseResource = 'list enabled branches';
                        $this->requestGood = self::REQUEST_GOOD;
                    } elseif( strtolower( $nextParam ) === 'all' ) {
                        $this->baseResource = 'list all branches';
                        $this->requestGood = self::REQUEST_GOOD;
                    } else {
                        $this->requestGood = self::REQUEST_NOGOOD;
                    }
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
            if( false === $nextParam )
            {
                $this->requestGood = self::REQUEST_NOGOOD;                
            } else {
                switch( $nextParam )
                {
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
            }// nextParam !== false
        }// method === 'get'
        elseif( $this->method === 'post' )
        {
            if( isset( $_POST['branchname'] ) )
            {
                $this->branch = ( new Branch )
                    ->setName( $_POST['branchname'] )
                ;

                if( isset( $_POST['branchabbr'] ) )
                {
                    $this->branch
                        ->setAbbr( $_POST['branchabbr'] );
                }

                if( isset( $_POST['branchenabled'] )
                    && $_POST['branchenabled'] === '1' )
                {
                    $this->branch->setEnabled();
                } else {
                    $this->branch->setDisabled();
                }
                $this->requestGood = self::REQUEST_GOOD;
            } else {// no branch name given
                $this->requestGood = self::REQUEST_NOGOOD;
            }
        }
    }// end getBranchParameters

/***********************
 * Method handlers
 ***********************/
    /**
     * Handler for GET requests
     */
    protected function handleGetRequest()
    {
        if( $this->baseResource === 'list enabled branches' 
            || $this->baseResource === 'list all branches' )
        {
            $this->getConnection();
            $select = <<<SQL
                SELECT
                    `branches_id`,
                    `branches_name`,
                    `branches_abbr`
SQL;

            $from = 'FROM `stat_branches`';
            $where = '';

            if( $this->baseResource === 'list enabled branches' )
            {
                $where .= 'WHERE `branches_enabled` = 1';
            } else {
                $select .= ', branches_enabled';
            }
            $sql = $select . ' ' . $from . ' ' . $where . ';';

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
        }// list branches
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

    /**
     * Handler for POST requests
     */
    protected function handlePostRequest()
    {
        if( $this->baseResource === 'create branch' )
        {
            if( $this->branch->create() !== false )
            {
                $this->responseCode = 200;
                $this->response =
                    '[{"branches_id":' . $this->branch->getId() . '}]';
                $this->sendResponse( 'json' );
            } else {
                $this->responseCode = 400;
                $this->response = $this->branch->getErrorMessage();
                $this->sendResponse();
            }
        } else {
            $this->responseCode = 400;
            $this->response = "Unknown resource.";
            $this->sendResponse();
        }
    }
}

<?php

class BranchesHandler extends RequestHandler
{
    private function notImplemented()
    {
        $this->responseCode = 501;
        $this->response = 'Method not yet implemented.';
        $this->sendResponse();
    }

    public function __construct( $method, $request )
    {
        $this->baseResource = 'branches';
        parent::__construct( $method, $request );
    }

    /**
     * TODO: Implement handler
     */
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

    /**
     * TODO: handle 
     */
    protected function getParameters()
    {
        $nextParam = $this->nextParam();

        switch ( $nextParam )
        {
            case false:
                $this->primaryResource = 'branch';
                $this->requestGood = self::REQUEST_GOOD;
                break;

            default:
                $this->requestGood = self::REQUEST_NOGOOD;
                break;
                
        }
    }// end getParameters()

/***********************
 * Method handlers
 ***********************/
    /**
     * Handler for GET requests
     */
     protected function handleGetRequest()
     {
        if( $primaryResource = 'branch' )
        {
            $this->getConnection();
            $sql = 'select `branches_name`, `branches_abbr` from `stat_branches`;';
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


        }// primaryResource = 'branch'
        else
        {
            $this->responseCode = 400;
            $this->sendResponse();
        }
     }
        
}

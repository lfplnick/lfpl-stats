<?php
/**
 * Abstract class for handling api requests.
 */
require_once __DIR__ . '/../../LocalSettings.php';
abstract class RequestHandler
{
    /**
     * @var bool Whether or not the request was able to be parsed
     */
    const REQUEST_NOGOOD = false;
    const REQUEST_GOOD   = true;

    /**
     * @var array $request Query string exploded into array at '/'
     */
    protected $request;  

    /**
     * @var bool $requestGood Tells whether or not the request was able to be
     *  parsed. Should be set to either self::REQUEST_GOOD or
     *  self::REQUEST_NOGOOD.
     */
     protected $requestGood = self::REQUEST_NOGOOD;
     
     /**
     * @var PDO $conn Connection to database, initialized with
     *  $this->getConnection
     */
    protected $conn; 

    /**
     * @var string $method Request method (GET, POST, etc.)
     */
    protected $method; 

    /**
    * @var string $baseResource Primary resource that client is working with.
    *  Ex., daily stats, service points, etc. This should generally be a good
    *  indicator of which database we'll be working with.
    */
   protected $baseResource;  

    /**
    * @var array $parameters
    *  TODO: Do we actually use this? Seems to be a duplicate of $request.
    */
    protected $parameters;
    
    /**
     * @var int $statusCode http response code
     */
     protected $responseCode;

     /**
     * @var string $response Response to client.
     */
    protected $response;


    public function __construct( $method, $request ){
        $this->cleanRequests( $request );
        $this->method = $this->parseMethod( $method );
        $this->getParameters();
    }

    abstract public function handle();

    abstract protected function getParameters();

    public function getResponse(){
        return $this->response;
    }

    /**
     * TODO: This should just return true if request is good. I.e.:
     *       return $this->requestGood === RequestHandler::REQUEST_GOOD;
     */
    public function requestIsGood(){
        return $this->requestGood;
    }

    /**
     * TODO: This should just return true if request is not good. I.e.:
     *       return $this->requestGood === RequestHandler::REQUEST_NOGOOD;
     */
     public function requestIsNoGood(){
        return !$this->requestGood;
    }

    public function sendResponse( $contentType = null ){
        $this->conn = null;
        if( $contentType === 'json' )
        {
            header( 'Content-Type: application/json', true, $this->responseCode );
        } else {
            http_response_code( $this->responseCode );
        }
        if( isset( $this->response ) ){
           echo $this->response;
        }
        exit;
    }

    private function parseMethod( $method ){
        $return = '';
        switch( strtolower( $method ) ){
            case 'get':
                $return = 'get';
                break;
            case 'post':
                $return = 'post';
                break;
            case 'put':
                $return = 'put';
                break;
            case 'delete':
                $return = 'delete';
                break;
        }

        return $return;
    }

    protected function getConnection(){
        if( isset( $this->conn ) ){ return; }

        $connectionString =
            "mysql:host=" . LocalSettings::$dbHost .
            ";port=" . LocalSettings::$dbPort .
            ";dbname=" . LocalSettings::$dbName . ""
        ;

        try{
            $this->conn = new PDO(
                $connectionString,
                LocalSettings::$dbUser,
                LocalSettings::$dbPw
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch( PDOException $e ){
            print_r( $e );
            $this->responseCode = 500;
            $this->response = 'Unable to establish database connection.';
            $this->sendResponse();
        }
    }

    protected function nextParam(){
        return isset( $this->request[0] )
            ? array_shift( $this->request )
            : false
        ;
    }

    private function cleanRequests( $request ){
        $this->request = array();
        foreach ( $request as $part ){
            if ( $part !== '' ){
                array_push( $this->request, $part );
            }
        }
    }



    /**
     * TODO: The following few methods for checking if things are enabled should
     *       probably be moved to a new class (or several).
     */
    public function servicePointEnabled( $sp_id ){
        $args = array(
            'table' => 'stat_service_points',
            'enabledField' => 'sp_enabled',
            'idField' => 'sp_id',
            'id' => $sp_id
        );
        return $this->helper_checkEnabled( $args );
    }

    public function dailyStatTypeEnabled( $dst_id ){
        $args = array(
            'table' => 'stat_daily_stat_types',
            'enabledField' => 'dst_enabled',
            'idField' => 'dst_id',
            'id' => $dst_id
        );
        return $this->helper_checkEnabled( $args );
    }

    private function helper_checkEnabled( $args ){
        $table = $args['table'];
        $enabledField = $args['enabledField'];
        $idField = $args['idField'];
        $id = $args['id'];

        if( !is_numeric( $id ) ){ return false; }

        $this->getConnection();
        $sql = 'SELECT ' . $enabledField . ' AS enabled FROM ' . $table . ' WHERE ' . $idField . ' = :dstId;';

        $statement = $this->conn->prepare( $sql );
        $goForExecute = $statement->bindParam( ':dstId', $id, PDO::PARAM_INT );


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
        1 == $records[0]['enabled'];
        $isEnabled = false;
        if( count( $records ) === 1 
            && ( 1 == $records[0]['enabled'] ) )
        {
                $isEnabled = true;
        }

        return $isEnabled;
    }// helper_checkEnabled
}

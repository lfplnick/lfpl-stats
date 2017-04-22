<?php

require_once __DIR__ . '/../../LocalSettings.php';
abstract class StatsHandler{

    /**
     * bool Whether or not the request was able to be parsed
     */
    const REQUEST_NOGOOD = false;
    const REQUEST_GOOD   = true;

    /**
     * @var PDO $conn Connection to database, initialized with
     *  $this->getConnection
     */
    protected $conn;

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
    protected $baseResource; // i.e. daily stats, outreach stats, program stats

    /**
     * @var string $method Request method
     */
    protected $method;
    protected $response;

    /**
     * @var array $parameters
     */
    protected $parameters;

    /**
     * @var int $statusCode http response code
     */
    protected $responseCode;

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

    public function requestIsGood(){
        return $this->requestGood;
    }

    public function requestIsNoGood(){
        return !$this->requestGood;
    }

    public function sendResponse(){
        $this->conn = null;
        http_response_code( $this->responseCode );
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
        global $sgDbName, $sgDbHost, $sgDbPort, $sg_mysql_statsName, $sg_mysql_statsPw;
        if( isset( $this->conn ) ){ return; }

        $connectionString = "mysql:host={$GLOBALS['sgDbHost']};port={$GLOBALS['sgDbPort']};dbname={$GLOBALS['sgDbName']}";

        try{
            $this->conn = new PDO( $connectionString, $GLOBALS['sg_mysql_statsName'], $GLOBALS['sg_mysql_statsPw'] );
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch( PDOException $e ){
            print_r( $e );
            $this->responseCode = 500;
            $this->response = 'Unable to establish database connection.';
            $this->sendResponse();
        }
    }

    private function routeDS(){
        switch ( $this->method ):
            case 'post':
                if ( !$this->popWID() ) {
                    $this->recordDailyStat();
                }
                break;
            default:
                http_response_code( 405 );
                die( 'Method not allowed for this resource');
                break;
        endswitch;
    }

    protected function nextParam(){
        return isset( $this->request[0] ) ? array_shift( $this->request ) : false;
    }

    private function recordDailyStat(){
        echo "Recording new daily stat";
    }

    private function cleanRequests( $request ){
        $this->request = array();
        foreach ( $request as $part ){
            if ( $part !== '' ){
                array_push( $this->request, $part );
            }
        }
    }

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

        $enabled = $records[0]['enabled'];
        $isEnabled = false;
        if( $enabled == 1 ){
            $isEnabled = true;
        }

        return $isEnabled;
    }
}

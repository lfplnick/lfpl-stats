<?php

class StatsHandler {
    private $whatitdo;
    private $resource; // i.e. daily stats, outreach stats, program stats
    private $method;   // request method
    public function __construct( $whatitdo ){
        $this->whatitdo = $whatitdo;
        $this->resource = $this->parseResource();
        $this->method = $this->parseMethod();
    }

    public function handle(){
        echo "Resource: {$this->resource}<br>";
        echo "Method: {$this->method}<br>";
    }

    private function parseResource(){
        if( count( $this->whatitdo ) <= 0 ) { return ""; }
        
        $resourceTest = array_shift( $this->whatitdo );
        
        $resource = "";
        switch( strtolower( $resourceTest ) ):
            case 'ds':
                $resource = 'dailystats';
                break;
            case 'os':
                $resource = 'outreachstats';
                break;
            case 'ps':
                $resource = 'programstats';
                break;
        endswitch;

        return $resource;
    }

    private function parseMethod(){
        $method = '';
        switch( strtolower( $_SERVER['REQUEST_METHOD'] ) ):
            case 'get':
                $method = 'get';
                break;
            case 'post':
                $method = 'post';
                break;
            case 'put':
                $method = 'put';
                break;
            case 'delete':
                $method = 'delete';
                break;
        endswitch;

        return $method;
    }
}

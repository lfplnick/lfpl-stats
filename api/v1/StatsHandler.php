<?php

class StatsHandler {
    private $whatitdo;
    private $resource; // i.e. daily stats, outreach stats, program stats
    public function __construct( $whatitdo ){
        $this->whatitdo = $whatitdo;
        $this->resource = $this->parseResource();
    }

    public function handle(){
        var_dump( $this->resource );
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
}

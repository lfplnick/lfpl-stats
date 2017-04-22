<?php
use PHPUnit\Framework\TestCase;

class DailyStatisticTest extends TestCase{

    /**
     * @test Constructor can set branch name
     */
    public function construct_can_set_branch_name(){
        $branchName = "Some Branch";
        $args = [ "branchname" => $branchName ];
        $ds = new DailyStatistic( $args );

        $testBranchName = $ds->getBranchName();
        $this->assertEquals( $branchName, $testBranchName );
    }

    /**
     * @test Constructor can set statistic type ID
     */
    public function construct_can_set_statID(){
        $statTypeID = 15;
        $args = [ "stattypeid" => $statTypeID ];
        $ds = new DailyStatistic( $args );

        $testStatTypeID = $ds->getStatTypeId();
        $this->assertEquals( $statTypeID, $testStatTypeID );
    }

    /**
     * @test Constructor can set service point ID
     */
    public function construct_can_set_servicePointID(){
        $servicePointID = 1;
        $args = [ "servicepointid" => $servicePointID ];
        $ds = new DailyStatistic( $args );

        $testServicePointID = $ds->getServicePointID();
        $this->assertEquals( $servicePointID, $testServicePointID );
    }

    /**
     * @test Constructor can set service point name
     */
    public function construct_can_set_servicePointName(){
        $servicePointName = "Awesome Service Point";
        $args = [ "servicepointname" => $servicePointName ];
        $ds = new DailyStatistic( $args );

        $testServicePointName = $ds->getServicePointName();
        $this->assertEquals( $servicePointName, $testServicePointName );
    }

    /**
     * @test Constructor arguments should be case insensitive
     */
    public function construct_arguments_should_be_case_insensitive(){
        $branchName = "Some Branch";
        $statTypeID = 22;
        $servicePointID = 33;
        $servicePointName = "Another Awesome Service Point";

        $args = [
            "brANchNaMe" => $branchName,
            "StAttYPEid" => $statTypeID,
            "sErvIcEpOIntId" => $servicePointID,
            "SeRViCePoiNTNaMe" => $servicePointName
        ];

        $ds = new DailyStatistic( $args );
        $testBranchName = $ds->getBranchName();
        $this->assertEquals( $branchName, $testBranchName );

        $testStatTypeID = $ds->getStatTypeId();
        $this->assertEquals( $statTypeID, $testStatTypeID );

        $testServicePointID = $ds->getServicePointID();
        $this->assertEquals( $servicePointID, $testServicePointID);

        $testServicePointName = $ds->getServicePointName();
        $this->assertEquals( $servicePointName, $testServicePointName );
    }

    /**
     * @test Constructor should work with no arguments
     */
    public function construct_without_arguments_should_work(){
        $ds = new DailyStatistic();
        $this->assertInstanceOf( DailyStatistic::class, $ds );

        return $ds;
    }

    /**
     * @depends construct_without_arguments_should_work
     * @test Branch Name getter and setter
     */
    public function test_branchName_get_and_set( DailyStatistic $ds ){
        $branchName = "Another Branch";
        $ds->setBranchName( $branchName );

        $testBranchName = $ds->getBranchName( $branchName );
        $this->assertEquals( $branchName, $testBranchName );
    }

    /**
     * @depends construct_without_arguments_should_work
     * @test Stat Type ID getter and setter
     */
    public function test_statTypeID_get_and_set( DailyStatistic $ds ){
        $statTypeID = 44;
        $ds->setStatTypeId( $statTypeID );

        $testStatTypeID = $ds->getStatTypeId();
        $this->assertEquals( $statTypeID, $testStatTypeID );
    }

    /**
     * @depends construct_without_arguments_should_work
     * @test Service Point ID getter and setter
     */
    public function test_servicePointID_get_and_set( DailyStatistic $ds ){
        $servicePointID = 22;
        $ds->setServicePointId( $servicePointID );

        $testServicePointID = $ds->getServicePointID();
        $this->assertEquals( $servicePointID, $testServicePointID );
    }

    /**
     * @depends construct_without_arguments_should_work
     * @test Service Point Name getter and setter
     */
    public function test_servicePointName_get_and_set( DailyStatistic $ds ){
        $servicePointName = "Meh";
        $ds->setServicePointName( $servicePointName );

        $testServicePointName = $ds->getServicePointName();
        $this->assertEquals( $servicePointName, $testServicePointName );
    }

}

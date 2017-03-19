<?php
/**
 * Class for Daily Statistic objects.
 *
 * Whenever a daily statistic is to be recorded it is stored in a DailyStatistic
 * object. From there a call to the record() method saves the statistic to
 * the database.
 */

class DailyStatistic {
    /**
     * @var int $statTypeId ID of statistic type pulled from stat_types table.
     */
    private $statTypeId;

    /**
     * @var int $servicePointId ID of service point pulled from service_points
     *  table.
     */
    private $servicePointId;

    /**
     * @var string $branchName Name of branch. Used to verify that service point
     *  hasn't changed since stat applicaton was loaded.
     */
    private $branchName;

    /**
     * @var string $servicePointName Name of service point. Used to verify that
     *  service point hasn't changed since stat application was loaded.
     */
    private $servicePointName;


    public function __construct( array $args ) {
        foreach( $args as $key => $value  ){
            switch ( strtolower( $key ) ) {
                case 'branchname':
                    $this->setBranchName( $value );
                    break;

                case 'stattypeid':
                    $this->setStatTypeId( $value );
                    break;

                case 'servicepointid':
                    $this->setServicePointId( $value );
                    break;

                case 'servicepointname':
                    $this->setServicePointName( $value );
                    break;
            }
        }
    }


    public function getStatTypeId(){ return $this->statTypeId; }
    public function getServicePointId(){ return $this->servicePointId; }
    public function getBranchName(){ return $this->branchName; }
    public function getServicePointName(){ return $this->servicePointName; }

    public function setStatTypeId( $statTypeId ){ $this->statTypeId = $statTypeId; }
    public function setServicePointId( $servicePointId ){ $this->servicePointId = $servicePointId; }
    public function setBranchName( $branchName ){ $this->branchName = $branchName; }
    public function setServicePointName($servicePointName ){ $this->servicePointName = $servicePointName; }

}

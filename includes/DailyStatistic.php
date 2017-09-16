<?php
/**
 * Class for Daily Statistic objects.
 *
 * Whenever a daily statistic is to be recorded it is stored in a DailyStatistic
 * object. From there a call to the record() method saves the statistic to
 * the database.
 */

require_once __DIR__ . '/../LocalSettings.php';

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


    public function __construct( array $args = [] ) {
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


    /**
     * Record statistic to the daily_stats table.
     *
     * @return [int|bool] Returns record ID of inserted row on success or false
     *  on a failed insert.
     */
    public function record() {
        $connectionString =
            "mysql:host=" . LocalSettings::$dbHost . ";" .
            "port=" . LocalSettings::$dbPort . ";" .
            "dbname=" . LocalSettings::$dbName;

        try {
            $conn = new PDO( $connectionString, LocalSettings::$dbUser, LocalSettings::$dbPw );
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch ( PDOException $e ) {
            // @todo Handle this better.
            return false;
        }

        $sql = "INSERT INTO stat_daily_stats ( dst_id, sp_id ) VALUES ( :dstId, :spId )";
        $statement = $conn->prepare($sql);

        $goForExecute = true;

        $goForExecute = $goForExecute && $statement->bindParam( ':dstId', $this->statTypeId, PDO::PARAM_INT );
        $goForExecute = $goForExecute && $statement->bindParam( ':spId', $this->servicePointId, PDO::PARAM_INT );

        $return = array();
        if ( $goForExecute ) {
            try {
                $conn->beginTransaction();
                $result = $statement->execute();
                if ( $result ) {
                    $return[] = [ 'dst_id' => $conn->lastInsertId() ];
                } else {
                    $return = false;
                }
                $conn->commit();
            } catch ( PDOException $e ) {
                $conn->rollback();
                $return = false;
            }
        } else {
            $return = false;
        }

        return $return;
    }
}

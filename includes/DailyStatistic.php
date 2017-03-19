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
}

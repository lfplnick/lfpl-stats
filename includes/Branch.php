<?php
/**
 * Class for Daily Statistic objects.
 *
 * Whenever a daily statistic is to be recorded it is stored in a DailyStatistic
 * object. From there a call to the record() method saves the statistic to
 * the database.
 */

require_once __DIR__ . '/../LocalSettings.php';

class Branch {
    /**
     * @var int $branchId Record ID of branch in stat_branches table
     */
    private $branchId = null;

    /**
     * @var string $branchName
     */
    private $branchName = null;

    /**
     * @var string $branchAbbr Branch abbreviation
     */
    private $branchAbbr = null;

    /**
     * @var boolean $branchEnabled Whether or not branch is operational
     */
    private $branchEnabled = false;

    /**
     *@var boolean $branchExists Whether or not branch exists in database
     */
    private $branchExists = null;

    /**
     * @var string $error Stores error message
     */
    private $error = '';


    public function setId( $id ){ $this->branchId = $id; return $this; }
    public function getId(){return $this->branchId;}

    public function setName( $name ){ $this->branchName = $name; return $this; }
    public function getName(){ return $this->branchName; }


    public function setAbbr( $abbr ){ $this->branchAbbr = $abbr; return $this; }
    public function getAbbr(){ return $this->branchAbbr; }

    public function enabled(){ return $this->branchEnabled; }
    public function disabled(){ return !$this->branchEnabled; }

    public function setEnabled(){ $this->branchEnabled = true; return $this; }
    public function setDisabled(){ $this->branchEnabled = false; return $this; }

    public function getErrorMessage(){ return $this->error; }

    public function exists()
    {
        if( !isset( $this->branchExists ) )
        {
            $this->search();
        }

        return $this->branchExists;
    }

    public function doesNotExist()
    {
        if( !isset( $this->branchExists ) )
        {
            $this->search();
        }

        return !$this->branchExists;
    }


    /**
     * Adds branch to database if branch does not already exist.
     *
     * @return [Branch|bool]
     *  If branch is successfully created then $this is returned, otherwise
     *  false is returned and $this->error is set.
     */
    public function create()
    {
        $searchBranch = new Branch;
        $searchBranch
            ->setName( $this->branchName )
            ->search()
        ;

        // likely an internal error, either way don't create the branch
        if( $searchBranch === false )
        {
            $this->error = "Error creating branch.";
            return false;
        }

        // branch exists, don't create the branch
        if( $searchBranch->exists() )
        {
            $this->error = "Branch already exists.";
            return false;
        }

        // branch name is required to create branch
        if( null === $this->branchName )
        {
            $this->error = "Not enough information to create new branch.";
            return false;
        }

        // can't create branch if we can't connect to database
        $conn = Connection::getConnection();
        if( $conn === false )
        {
            $this->error = "Error creating branch.";
            return false;
        }


        $insert = 'INSERT INTO `stat_branches` ( branches_name';
        $values = 'VALUES ( :branchName';

        if( null !== $this->getAbbr() )
        {
            $insert .= ', branches_abbr';
            $values .= ', :branchAbbr';
        }

        if( true === $this->disabled() )
        {
            $insert .= ', branches_enabled';
            $values .= ', 0';
        }

        $sql = $insert . ' ) ' . $values . ' );';

        $sth = $conn->prepare( $sql );

        $goForExecute = $sth->bindValue(
            ':branchName',
            $this->branchName,
            PDO::PARAM_STR
        );

        if( null !== $this->getAbbr() )
        {
            $goForExecute = $goForExecute &
                $sth->bindValue(
                    ':branchAbbr',
                    $this->branchAbbr,
                    PDO::PARAM_STR
                );
        }

        // values didn't bind correctly
        if( $goForExecute === false )
        {
            $this->error = "Error creating branch.";
            return false;
        }

        // statement failed to execute
        if( $sth->execute() === false )
        {
            $this->error = "Error creating branch.";
            return false;
        }

        // can't verify that branch was created
        $this->search();
        if( $this->branchExists === false )
        {
            $this->error = "Error creating branch.";
            return false;
        }

        return $this;
    }//create

    /**
     * Searches for branch. Note that wildcards are not allowed and that all set
     *  properties will be used in the search. So if branchName and branchId are
     *  both set then they will both be used in the search.
     *
     *  This may lead to unexpected results if you are searching for a branch
     *  that has a name of 'Iroquois' or an abbreviation of '18' and try to do
     *  it with one search. This would need to be done with two calls to
     *  search(), one where name is null and the next where abbreviation is
     *  null.
     * @return [Branch|bool]
     *  If search is successful then calling Branch object is returned,
     *  otherwise false is returned and $this->error is set.
     *
     *  If branch is found then calling Branch object's id, name,
     *  abbreviation, and enabled status are set and branchExists is set to
     *  true.
     *
     *  If branch is not found then calling Branch object is returned with
     *  branchExists set to false.
     */
    public function search()
    {
        $where = 'WHERE ';
        if ( isset( $this->branchId ) )
        {
            $where .= '( `branches_id` = :id )';
        }

        if ( isset( $this->branchName ) )
        {
            if ( strlen( $where ) > 6 )
            {
                $where .= ' AND ';
            }
            $where .= '( `branches_name` = :name )';
        }

        if ( isset( $this->branchAbbr ) )
        {
            if (strlen( $where ) > 6 )
            {
                $where .= ' AND ';
            }
            $where .= '( `branches_abbr` = :abbr )';
        }

        if ( strlen( $where ) <= 6 )
        {
            $this->error = "Invalid search parameters.";
            return false;
        }

        $select = <<<SQL
            SELECT
             `branches_id`,
             `branches_name`,
             `branches_abbr`,
             `branches_enabled`
            FROM `stat_branches`
SQL;
        $sql = $select . $where . ';';

        $conn = Connection::getConnection();
        if( $conn === false )
        {
            $this->error = "Unable to connect to database.";
            return false;
        }
        $statement = $conn->prepare( $sql );

        $goForExecute = true;
        if ( isset( $this->branchId ) )
        {
            $goForExecute =
                $goForExecute &&
                $statement->bindValue(
                    ':id',
                    $this->branchId,
                    PDO::PARAM_INT
                )
            ;
        }

        if ( isset( $this->branchName ) )
        {
            $goForExecute =
                $goForExecute &&
                $statement->bindValue(
                    ':name',
                    $this->branchName,
                    PDO::PARAM_STR
                )
            ;
        }

        if ( isset( $this->branchAbbr ) )
        {
            $goForExecute =
                $goForExecute &&
                $statement->bindValue(
                    ':abbr',
                    $this->branchAbbr,
                    PDO::PARAM_STR
                )
            ;
        }

        $info = false;
        if ( $goForExecute && $statement->execute() )
        {
            $records = $statement->fetchAll( PDO::FETCH_ASSOC );
            if ( count( $records) === 1 )
            {
                $info = $records;
            }
        }

        if( $info !== false )
        {
            $info = $info[0];
            $this->setName( $info['branches_name'] );
            $this->setId( $info['branches_id'] );
            $this->setAbbr( $info['branches_abbr'] );
            if( $info['branches_enabled'] === '1' )
            {
                $this->setEnabled();
            } else {
                $this->setDisabled();
            }
            $this->branchExists = true;
        } else {// $info === false
            $this->branchExists = false;
        }


        $statement = null;
        return $this;
    }// search
}

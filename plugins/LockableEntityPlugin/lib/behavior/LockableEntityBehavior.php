<?php

class LockableEntityBehavior extends Behavior {

    // default parameters value
    protected $parameters = array(
        'locked_by_column' => 'locked_by',
        'locked_to_column' => 'locked_to'
    );

    public function modifyTable() {
        $this->addColumnIfNotExists($this->getTable(), $this->getParameter('locked_by_column'), 'VARCHAR', 255);
        $this->addColumnIfNotExists($this->getTable(), $this->getParameter('locked_to_column'), 'TIMESTAMP');
    }    
    
    /**
     *  Add column to current table if one does not exist yet
     * 
     * @param Table $table
     * @param string $columnName
     * @param string $propelType
     * @param int $size 
     */
    protected function addColumnIfNotExists(Table $table, $columnName, $propelType, $size = null) {
        if (!$table->containsColumn($columnName)) {
            $table->addColumn(array(
                'name' => $columnName,
                'type' => $propelType,
                'size' => $size
            ));
        }
    }
    
    /**
     * Get the setter of one of the columns of the behavior
     *
     * @param     string $column One of the behavior colums, 'locked_by_column' or 'locked_to_column'
     * @return    string The related setter, 'setLockedBy' or 'setLockedTo'
     */
    protected function getColumnSetter($column) {
        return 'set' . $this->getColumnForParameter($column)->getPhpName();
    }
    
    /**
     * Get the getter of one of the columns of the behavior
     *
     * @param     string $column One of the behavior colums, 'locked_by_column' or 'locked_to_column'
     * @return    string The related setter, 'getLockedBy' or 'getLockedTo'
     */
    protected function getColumnGetter($column) {
        return 'get' . $this->getColumnForParameter($column)->getPhpName();
    }

    /**
     *  Returns methods to add to object.
     * 
     * @return String
     */
    public function objectMethods() {
        $lockedBySetter = $this->getColumnSetter('locked_by_column');
        $lockedByGetter = $this->getColumnGetter('locked_by_column');
        $lockedToSetter = $this->getColumnSetter('locked_to_column');
        $lockedToGetter = $this->getColumnGetter('locked_to_column');
        
        $modelName = $this->getTable()->getPhpName();
        
        return <<<PHP
/**     
 *  Checks if given user can acquire lock for this $modelName.
 *  This is true if $modelName is not locked, it is locked on given user or current lock on this $modelName has expired
 * 
 * @param string \$user Login of user to test
 */
public function isLockableBy(\$user)
{
    return !\$this->$lockedByGetter()                  //not locked
         || \$this->$lockedByGetter() === \$user        //or locked on me
         || \$this->$lockedToGetter('U') < date('U');  //or lock has expired
}
    
/**
 *  Returns true if $modelName is locked for given user. 
 * 
 * @return type 
 */
public function isLocked(\$user)
{
    return !\$this->isLockableBy(\$user);
}
    
/**
 *  Locks $modelName on given user. Warning: this method must be wrapped in transaction
 *   to avoid race conditions.
 * 
 *  Will fail if this $modelName is not lockable by \$user
 * 
 * @param string \$user Login of user to test
 * @return true on success, false on failure
 */
protected function doLockTo(\$user)
{
    if (\$this->isLockableBy(\$user)) {
        \$this->$lockedBySetter(\$user);
        \$this->$lockedToSetter(date('U') + 15*60);
        return true;
    }

    return false;
}
    
/**
 *  Locks $modelName on given user. This method uses MySQL transactions to eliminate
 *   race conditions.
 * 
 * @param string \$user 
 */
public function lockTo(\$user)
{                       
    //start transaction for SELECT ... FOR UPDATE to work
    \$con = Propel::getConnection({$modelName}Peer::DATABASE_NAME);
    \$con->beginTransaction();

    try {
        //will make us retrieve fresh copy of record from db
        {$modelName}Peer::removeInstanceFromPool(\$this);

        \$entity = {$modelName}Query::create()
                    //set for update
                    ->filterByPrimaryKey(\$this->getPrimaryKey())
                    ->findOne();
        \$result = \$entity->doLockTo(\$user);
        \$entity->save();

        \$con->commit();            
    } catch (Exception \$e) {
        \$con->rollBack();
        throw \$e;
    }

    return \$result;
}   
//end LockableEntityBehavior
PHP;
    }
}

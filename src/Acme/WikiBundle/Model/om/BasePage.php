<?php

namespace Acme\WikiBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Acme\WikiBundle\Model\Page;
use Acme\WikiBundle\Model\PagePeer;
use Acme\WikiBundle\Model\PageQuery;
use Symfony\Component\Validator\Constraints as Assert;

abstract class BasePage extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Acme\\WikiBundle\\Model\\PagePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PagePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the parent_id field.
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the parentpath field.
     * @var        string
     */
    protected $parentpath;

    /**
     * The value for the pagename field.
     * 
     * @var        string
     */
    protected $pagename;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the text field.
     * @var        string
     */
    protected $text;

    /**
     * Whether the lazy-loaded $text value has been loaded from database.
     * This is necessary to avoid repeated lookups if $text column is null in the db.
     * @var        boolean
     */
    protected $text_isLoaded = false;

    /**
     * @var        Page
     */
    protected $aParent;

    /**
     * @var        PropelObjectCollection|Page[] Collection to store aggregation of Page objects.
     */
    protected $collChildren_refs;
    protected $collChildren_refsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $children_refsScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {

        return $this->parent_id;
    }

    /**
     * Get the [parentpath] column value.
     *
     * @return string
     */
    public function getParentpath()
    {

        return $this->parentpath;
    }

    /**
     * Get the [pagename] column value.
     *
     * @return string
     */
    public function getPagename()
    {

        return $this->pagename;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [text] column value.
     *
     * @param PropelPDO $con An optional PropelPDO connection to use for fetching this lazy-loaded column.
     * @return string
     */
    public function getText(PropelPDO $con = null)
    {
        if (!$this->text_isLoaded && $this->text === null && !$this->isNew()) {
            $this->loadText($con);
        }


        return $this->text;
    }

    /**
     * Load the value for the lazy-loaded [text] column.
     *
     * This method performs an additional query to return the value for
     * the [text] column, since it is not populated by
     * the hydrate() method.
     *
     * @param  PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - any underlying error will be wrapped and re-thrown.
     */
    protected function loadText(PropelPDO $con = null)
    {
        $c = $this->buildPkeyCriteria();
        $c->addSelectColumn(PagePeer::TEXT);
        try {
            $stmt = PagePeer::doSelectStmt($c, $con);
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $stmt->closeCursor();
            $this->text = ($row[0] !== null) ? (string) $row[0] : null;
            $this->text_isLoaded = true;
        } catch (Exception $e) {
            throw new PropelException("Error loading value for [text] column on demand.", $e);
        }
    }
    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Page The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PagePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param  int $v new value
     * @return Page The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[] = PagePeer::PARENT_ID;
        }

        if ($this->aParent !== null && $this->aParent->getId() !== $v) {
            $this->aParent = null;
        }


        return $this;
    } // setParentId()

    /**
     * Set the value of [parentpath] column.
     *
     * @param  string $v new value
     * @return Page The current object (for fluent API support)
     */
    public function setParentpath($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->parentpath !== $v) {
            $this->parentpath = $v;
            $this->modifiedColumns[] = PagePeer::PARENTPATH;
        }


        return $this;
    } // setParentpath()

    /**
     * Set the value of [pagename] column.
     *
     * @param  string $v new value
     * @return Page The current object (for fluent API support)
     */
    public function setPagename($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->pagename !== $v) {
            $this->pagename = $v;
            $this->modifiedColumns[] = PagePeer::PAGENAME;
        }


        return $this;
    } // setPagename()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Page The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = PagePeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [text] column.
     *
     * @param  string $v new value
     * @return Page The current object (for fluent API support)
     */
    public function setText($v)
    {
        // Allow unsetting the lazy loaded column even when its not loaded.
        if (!$this->text_isLoaded && $v === null) {
            $this->modifiedColumns[] = PagePeer::TEXT;
        }

        // explicitly set the is-loaded flag to true for this lazy load col;
        // it doesn't matter if the value is actually set or not (logic below) as
        // any attempt to set the value means that no db lookup should be performed
        // when the getText() method is called.
        $this->text_isLoaded = true;

        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->text !== $v) {
            $this->text = $v;
            $this->modifiedColumns[] = PagePeer::TEXT;
        }


        return $this;
    } // setText()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->parentpath = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->pagename = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->title = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = PagePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Page object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aParent !== null && $this->parent_id !== $this->aParent->getId()) {
            $this->aParent = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PagePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        // Reset the text lazy-load column
        $this->text = null;
        $this->text_isLoaded = false;

        if ($deep) {  // also de-associate any related objects?

            $this->aParent = null;
            $this->collChildren_refs = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PageQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                PagePeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aParent !== null) {
                if ($this->aParent->isModified() || $this->aParent->isNew()) {
                    $affectedRows += $this->aParent->save($con);
                }
                $this->setParent($this->aParent);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->children_refsScheduledForDeletion !== null) {
                if (!$this->children_refsScheduledForDeletion->isEmpty()) {
                    PageQuery::create()
                        ->filterByPrimaryKeys($this->children_refsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->children_refsScheduledForDeletion = null;
                }
            }

            if ($this->collChildren_refs !== null) {
                foreach ($this->collChildren_refs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = PagePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PagePeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PagePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(PagePeer::PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`parent_id`';
        }
        if ($this->isColumnModified(PagePeer::PARENTPATH)) {
            $modifiedColumns[':p' . $index++]  = '`parentPath`';
        }
        if ($this->isColumnModified(PagePeer::PAGENAME)) {
            $modifiedColumns[':p' . $index++]  = '`pageName`';
        }
        if ($this->isColumnModified(PagePeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(PagePeer::TEXT)) {
            $modifiedColumns[':p' . $index++]  = '`text`';
        }

        $sql = sprintf(
            'INSERT INTO `page` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`parent_id`':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case '`parentPath`':
                        $stmt->bindValue($identifier, $this->parentpath, PDO::PARAM_STR);
                        break;
                    case '`pageName`':
                        $stmt->bindValue($identifier, $this->pagename, PDO::PARAM_STR);
                        break;
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`text`':
                        $stmt->bindValue($identifier, $this->text, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aParent !== null) {
                if (!$this->aParent->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aParent->getValidationFailures());
                }
            }


            if (($retval = PagePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collChildren_refs !== null) {
                    foreach ($this->collChildren_refs as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = PagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getParentId();
                break;
            case 2:
                return $this->getParentpath();
                break;
            case 3:
                return $this->getPagename();
                break;
            case 4:
                return $this->getTitle();
                break;
            case 5:
                return $this->getText();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Page'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Page'][$this->getPrimaryKey()] = true;
        $keys = PagePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getParentId(),
            $keys[2] => $this->getParentpath(),
            $keys[3] => $this->getPagename(),
            $keys[4] => $this->getTitle(),
            $keys[5] => ($includeLazyLoadColumns) ? $this->getText() : null,
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aParent) {
                $result['Parent'] = $this->aParent->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collChildren_refs) {
                $result['Children_refs'] = $this->collChildren_refs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = PagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setParentId($value);
                break;
            case 2:
                $this->setParentpath($value);
                break;
            case 3:
                $this->setPagename($value);
                break;
            case 4:
                $this->setTitle($value);
                break;
            case 5:
                $this->setText($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = PagePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setParentpath($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPagename($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setTitle($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setText($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PagePeer::DATABASE_NAME);

        if ($this->isColumnModified(PagePeer::ID)) $criteria->add(PagePeer::ID, $this->id);
        if ($this->isColumnModified(PagePeer::PARENT_ID)) $criteria->add(PagePeer::PARENT_ID, $this->parent_id);
        if ($this->isColumnModified(PagePeer::PARENTPATH)) $criteria->add(PagePeer::PARENTPATH, $this->parentpath);
        if ($this->isColumnModified(PagePeer::PAGENAME)) $criteria->add(PagePeer::PAGENAME, $this->pagename);
        if ($this->isColumnModified(PagePeer::TITLE)) $criteria->add(PagePeer::TITLE, $this->title);
        if ($this->isColumnModified(PagePeer::TEXT)) $criteria->add(PagePeer::TEXT, $this->text);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(PagePeer::DATABASE_NAME);
        $criteria->add(PagePeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Page (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentId($this->getParentId());
        $copyObj->setParentpath($this->getParentpath());
        $copyObj->setPagename($this->getPagename());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setText($this->getText());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getChildren_refs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addChildren_ref($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Page Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return PagePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PagePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Page object.
     *
     * @param                  Page $v
     * @return Page The current object (for fluent API support)
     * @throws PropelException
     */
    public function setParent(Page $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aParent = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Page object, it will not be re-added.
        if ($v !== null) {
            $v->addChildren_ref($this);
        }


        return $this;
    }


    /**
     * Get the associated Page object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Page The associated Page object.
     * @throws PropelException
     */
    public function getParent(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aParent === null && ($this->parent_id !== null) && $doQuery) {
            $this->aParent = PageQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aParent->addChildren_refs($this);
             */
        }

        return $this->aParent;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Children_ref' == $relationName) {
            $this->initChildren_refs();
        }
    }

    /**
     * Clears out the collChildren_refs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Page The current object (for fluent API support)
     * @see        addChildren_refs()
     */
    public function clearChildren_refs()
    {
        $this->collChildren_refs = null; // important to set this to null since that means it is uninitialized
        $this->collChildren_refsPartial = null;

        return $this;
    }

    /**
     * reset is the collChildren_refs collection loaded partially
     *
     * @return void
     */
    public function resetPartialChildren_refs($v = true)
    {
        $this->collChildren_refsPartial = $v;
    }

    /**
     * Initializes the collChildren_refs collection.
     *
     * By default this just sets the collChildren_refs collection to an empty array (like clearcollChildren_refs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initChildren_refs($overrideExisting = true)
    {
        if (null !== $this->collChildren_refs && !$overrideExisting) {
            return;
        }
        $this->collChildren_refs = new PropelObjectCollection();
        $this->collChildren_refs->setModel('Page');
    }

    /**
     * Gets an array of Page objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Page is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Page[] List of Page objects
     * @throws PropelException
     */
    public function getChildren_refs($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collChildren_refsPartial && !$this->isNew();
        if (null === $this->collChildren_refs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collChildren_refs) {
                // return empty collection
                $this->initChildren_refs();
            } else {
                $collChildren_refs = PageQuery::create(null, $criteria)
                    ->filterByParent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collChildren_refsPartial && count($collChildren_refs)) {
                      $this->initChildren_refs(false);

                      foreach ($collChildren_refs as $obj) {
                        if (false == $this->collChildren_refs->contains($obj)) {
                          $this->collChildren_refs->append($obj);
                        }
                      }

                      $this->collChildren_refsPartial = true;
                    }

                    $collChildren_refs->getInternalIterator()->rewind();

                    return $collChildren_refs;
                }

                if ($partial && $this->collChildren_refs) {
                    foreach ($this->collChildren_refs as $obj) {
                        if ($obj->isNew()) {
                            $collChildren_refs[] = $obj;
                        }
                    }
                }

                $this->collChildren_refs = $collChildren_refs;
                $this->collChildren_refsPartial = false;
            }
        }

        return $this->collChildren_refs;
    }

    /**
     * Sets a collection of Children_ref objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $children_refs A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Page The current object (for fluent API support)
     */
    public function setChildren_refs(PropelCollection $children_refs, PropelPDO $con = null)
    {
        $children_refsToDelete = $this->getChildren_refs(new Criteria(), $con)->diff($children_refs);


        $this->children_refsScheduledForDeletion = $children_refsToDelete;

        foreach ($children_refsToDelete as $children_refRemoved) {
            $children_refRemoved->setParent(null);
        }

        $this->collChildren_refs = null;
        foreach ($children_refs as $children_ref) {
            $this->addChildren_ref($children_ref);
        }

        $this->collChildren_refs = $children_refs;
        $this->collChildren_refsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Page objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Page objects.
     * @throws PropelException
     */
    public function countChildren_refs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collChildren_refsPartial && !$this->isNew();
        if (null === $this->collChildren_refs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collChildren_refs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getChildren_refs());
            }
            $query = PageQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByParent($this)
                ->count($con);
        }

        return count($this->collChildren_refs);
    }

    /**
     * Method called to associate a Page object to this object
     * through the Page foreign key attribute.
     *
     * @param    Page $l Page
     * @return Page The current object (for fluent API support)
     */
    public function addChildren_ref(Page $l)
    {
        if ($this->collChildren_refs === null) {
            $this->initChildren_refs();
            $this->collChildren_refsPartial = true;
        }

        if (!in_array($l, $this->collChildren_refs->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddChildren_ref($l);

            if ($this->children_refsScheduledForDeletion and $this->children_refsScheduledForDeletion->contains($l)) {
                $this->children_refsScheduledForDeletion->remove($this->children_refsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Children_ref $children_ref The children_ref object to add.
     */
    protected function doAddChildren_ref($children_ref)
    {
        $this->collChildren_refs[]= $children_ref;
        $children_ref->setParent($this);
    }

    /**
     * @param	Children_ref $children_ref The children_ref object to remove.
     * @return Page The current object (for fluent API support)
     */
    public function removeChildren_ref($children_ref)
    {
        if ($this->getChildren_refs()->contains($children_ref)) {
            $this->collChildren_refs->remove($this->collChildren_refs->search($children_ref));
            if (null === $this->children_refsScheduledForDeletion) {
                $this->children_refsScheduledForDeletion = clone $this->collChildren_refs;
                $this->children_refsScheduledForDeletion->clear();
            }
            $this->children_refsScheduledForDeletion[]= $children_ref;
            $children_ref->setParent(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->parent_id = null;
        $this->parentpath = null;
        $this->pagename = null;
        $this->title = null;
        $this->text = null;
        $this->text_isLoaded = false;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collChildren_refs) {
                foreach ($this->collChildren_refs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aParent instanceof Persistent) {
              $this->aParent->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collChildren_refs instanceof PropelCollection) {
            $this->collChildren_refs->clearIterator();
        }
        $this->collChildren_refs = null;
        $this->aParent = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string The value of the 'parentPath' column
     */
    public function __toString()
    {
        return (string) $this->getParentpath();
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}

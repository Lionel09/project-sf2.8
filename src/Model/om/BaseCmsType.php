<?php

namespace Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Model\CmsContent;
use Model\CmsContentHasTypes;
use Model\CmsContentHasTypesQuery;
use Model\CmsContentQuery;
use Model\CmsType;
use Model\CmsTypePeer;
use Model\CmsTypeQuery;

abstract class BaseCmsType extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Model\\CmsTypePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CmsTypePeer
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
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        PropelObjectCollection|CmsContentHasTypes[] Collection to store aggregation of CmsContentHasTypes objects.
     */
    protected $collCmsContentHasTypess;
    protected $collCmsContentHasTypessPartial;

    /**
     * @var        PropelObjectCollection|CmsContent[] Collection to store aggregation of CmsContent objects.
     */
    protected $collCmsContents;

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
    protected $cmsContentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $cmsContentHasTypessScheduledForDeletion = null;

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
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = null)
    {
        if ($this->created_at === null) {
            return null;
        }

        if ($this->created_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = null)
    {
        if ($this->updated_at === null) {
            return null;
        }

        if ($this->updated_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return CmsType The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CmsTypePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return CmsType The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = CmsTypePeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CmsType The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = CmsTypePeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CmsType The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = CmsTypePeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

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
            $this->title = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->created_at = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->updated_at = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 4; // 4 = CmsTypePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CmsType object", $e);
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
            $con = Propel::getConnection(CmsTypePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CmsTypePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCmsContentHasTypess = null;

            $this->collCmsContents = null;
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
            $con = Propel::getConnection(CmsTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CmsTypeQuery::create()
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
            $con = Propel::getConnection(CmsTypePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CmsTypePeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CmsTypePeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CmsTypePeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CmsTypePeer::addInstanceToPool($this);
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

            if ($this->cmsContentsScheduledForDeletion !== null) {
                if (!$this->cmsContentsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->cmsContentsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    CmsContentHasTypesQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->cmsContentsScheduledForDeletion = null;
                }

                foreach ($this->getCmsContents() as $cmsContent) {
                    if ($cmsContent->isModified()) {
                        $cmsContent->save($con);
                    }
                }
            } elseif ($this->collCmsContents) {
                foreach ($this->collCmsContents as $cmsContent) {
                    if ($cmsContent->isModified()) {
                        $cmsContent->save($con);
                    }
                }
            }

            if ($this->cmsContentHasTypessScheduledForDeletion !== null) {
                if (!$this->cmsContentHasTypessScheduledForDeletion->isEmpty()) {
                    CmsContentHasTypesQuery::create()
                        ->filterByPrimaryKeys($this->cmsContentHasTypessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cmsContentHasTypessScheduledForDeletion = null;
                }
            }

            if ($this->collCmsContentHasTypess !== null) {
                foreach ($this->collCmsContentHasTypess as $referrerFK) {
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

        $this->modifiedColumns[] = CmsTypePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CmsTypePeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CmsTypePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CmsTypePeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(CmsTypePeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CmsTypePeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `cms_type` (%s) VALUES (%s)',
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
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
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
        $pos = CmsTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTitle();
                break;
            case 2:
                return $this->getCreatedAt();
                break;
            case 3:
                return $this->getUpdatedAt();
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
        if (isset($alreadyDumpedObjects['CmsType'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CmsType'][$this->getPrimaryKey()] = true;
        $keys = CmsTypePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getCreatedAt(),
            $keys[3] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCmsContentHasTypess) {
                $result['CmsContentHasTypess'] = $this->collCmsContentHasTypess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CmsTypePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTitle($value);
                break;
            case 2:
                $this->setCreatedAt($value);
                break;
            case 3:
                $this->setUpdatedAt($value);
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
        $keys = CmsTypePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTitle($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCreatedAt($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setUpdatedAt($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CmsTypePeer::DATABASE_NAME);

        if ($this->isColumnModified(CmsTypePeer::ID)) $criteria->add(CmsTypePeer::ID, $this->id);
        if ($this->isColumnModified(CmsTypePeer::TITLE)) $criteria->add(CmsTypePeer::TITLE, $this->title);
        if ($this->isColumnModified(CmsTypePeer::CREATED_AT)) $criteria->add(CmsTypePeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CmsTypePeer::UPDATED_AT)) $criteria->add(CmsTypePeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CmsTypePeer::DATABASE_NAME);
        $criteria->add(CmsTypePeer::ID, $this->id);

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
     * @param object $copyObj An object of CmsType (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCmsContentHasTypess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCmsContentHasTypes($relObj->copy($deepCopy));
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
     * @return CmsType Clone of current object.
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
     * @return CmsTypePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CmsTypePeer();
        }

        return self::$peer;
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
        if ('CmsContentHasTypes' == $relationName) {
            $this->initCmsContentHasTypess();
        }
    }

    /**
     * Clears out the collCmsContentHasTypess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CmsType The current object (for fluent API support)
     * @see        addCmsContentHasTypess()
     */
    public function clearCmsContentHasTypess()
    {
        $this->collCmsContentHasTypess = null; // important to set this to null since that means it is uninitialized
        $this->collCmsContentHasTypessPartial = null;

        return $this;
    }

    /**
     * reset is the collCmsContentHasTypess collection loaded partially
     *
     * @return void
     */
    public function resetPartialCmsContentHasTypess($v = true)
    {
        $this->collCmsContentHasTypessPartial = $v;
    }

    /**
     * Initializes the collCmsContentHasTypess collection.
     *
     * By default this just sets the collCmsContentHasTypess collection to an empty array (like clearcollCmsContentHasTypess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCmsContentHasTypess($overrideExisting = true)
    {
        if (null !== $this->collCmsContentHasTypess && !$overrideExisting) {
            return;
        }
        $this->collCmsContentHasTypess = new PropelObjectCollection();
        $this->collCmsContentHasTypess->setModel('CmsContentHasTypes');
    }

    /**
     * Gets an array of CmsContentHasTypes objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CmsType is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CmsContentHasTypes[] List of CmsContentHasTypes objects
     * @throws PropelException
     */
    public function getCmsContentHasTypess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCmsContentHasTypessPartial && !$this->isNew();
        if (null === $this->collCmsContentHasTypess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCmsContentHasTypess) {
                // return empty collection
                $this->initCmsContentHasTypess();
            } else {
                $collCmsContentHasTypess = CmsContentHasTypesQuery::create(null, $criteria)
                    ->filterByCmsType($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCmsContentHasTypessPartial && count($collCmsContentHasTypess)) {
                      $this->initCmsContentHasTypess(false);

                      foreach ($collCmsContentHasTypess as $obj) {
                        if (false == $this->collCmsContentHasTypess->contains($obj)) {
                          $this->collCmsContentHasTypess->append($obj);
                        }
                      }

                      $this->collCmsContentHasTypessPartial = true;
                    }

                    $collCmsContentHasTypess->getInternalIterator()->rewind();

                    return $collCmsContentHasTypess;
                }

                if ($partial && $this->collCmsContentHasTypess) {
                    foreach ($this->collCmsContentHasTypess as $obj) {
                        if ($obj->isNew()) {
                            $collCmsContentHasTypess[] = $obj;
                        }
                    }
                }

                $this->collCmsContentHasTypess = $collCmsContentHasTypess;
                $this->collCmsContentHasTypessPartial = false;
            }
        }

        return $this->collCmsContentHasTypess;
    }

    /**
     * Sets a collection of CmsContentHasTypes objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $cmsContentHasTypess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CmsType The current object (for fluent API support)
     */
    public function setCmsContentHasTypess(PropelCollection $cmsContentHasTypess, PropelPDO $con = null)
    {
        $cmsContentHasTypessToDelete = $this->getCmsContentHasTypess(new Criteria(), $con)->diff($cmsContentHasTypess);


        $this->cmsContentHasTypessScheduledForDeletion = $cmsContentHasTypessToDelete;

        foreach ($cmsContentHasTypessToDelete as $cmsContentHasTypesRemoved) {
            $cmsContentHasTypesRemoved->setCmsType(null);
        }

        $this->collCmsContentHasTypess = null;
        foreach ($cmsContentHasTypess as $cmsContentHasTypes) {
            $this->addCmsContentHasTypes($cmsContentHasTypes);
        }

        $this->collCmsContentHasTypess = $cmsContentHasTypess;
        $this->collCmsContentHasTypessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CmsContentHasTypes objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CmsContentHasTypes objects.
     * @throws PropelException
     */
    public function countCmsContentHasTypess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCmsContentHasTypessPartial && !$this->isNew();
        if (null === $this->collCmsContentHasTypess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCmsContentHasTypess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCmsContentHasTypess());
            }
            $query = CmsContentHasTypesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCmsType($this)
                ->count($con);
        }

        return count($this->collCmsContentHasTypess);
    }

    /**
     * Method called to associate a CmsContentHasTypes object to this object
     * through the CmsContentHasTypes foreign key attribute.
     *
     * @param    CmsContentHasTypes $l CmsContentHasTypes
     * @return CmsType The current object (for fluent API support)
     */
    public function addCmsContentHasTypes(CmsContentHasTypes $l)
    {
        if ($this->collCmsContentHasTypess === null) {
            $this->initCmsContentHasTypess();
            $this->collCmsContentHasTypessPartial = true;
        }

        if (!in_array($l, $this->collCmsContentHasTypess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCmsContentHasTypes($l);

            if ($this->cmsContentHasTypessScheduledForDeletion and $this->cmsContentHasTypessScheduledForDeletion->contains($l)) {
                $this->cmsContentHasTypessScheduledForDeletion->remove($this->cmsContentHasTypessScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CmsContentHasTypes $cmsContentHasTypes The cmsContentHasTypes object to add.
     */
    protected function doAddCmsContentHasTypes($cmsContentHasTypes)
    {
        $this->collCmsContentHasTypess[]= $cmsContentHasTypes;
        $cmsContentHasTypes->setCmsType($this);
    }

    /**
     * @param	CmsContentHasTypes $cmsContentHasTypes The cmsContentHasTypes object to remove.
     * @return CmsType The current object (for fluent API support)
     */
    public function removeCmsContentHasTypes($cmsContentHasTypes)
    {
        if ($this->getCmsContentHasTypess()->contains($cmsContentHasTypes)) {
            $this->collCmsContentHasTypess->remove($this->collCmsContentHasTypess->search($cmsContentHasTypes));
            if (null === $this->cmsContentHasTypessScheduledForDeletion) {
                $this->cmsContentHasTypessScheduledForDeletion = clone $this->collCmsContentHasTypess;
                $this->cmsContentHasTypessScheduledForDeletion->clear();
            }
            $this->cmsContentHasTypessScheduledForDeletion[]= clone $cmsContentHasTypes;
            $cmsContentHasTypes->setCmsType(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CmsType is new, it will return
     * an empty collection; or if this CmsType has previously
     * been saved, it will retrieve related CmsContentHasTypess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CmsType.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CmsContentHasTypes[] List of CmsContentHasTypes objects
     */
    public function getCmsContentHasTypessJoinCmsContent($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CmsContentHasTypesQuery::create(null, $criteria);
        $query->joinWith('CmsContent', $join_behavior);

        return $this->getCmsContentHasTypess($query, $con);
    }

    /**
     * Clears out the collCmsContents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CmsType The current object (for fluent API support)
     * @see        addCmsContents()
     */
    public function clearCmsContents()
    {
        $this->collCmsContents = null; // important to set this to null since that means it is uninitialized
        $this->collCmsContentsPartial = null;

        return $this;
    }

    /**
     * Initializes the collCmsContents collection.
     *
     * By default this just sets the collCmsContents collection to an empty collection (like clearCmsContents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initCmsContents()
    {
        $this->collCmsContents = new PropelObjectCollection();
        $this->collCmsContents->setModel('CmsContent');
    }

    /**
     * Gets a collection of CmsContent objects related by a many-to-many relationship
     * to the current object by way of the cms_content_has_types cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CmsType is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|CmsContent[] List of CmsContent objects
     */
    public function getCmsContents($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collCmsContents || null !== $criteria) {
            if ($this->isNew() && null === $this->collCmsContents) {
                // return empty collection
                $this->initCmsContents();
            } else {
                $collCmsContents = CmsContentQuery::create(null, $criteria)
                    ->filterByCmsType($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCmsContents;
                }
                $this->collCmsContents = $collCmsContents;
            }
        }

        return $this->collCmsContents;
    }

    /**
     * Sets a collection of CmsContent objects related by a many-to-many relationship
     * to the current object by way of the cms_content_has_types cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $cmsContents A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CmsType The current object (for fluent API support)
     */
    public function setCmsContents(PropelCollection $cmsContents, PropelPDO $con = null)
    {
        $this->clearCmsContents();
        $currentCmsContents = $this->getCmsContents(null, $con);

        $this->cmsContentsScheduledForDeletion = $currentCmsContents->diff($cmsContents);

        foreach ($cmsContents as $cmsContent) {
            if (!$currentCmsContents->contains($cmsContent)) {
                $this->doAddCmsContent($cmsContent);
            }
        }

        $this->collCmsContents = $cmsContents;

        return $this;
    }

    /**
     * Gets the number of CmsContent objects related by a many-to-many relationship
     * to the current object by way of the cms_content_has_types cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related CmsContent objects
     */
    public function countCmsContents($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collCmsContents || null !== $criteria) {
            if ($this->isNew() && null === $this->collCmsContents) {
                return 0;
            } else {
                $query = CmsContentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByCmsType($this)
                    ->count($con);
            }
        } else {
            return count($this->collCmsContents);
        }
    }

    /**
     * Associate a CmsContent object to this object
     * through the cms_content_has_types cross reference table.
     *
     * @param  CmsContent $cmsContent The CmsContentHasTypes object to relate
     * @return CmsType The current object (for fluent API support)
     */
    public function addCmsContent(CmsContent $cmsContent)
    {
        if ($this->collCmsContents === null) {
            $this->initCmsContents();
        }

        if (!$this->collCmsContents->contains($cmsContent)) { // only add it if the **same** object is not already associated
            $this->doAddCmsContent($cmsContent);
            $this->collCmsContents[] = $cmsContent;

            if ($this->cmsContentsScheduledForDeletion and $this->cmsContentsScheduledForDeletion->contains($cmsContent)) {
                $this->cmsContentsScheduledForDeletion->remove($this->cmsContentsScheduledForDeletion->search($cmsContent));
            }
        }

        return $this;
    }

    /**
     * @param	CmsContent $cmsContent The cmsContent object to add.
     */
    protected function doAddCmsContent(CmsContent $cmsContent)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$cmsContent->getCmsTypes()->contains($this)) { $cmsContentHasTypes = new CmsContentHasTypes();
            $cmsContentHasTypes->setCmsContent($cmsContent);
            $this->addCmsContentHasTypes($cmsContentHasTypes);

            $foreignCollection = $cmsContent->getCmsTypes();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a CmsContent object to this object
     * through the cms_content_has_types cross reference table.
     *
     * @param CmsContent $cmsContent The CmsContentHasTypes object to relate
     * @return CmsType The current object (for fluent API support)
     */
    public function removeCmsContent(CmsContent $cmsContent)
    {
        if ($this->getCmsContents()->contains($cmsContent)) {
            $this->collCmsContents->remove($this->collCmsContents->search($cmsContent));
            if (null === $this->cmsContentsScheduledForDeletion) {
                $this->cmsContentsScheduledForDeletion = clone $this->collCmsContents;
                $this->cmsContentsScheduledForDeletion->clear();
            }
            $this->cmsContentsScheduledForDeletion[]= $cmsContent;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->created_at = null;
        $this->updated_at = null;
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
            if ($this->collCmsContentHasTypess) {
                foreach ($this->collCmsContentHasTypess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCmsContents) {
                foreach ($this->collCmsContents as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCmsContentHasTypess instanceof PropelCollection) {
            $this->collCmsContentHasTypess->clearIterator();
        }
        $this->collCmsContentHasTypess = null;
        if ($this->collCmsContents instanceof PropelCollection) {
            $this->collCmsContents->clearIterator();
        }
        $this->collCmsContents = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CmsTypePeer::DEFAULT_STRING_FORMAT);
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

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     CmsType The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = CmsTypePeer::UPDATED_AT;

        return $this;
    }

}

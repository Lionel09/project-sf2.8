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
use Model\CmsCategory;
use Model\CmsCategoryQuery;
use Model\CmsContent;
use Model\CmsContentHasTypes;
use Model\CmsContentHasTypesQuery;
use Model\CmsContentPeer;
use Model\CmsContentQuery;
use Model\CmsType;
use Model\CmsTypeQuery;

abstract class BaseCmsContent extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Model\\CmsContentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CmsContentPeer
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
     * The value for the cms_category_id field.
     * @var        int
     */
    protected $cms_category_id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the summary field.
     * @var        string
     */
    protected $summary;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the online field.
     * @var        boolean
     */
    protected $online;

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
     * @var        CmsCategory
     */
    protected $aCmsCategory;

    /**
     * @var        PropelObjectCollection|CmsContentHasTypes[] Collection to store aggregation of CmsContentHasTypes objects.
     */
    protected $collCmsContentHasTypess;
    protected $collCmsContentHasTypessPartial;

    /**
     * @var        PropelObjectCollection|CmsType[] Collection to store aggregation of CmsType objects.
     */
    protected $collCmsTypes;

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
    protected $cmsTypesScheduledForDeletion = null;

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
     * Get the [cms_category_id] column value.
     *
     * @return int
     */
    public function getCmsCategoryId()
    {

        return $this->cms_category_id;
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
     * Get the [summary] column value.
     *
     * @return string
     */
    public function getSummary()
    {

        return $this->summary;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
    }

    /**
     * Get the [online] column value.
     *
     * @return boolean
     */
    public function getOnline()
    {

        return $this->online;
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
     * @return CmsContent The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CmsContentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [cms_category_id] column.
     *
     * @param  int $v new value
     * @return CmsContent The current object (for fluent API support)
     */
    public function setCmsCategoryId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->cms_category_id !== $v) {
            $this->cms_category_id = $v;
            $this->modifiedColumns[] = CmsContentPeer::CMS_CATEGORY_ID;
        }

        if ($this->aCmsCategory !== null && $this->aCmsCategory->getId() !== $v) {
            $this->aCmsCategory = null;
        }


        return $this;
    } // setCmsCategoryId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return CmsContent The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = CmsContentPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [summary] column.
     *
     * @param  string $v new value
     * @return CmsContent The current object (for fluent API support)
     */
    public function setSummary($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->summary !== $v) {
            $this->summary = $v;
            $this->modifiedColumns[] = CmsContentPeer::SUMMARY;
        }


        return $this;
    } // setSummary()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CmsContent The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CmsContentPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Sets the value of the [online] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return CmsContent The current object (for fluent API support)
     */
    public function setOnline($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->online !== $v) {
            $this->online = $v;
            $this->modifiedColumns[] = CmsContentPeer::ONLINE;
        }


        return $this;
    } // setOnline()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CmsContent The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = CmsContentPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return CmsContent The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = CmsContentPeer::UPDATED_AT;
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
            $this->cms_category_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->title = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->summary = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->online = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->created_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->updated_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = CmsContentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CmsContent object", $e);
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

        if ($this->aCmsCategory !== null && $this->cms_category_id !== $this->aCmsCategory->getId()) {
            $this->aCmsCategory = null;
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
            $con = Propel::getConnection(CmsContentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CmsContentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCmsCategory = null;
            $this->collCmsContentHasTypess = null;

            $this->collCmsTypes = null;
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
            $con = Propel::getConnection(CmsContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CmsContentQuery::create()
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
            $con = Propel::getConnection(CmsContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CmsContentPeer::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CmsContentPeer::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CmsContentPeer::UPDATED_AT)) {
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
                CmsContentPeer::addInstanceToPool($this);
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

            if ($this->aCmsCategory !== null) {
                if ($this->aCmsCategory->isModified() || $this->aCmsCategory->isNew()) {
                    $affectedRows += $this->aCmsCategory->save($con);
                }
                $this->setCmsCategory($this->aCmsCategory);
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

            if ($this->cmsTypesScheduledForDeletion !== null) {
                if (!$this->cmsTypesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->cmsTypesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    CmsContentHasTypesQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->cmsTypesScheduledForDeletion = null;
                }

                foreach ($this->getCmsTypes() as $cmsType) {
                    if ($cmsType->isModified()) {
                        $cmsType->save($con);
                    }
                }
            } elseif ($this->collCmsTypes) {
                foreach ($this->collCmsTypes as $cmsType) {
                    if ($cmsType->isModified()) {
                        $cmsType->save($con);
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

        $this->modifiedColumns[] = CmsContentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CmsContentPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CmsContentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CmsContentPeer::CMS_CATEGORY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`cms_category_id`';
        }
        if ($this->isColumnModified(CmsContentPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(CmsContentPeer::SUMMARY)) {
            $modifiedColumns[':p' . $index++]  = '`summary`';
        }
        if ($this->isColumnModified(CmsContentPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`description`';
        }
        if ($this->isColumnModified(CmsContentPeer::ONLINE)) {
            $modifiedColumns[':p' . $index++]  = '`online`';
        }
        if ($this->isColumnModified(CmsContentPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CmsContentPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `cms_content` (%s) VALUES (%s)',
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
                    case '`cms_category_id`':
                        $stmt->bindValue($identifier, $this->cms_category_id, PDO::PARAM_INT);
                        break;
                    case '`title`':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`summary`':
                        $stmt->bindValue($identifier, $this->summary, PDO::PARAM_STR);
                        break;
                    case '`description`':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '`online`':
                        $stmt->bindValue($identifier, (int) $this->online, PDO::PARAM_INT);
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
        $pos = CmsContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getCmsCategoryId();
                break;
            case 2:
                return $this->getTitle();
                break;
            case 3:
                return $this->getSummary();
                break;
            case 4:
                return $this->getDescription();
                break;
            case 5:
                return $this->getOnline();
                break;
            case 6:
                return $this->getCreatedAt();
                break;
            case 7:
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
        if (isset($alreadyDumpedObjects['CmsContent'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CmsContent'][$this->getPrimaryKey()] = true;
        $keys = CmsContentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCmsCategoryId(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getSummary(),
            $keys[4] => $this->getDescription(),
            $keys[5] => $this->getOnline(),
            $keys[6] => $this->getCreatedAt(),
            $keys[7] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCmsCategory) {
                $result['CmsCategory'] = $this->aCmsCategory->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
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
        $pos = CmsContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setCmsCategoryId($value);
                break;
            case 2:
                $this->setTitle($value);
                break;
            case 3:
                $this->setSummary($value);
                break;
            case 4:
                $this->setDescription($value);
                break;
            case 5:
                $this->setOnline($value);
                break;
            case 6:
                $this->setCreatedAt($value);
                break;
            case 7:
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
        $keys = CmsContentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCmsCategoryId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSummary($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setOnline($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedAt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setUpdatedAt($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CmsContentPeer::DATABASE_NAME);

        if ($this->isColumnModified(CmsContentPeer::ID)) $criteria->add(CmsContentPeer::ID, $this->id);
        if ($this->isColumnModified(CmsContentPeer::CMS_CATEGORY_ID)) $criteria->add(CmsContentPeer::CMS_CATEGORY_ID, $this->cms_category_id);
        if ($this->isColumnModified(CmsContentPeer::TITLE)) $criteria->add(CmsContentPeer::TITLE, $this->title);
        if ($this->isColumnModified(CmsContentPeer::SUMMARY)) $criteria->add(CmsContentPeer::SUMMARY, $this->summary);
        if ($this->isColumnModified(CmsContentPeer::DESCRIPTION)) $criteria->add(CmsContentPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CmsContentPeer::ONLINE)) $criteria->add(CmsContentPeer::ONLINE, $this->online);
        if ($this->isColumnModified(CmsContentPeer::CREATED_AT)) $criteria->add(CmsContentPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CmsContentPeer::UPDATED_AT)) $criteria->add(CmsContentPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CmsContentPeer::DATABASE_NAME);
        $criteria->add(CmsContentPeer::ID, $this->id);

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
     * @param object $copyObj An object of CmsContent (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCmsCategoryId($this->getCmsCategoryId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setSummary($this->getSummary());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setOnline($this->getOnline());
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
     * @return CmsContent Clone of current object.
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
     * @return CmsContentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CmsContentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a CmsCategory object.
     *
     * @param                  CmsCategory $v
     * @return CmsContent The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCmsCategory(CmsCategory $v = null)
    {
        if ($v === null) {
            $this->setCmsCategoryId(NULL);
        } else {
            $this->setCmsCategoryId($v->getId());
        }

        $this->aCmsCategory = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CmsCategory object, it will not be re-added.
        if ($v !== null) {
            $v->addCmsContent($this);
        }


        return $this;
    }


    /**
     * Get the associated CmsCategory object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CmsCategory The associated CmsCategory object.
     * @throws PropelException
     */
    public function getCmsCategory(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCmsCategory === null && ($this->cms_category_id !== null) && $doQuery) {
            $this->aCmsCategory = CmsCategoryQuery::create()->findPk($this->cms_category_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCmsCategory->addCmsContents($this);
             */
        }

        return $this->aCmsCategory;
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
     * @return CmsContent The current object (for fluent API support)
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
     * If this CmsContent is new, it will return
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
                    ->filterByCmsContent($this)
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
     * @return CmsContent The current object (for fluent API support)
     */
    public function setCmsContentHasTypess(PropelCollection $cmsContentHasTypess, PropelPDO $con = null)
    {
        $cmsContentHasTypessToDelete = $this->getCmsContentHasTypess(new Criteria(), $con)->diff($cmsContentHasTypess);


        $this->cmsContentHasTypessScheduledForDeletion = $cmsContentHasTypessToDelete;

        foreach ($cmsContentHasTypessToDelete as $cmsContentHasTypesRemoved) {
            $cmsContentHasTypesRemoved->setCmsContent(null);
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
                ->filterByCmsContent($this)
                ->count($con);
        }

        return count($this->collCmsContentHasTypess);
    }

    /**
     * Method called to associate a CmsContentHasTypes object to this object
     * through the CmsContentHasTypes foreign key attribute.
     *
     * @param    CmsContentHasTypes $l CmsContentHasTypes
     * @return CmsContent The current object (for fluent API support)
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
        $cmsContentHasTypes->setCmsContent($this);
    }

    /**
     * @param	CmsContentHasTypes $cmsContentHasTypes The cmsContentHasTypes object to remove.
     * @return CmsContent The current object (for fluent API support)
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
            $cmsContentHasTypes->setCmsContent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CmsContent is new, it will return
     * an empty collection; or if this CmsContent has previously
     * been saved, it will retrieve related CmsContentHasTypess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CmsContent.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CmsContentHasTypes[] List of CmsContentHasTypes objects
     */
    public function getCmsContentHasTypessJoinCmsType($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CmsContentHasTypesQuery::create(null, $criteria);
        $query->joinWith('CmsType', $join_behavior);

        return $this->getCmsContentHasTypess($query, $con);
    }

    /**
     * Clears out the collCmsTypes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CmsContent The current object (for fluent API support)
     * @see        addCmsTypes()
     */
    public function clearCmsTypes()
    {
        $this->collCmsTypes = null; // important to set this to null since that means it is uninitialized
        $this->collCmsTypesPartial = null;

        return $this;
    }

    /**
     * Initializes the collCmsTypes collection.
     *
     * By default this just sets the collCmsTypes collection to an empty collection (like clearCmsTypes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initCmsTypes()
    {
        $this->collCmsTypes = new PropelObjectCollection();
        $this->collCmsTypes->setModel('CmsType');
    }

    /**
     * Gets a collection of CmsType objects related by a many-to-many relationship
     * to the current object by way of the cms_content_has_types cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CmsContent is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|CmsType[] List of CmsType objects
     */
    public function getCmsTypes($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collCmsTypes || null !== $criteria) {
            if ($this->isNew() && null === $this->collCmsTypes) {
                // return empty collection
                $this->initCmsTypes();
            } else {
                $collCmsTypes = CmsTypeQuery::create(null, $criteria)
                    ->filterByCmsContent($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCmsTypes;
                }
                $this->collCmsTypes = $collCmsTypes;
            }
        }

        return $this->collCmsTypes;
    }

    /**
     * Sets a collection of CmsType objects related by a many-to-many relationship
     * to the current object by way of the cms_content_has_types cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $cmsTypes A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CmsContent The current object (for fluent API support)
     */
    public function setCmsTypes(PropelCollection $cmsTypes, PropelPDO $con = null)
    {
        $this->clearCmsTypes();
        $currentCmsTypes = $this->getCmsTypes(null, $con);

        $this->cmsTypesScheduledForDeletion = $currentCmsTypes->diff($cmsTypes);

        foreach ($cmsTypes as $cmsType) {
            if (!$currentCmsTypes->contains($cmsType)) {
                $this->doAddCmsType($cmsType);
            }
        }

        $this->collCmsTypes = $cmsTypes;

        return $this;
    }

    /**
     * Gets the number of CmsType objects related by a many-to-many relationship
     * to the current object by way of the cms_content_has_types cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related CmsType objects
     */
    public function countCmsTypes($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collCmsTypes || null !== $criteria) {
            if ($this->isNew() && null === $this->collCmsTypes) {
                return 0;
            } else {
                $query = CmsTypeQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByCmsContent($this)
                    ->count($con);
            }
        } else {
            return count($this->collCmsTypes);
        }
    }

    /**
     * Associate a CmsType object to this object
     * through the cms_content_has_types cross reference table.
     *
     * @param  CmsType $cmsType The CmsContentHasTypes object to relate
     * @return CmsContent The current object (for fluent API support)
     */
    public function addCmsType(CmsType $cmsType)
    {
        if ($this->collCmsTypes === null) {
            $this->initCmsTypes();
        }

        if (!$this->collCmsTypes->contains($cmsType)) { // only add it if the **same** object is not already associated
            $this->doAddCmsType($cmsType);
            $this->collCmsTypes[] = $cmsType;

            if ($this->cmsTypesScheduledForDeletion and $this->cmsTypesScheduledForDeletion->contains($cmsType)) {
                $this->cmsTypesScheduledForDeletion->remove($this->cmsTypesScheduledForDeletion->search($cmsType));
            }
        }

        return $this;
    }

    /**
     * @param	CmsType $cmsType The cmsType object to add.
     */
    protected function doAddCmsType(CmsType $cmsType)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$cmsType->getCmsContents()->contains($this)) { $cmsContentHasTypes = new CmsContentHasTypes();
            $cmsContentHasTypes->setCmsType($cmsType);
            $this->addCmsContentHasTypes($cmsContentHasTypes);

            $foreignCollection = $cmsType->getCmsContents();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a CmsType object to this object
     * through the cms_content_has_types cross reference table.
     *
     * @param CmsType $cmsType The CmsContentHasTypes object to relate
     * @return CmsContent The current object (for fluent API support)
     */
    public function removeCmsType(CmsType $cmsType)
    {
        if ($this->getCmsTypes()->contains($cmsType)) {
            $this->collCmsTypes->remove($this->collCmsTypes->search($cmsType));
            if (null === $this->cmsTypesScheduledForDeletion) {
                $this->cmsTypesScheduledForDeletion = clone $this->collCmsTypes;
                $this->cmsTypesScheduledForDeletion->clear();
            }
            $this->cmsTypesScheduledForDeletion[]= $cmsType;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->cms_category_id = null;
        $this->title = null;
        $this->summary = null;
        $this->description = null;
        $this->online = null;
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
            if ($this->collCmsTypes) {
                foreach ($this->collCmsTypes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCmsCategory instanceof Persistent) {
              $this->aCmsCategory->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCmsContentHasTypess instanceof PropelCollection) {
            $this->collCmsContentHasTypess->clearIterator();
        }
        $this->collCmsContentHasTypess = null;
        if ($this->collCmsTypes instanceof PropelCollection) {
            $this->collCmsTypes->clearIterator();
        }
        $this->collCmsTypes = null;
        $this->aCmsCategory = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CmsContentPeer::DEFAULT_STRING_FORMAT);
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
     * @return     CmsContent The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[] = CmsContentPeer::UPDATED_AT;

        return $this;
    }

}

<?php

namespace Model\om;

use \BasePeer;
use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Model\CmsContent;
use Model\CmsContentHasTypes;
use Model\CmsContentHasTypesPeer;
use Model\CmsContentHasTypesQuery;
use Model\CmsType;

/**
 * @method CmsContentHasTypesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CmsContentHasTypesQuery orderByCmsContentId($order = Criteria::ASC) Order by the cms_content_id column
 * @method CmsContentHasTypesQuery orderByCmsTypeId($order = Criteria::ASC) Order by the cms_type_id column
 * @method CmsContentHasTypesQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method CmsContentHasTypesQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method CmsContentHasTypesQuery groupById() Group by the id column
 * @method CmsContentHasTypesQuery groupByCmsContentId() Group by the cms_content_id column
 * @method CmsContentHasTypesQuery groupByCmsTypeId() Group by the cms_type_id column
 * @method CmsContentHasTypesQuery groupByCreatedAt() Group by the created_at column
 * @method CmsContentHasTypesQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method CmsContentHasTypesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CmsContentHasTypesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CmsContentHasTypesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CmsContentHasTypesQuery leftJoinCmsContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsContent relation
 * @method CmsContentHasTypesQuery rightJoinCmsContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsContent relation
 * @method CmsContentHasTypesQuery innerJoinCmsContent($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsContent relation
 *
 * @method CmsContentHasTypesQuery leftJoinCmsType($relationAlias = null) Adds a LEFT JOIN clause to the query using the CmsType relation
 * @method CmsContentHasTypesQuery rightJoinCmsType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CmsType relation
 * @method CmsContentHasTypesQuery innerJoinCmsType($relationAlias = null) Adds a INNER JOIN clause to the query using the CmsType relation
 *
 * @method CmsContentHasTypes findOne(PropelPDO $con = null) Return the first CmsContentHasTypes matching the query
 * @method CmsContentHasTypes findOneOrCreate(PropelPDO $con = null) Return the first CmsContentHasTypes matching the query, or a new CmsContentHasTypes object populated from the query conditions when no match is found
 *
 * @method CmsContentHasTypes findOneByCmsContentId(int $cms_content_id) Return the first CmsContentHasTypes filtered by the cms_content_id column
 * @method CmsContentHasTypes findOneByCmsTypeId(int $cms_type_id) Return the first CmsContentHasTypes filtered by the cms_type_id column
 * @method CmsContentHasTypes findOneByCreatedAt(string $created_at) Return the first CmsContentHasTypes filtered by the created_at column
 * @method CmsContentHasTypes findOneByUpdatedAt(string $updated_at) Return the first CmsContentHasTypes filtered by the updated_at column
 *
 * @method array findById(int $id) Return CmsContentHasTypes objects filtered by the id column
 * @method array findByCmsContentId(int $cms_content_id) Return CmsContentHasTypes objects filtered by the cms_content_id column
 * @method array findByCmsTypeId(int $cms_type_id) Return CmsContentHasTypes objects filtered by the cms_type_id column
 * @method array findByCreatedAt(string $created_at) Return CmsContentHasTypes objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return CmsContentHasTypes objects filtered by the updated_at column
 */
abstract class BaseCmsContentHasTypesQuery extends ModelCriteria
{
    // query_cache behavior
    protected $queryKey = '';

    /**
     * Initializes internal state of BaseCmsContentHasTypesQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Model\\CmsContentHasTypes';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CmsContentHasTypesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CmsContentHasTypesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CmsContentHasTypesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CmsContentHasTypesQuery) {
            return $criteria;
        }
        $query = new CmsContentHasTypesQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CmsContentHasTypes|CmsContentHasTypes[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CmsContentHasTypesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CmsContentHasTypesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 CmsContentHasTypes A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 CmsContentHasTypes A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `cms_content_id`, `cms_type_id`, `created_at`, `updated_at` FROM `cms_content_has_types` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CmsContentHasTypes();
            $obj->hydrate($row);
            CmsContentHasTypesPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return CmsContentHasTypes|CmsContentHasTypes[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|CmsContentHasTypes[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CmsContentHasTypesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CmsContentHasTypesPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsContentHasTypesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the cms_content_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCmsContentId(1234); // WHERE cms_content_id = 1234
     * $query->filterByCmsContentId(array(12, 34)); // WHERE cms_content_id IN (12, 34)
     * $query->filterByCmsContentId(array('min' => 12)); // WHERE cms_content_id >= 12
     * $query->filterByCmsContentId(array('max' => 12)); // WHERE cms_content_id <= 12
     * </code>
     *
     * @see       filterByCmsContent()
     *
     * @param     mixed $cmsContentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterByCmsContentId($cmsContentId = null, $comparison = null)
    {
        if (is_array($cmsContentId)) {
            $useMinMax = false;
            if (isset($cmsContentId['min'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::CMS_CONTENT_ID, $cmsContentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cmsContentId['max'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::CMS_CONTENT_ID, $cmsContentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsContentHasTypesPeer::CMS_CONTENT_ID, $cmsContentId, $comparison);
    }

    /**
     * Filter the query on the cms_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCmsTypeId(1234); // WHERE cms_type_id = 1234
     * $query->filterByCmsTypeId(array(12, 34)); // WHERE cms_type_id IN (12, 34)
     * $query->filterByCmsTypeId(array('min' => 12)); // WHERE cms_type_id >= 12
     * $query->filterByCmsTypeId(array('max' => 12)); // WHERE cms_type_id <= 12
     * </code>
     *
     * @see       filterByCmsType()
     *
     * @param     mixed $cmsTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterByCmsTypeId($cmsTypeId = null, $comparison = null)
    {
        if (is_array($cmsTypeId)) {
            $useMinMax = false;
            if (isset($cmsTypeId['min'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::CMS_TYPE_ID, $cmsTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cmsTypeId['max'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::CMS_TYPE_ID, $cmsTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsContentHasTypesPeer::CMS_TYPE_ID, $cmsTypeId, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsContentHasTypesPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CmsContentHasTypesPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CmsContentHasTypesPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related CmsContent object
     *
     * @param   CmsContent|PropelObjectCollection $cmsContent The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsContentHasTypesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsContent($cmsContent, $comparison = null)
    {
        if ($cmsContent instanceof CmsContent) {
            return $this
                ->addUsingAlias(CmsContentHasTypesPeer::CMS_CONTENT_ID, $cmsContent->getId(), $comparison);
        } elseif ($cmsContent instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CmsContentHasTypesPeer::CMS_CONTENT_ID, $cmsContent->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCmsContent() only accepts arguments of type CmsContent or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsContent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function joinCmsContent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsContent');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CmsContent');
        }

        return $this;
    }

    /**
     * Use the CmsContent relation CmsContent object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Model\CmsContentQuery A secondary query class using the current class as primary query
     */
    public function useCmsContentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCmsContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsContent', '\Model\CmsContentQuery');
    }

    /**
     * Filter the query by a related CmsType object
     *
     * @param   CmsType|PropelObjectCollection $cmsType The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CmsContentHasTypesQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCmsType($cmsType, $comparison = null)
    {
        if ($cmsType instanceof CmsType) {
            return $this
                ->addUsingAlias(CmsContentHasTypesPeer::CMS_TYPE_ID, $cmsType->getId(), $comparison);
        } elseif ($cmsType instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CmsContentHasTypesPeer::CMS_TYPE_ID, $cmsType->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCmsType() only accepts arguments of type CmsType or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CmsType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function joinCmsType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CmsType');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CmsType');
        }

        return $this;
    }

    /**
     * Use the CmsType relation CmsType object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Model\CmsTypeQuery A secondary query class using the current class as primary query
     */
    public function useCmsTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCmsType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CmsType', '\Model\CmsTypeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CmsContentHasTypes $cmsContentHasTypes Object to remove from the list of results
     *
     * @return CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function prune($cmsContentHasTypes = null)
    {
        if ($cmsContentHasTypes) {
            $this->addUsingAlias(CmsContentHasTypesPeer::ID, $cmsContentHasTypes->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CmsContentHasTypesPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CmsContentHasTypesPeer::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CmsContentHasTypesPeer::UPDATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CmsContentHasTypesPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CmsContentHasTypesPeer::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     CmsContentHasTypesQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CmsContentHasTypesPeer::CREATED_AT);
    }
    // query_cache behavior

    public function setQueryKey($key)
    {
        $this->queryKey = $key;

        return $this;
    }

    public function getQueryKey()
    {
        return $this->queryKey;
    }

    public function cacheContains($key)
    {

        return apc_fetch($key);
    }

    public function cacheFetch($key)
    {

        return apc_fetch($key);
    }

    public function cacheStore($key, $value, $lifetime = 3600)
    {
        apc_store($key, $value, $lifetime);
    }

    protected function doSelect($con)
    {
        // check that the columns of the main class are already added (if this is the primary ModelCriteria)
        if (!$this->hasSelectClause() && !$this->getPrimaryCriteria()) {
            $this->addSelfSelectColumns();
        }
        $this->configureSelectColumns();

        $dbMap = Propel::getDatabaseMap(CmsContentHasTypesPeer::DATABASE_NAME);
        $db = Propel::getDB(CmsContentHasTypesPeer::DATABASE_NAME);

        $key = $this->getQueryKey();
        if ($key && $this->cacheContains($key)) {
            $params = $this->getParams();
            $sql = $this->cacheFetch($key);
        } else {
            $params = array();
            $sql = BasePeer::createSelectSql($this, $params);
            if ($key) {
                $this->cacheStore($key, $sql);
            }
        }

        try {
            $stmt = $con->prepare($sql);
            $db->bindValues($stmt, $params, $dbMap);
            $stmt->execute();
            } catch (Exception $e) {
                Propel::log($e->getMessage(), Propel::LOG_ERR);
                throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
            }

        return $stmt;
    }

    protected function doCount($con)
    {
        $dbMap = Propel::getDatabaseMap($this->getDbName());
        $db = Propel::getDB($this->getDbName());

        $key = $this->getQueryKey();
        if ($key && $this->cacheContains($key)) {
            $params = $this->getParams();
            $sql = $this->cacheFetch($key);
        } else {
            // check that the columns of the main class are already added (if this is the primary ModelCriteria)
            if (!$this->hasSelectClause() && !$this->getPrimaryCriteria()) {
                $this->addSelfSelectColumns();
            }

            $this->configureSelectColumns();

            $needsComplexCount = $this->getGroupByColumns()
                || $this->getOffset()
                || $this->getLimit()
                || $this->getHaving()
                || in_array(Criteria::DISTINCT, $this->getSelectModifiers());

            $params = array();
            if ($needsComplexCount) {
                if (BasePeer::needsSelectAliases($this)) {
                    if ($this->getHaving()) {
                        throw new PropelException('Propel cannot create a COUNT query when using HAVING and  duplicate column names in the SELECT part');
                    }
                    $db->turnSelectColumnsToAliases($this);
                }
                $selectSql = BasePeer::createSelectSql($this, $params);
                $sql = 'SELECT COUNT(*) FROM (' . $selectSql . ') propelmatch4cnt';
            } else {
                // Replace SELECT columns with COUNT(*)
                $this->clearSelectColumns()->addSelectColumn('COUNT(*)');
                $sql = BasePeer::createSelectSql($this, $params);
            }

            if ($key) {
                $this->cacheStore($key, $sql);
            }
        }

        try {
            $stmt = $con->prepare($sql);
            $db->bindValues($stmt, $params, $dbMap);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute COUNT statement [%s]', $sql), $e);
        }

        return $stmt;
    }

}

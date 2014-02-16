<?php

namespace Acme\WikiBundle\Model\om;

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
use Acme\WikiBundle\Model\Page;
use Acme\WikiBundle\Model\PagePeer;
use Acme\WikiBundle\Model\PageQuery;

/**
 * @method PageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PageQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method PageQuery orderByParentpath($order = Criteria::ASC) Order by the parentPath column
 * @method PageQuery orderByPagename($order = Criteria::ASC) Order by the pageName column
 * @method PageQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method PageQuery orderByText($order = Criteria::ASC) Order by the text column
 *
 * @method PageQuery groupById() Group by the id column
 * @method PageQuery groupByParentId() Group by the parent_id column
 * @method PageQuery groupByParentpath() Group by the parentPath column
 * @method PageQuery groupByPagename() Group by the pageName column
 * @method PageQuery groupByTitle() Group by the title column
 * @method PageQuery groupByText() Group by the text column
 *
 * @method PageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PageQuery leftJoinParent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Parent relation
 * @method PageQuery rightJoinParent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Parent relation
 * @method PageQuery innerJoinParent($relationAlias = null) Adds a INNER JOIN clause to the query using the Parent relation
 *
 * @method PageQuery leftJoinChildren_ref($relationAlias = null) Adds a LEFT JOIN clause to the query using the Children_ref relation
 * @method PageQuery rightJoinChildren_ref($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Children_ref relation
 * @method PageQuery innerJoinChildren_ref($relationAlias = null) Adds a INNER JOIN clause to the query using the Children_ref relation
 *
 * @method Page findOne(PropelPDO $con = null) Return the first Page matching the query
 * @method Page findOneOrCreate(PropelPDO $con = null) Return the first Page matching the query, or a new Page object populated from the query conditions when no match is found
 *
 * @method Page findOneByParentId(int $parent_id) Return the first Page filtered by the parent_id column
 * @method Page findOneByParentpath(string $parentPath) Return the first Page filtered by the parentPath column
 * @method Page findOneByPagename(string $pageName) Return the first Page filtered by the pageName column
 * @method Page findOneByTitle(string $title) Return the first Page filtered by the title column
 * @method Page findOneByText(string $text) Return the first Page filtered by the text column
 *
 * @method array findById(int $id) Return Page objects filtered by the id column
 * @method array findByParentId(int $parent_id) Return Page objects filtered by the parent_id column
 * @method array findByParentpath(string $parentPath) Return Page objects filtered by the parentPath column
 * @method array findByPagename(string $pageName) Return Page objects filtered by the pageName column
 * @method array findByTitle(string $title) Return Page objects filtered by the title column
 * @method array findByText(string $text) Return Page objects filtered by the text column
 */
abstract class BasePageQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePageQuery object.
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
            $modelName = 'Acme\\WikiBundle\\Model\\Page';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PageQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PageQuery) {
            return $criteria;
        }
        $query = new PageQuery(null, null, $modelAlias);

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
     * @return   Page|Page[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PagePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Page A model object, or null if the key is not found
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
     * @return                 Page A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `parent_id`, `parentPath`, `pageName`, `title` FROM `page` WHERE `id` = :p0';
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
            $obj = new Page();
            $obj->hydrate($row);
            PagePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Page|Page[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Page[]|mixed the list of results, formatted by the current formatter
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PagePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PagePeer::ID, $keys, Criteria::IN);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PagePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PagePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentId(1234); // WHERE parent_id = 1234
     * $query->filterByParentId(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByParentId(array('min' => 12)); // WHERE parent_id >= 12
     * $query->filterByParentId(array('max' => 12)); // WHERE parent_id <= 12
     * </code>
     *
     * @see       filterByParent()
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(PagePeer::PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(PagePeer::PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::PARENT_ID, $parentId, $comparison);
    }

    /**
     * Filter the query on the parentPath column
     *
     * Example usage:
     * <code>
     * $query->filterByParentpath('fooValue');   // WHERE parentPath = 'fooValue'
     * $query->filterByParentpath('%fooValue%'); // WHERE parentPath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $parentpath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByParentpath($parentpath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($parentpath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $parentpath)) {
                $parentpath = str_replace('*', '%', $parentpath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagePeer::PARENTPATH, $parentpath, $comparison);
    }

    /**
     * Filter the query on the pageName column
     *
     * Example usage:
     * <code>
     * $query->filterByPagename('fooValue');   // WHERE pageName = 'fooValue'
     * $query->filterByPagename('%fooValue%'); // WHERE pageName LIKE '%fooValue%'
     * </code>
     *
     * @param     string $pagename The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByPagename($pagename = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($pagename)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $pagename)) {
                $pagename = str_replace('*', '%', $pagename);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagePeer::PAGENAME, $pagename, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagePeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the text column
     *
     * Example usage:
     * <code>
     * $query->filterByText('fooValue');   // WHERE text = 'fooValue'
     * $query->filterByText('%fooValue%'); // WHERE text LIKE '%fooValue%'
     * </code>
     *
     * @param     string $text The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByText($text = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($text)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $text)) {
                $text = str_replace('*', '%', $text);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagePeer::TEXT, $text, $comparison);
    }

    /**
     * Filter the query by a related Page object
     *
     * @param   Page|PropelObjectCollection $page The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PageQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByParent($page, $comparison = null)
    {
        if ($page instanceof Page) {
            return $this
                ->addUsingAlias(PagePeer::PARENT_ID, $page->getId(), $comparison);
        } elseif ($page instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PagePeer::PARENT_ID, $page->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByParent() only accepts arguments of type Page or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Parent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinParent($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Parent');

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
            $this->addJoinObject($join, 'Parent');
        }

        return $this;
    }

    /**
     * Use the Parent relation Page object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Acme\WikiBundle\Model\PageQuery A secondary query class using the current class as primary query
     */
    public function useParentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinParent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Parent', '\Acme\WikiBundle\Model\PageQuery');
    }

    /**
     * Filter the query by a related Page object
     *
     * @param   Page|PropelObjectCollection $page  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PageQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByChildren_ref($page, $comparison = null)
    {
        if ($page instanceof Page) {
            return $this
                ->addUsingAlias(PagePeer::ID, $page->getParentId(), $comparison);
        } elseif ($page instanceof PropelObjectCollection) {
            return $this
                ->useChildren_refQuery()
                ->filterByPrimaryKeys($page->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByChildren_ref() only accepts arguments of type Page or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Children_ref relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinChildren_ref($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Children_ref');

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
            $this->addJoinObject($join, 'Children_ref');
        }

        return $this;
    }

    /**
     * Use the Children_ref relation Page object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Acme\WikiBundle\Model\PageQuery A secondary query class using the current class as primary query
     */
    public function useChildren_refQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinChildren_ref($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Children_ref', '\Acme\WikiBundle\Model\PageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Page $page Object to remove from the list of results
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function prune($page = null)
    {
        if ($page) {
            $this->addUsingAlias(PagePeer::ID, $page->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

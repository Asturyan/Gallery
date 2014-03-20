<?php

namespace Gallery\Model\Base;

use \Exception;
use \PDO;
use Gallery\Model\GalleryImage as ChildGalleryImage;
use Gallery\Model\GalleryImageI18nQuery as ChildGalleryImageI18nQuery;
use Gallery\Model\GalleryImageQuery as ChildGalleryImageQuery;
use Gallery\Model\Map\GalleryImageTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'gallery_image' table.
 *
 *
 *
 * @method     ChildGalleryImageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildGalleryImageQuery orderByGalleryId($order = Criteria::ASC) Order by the gallery_id column
 * @method     ChildGalleryImageQuery orderByFile($order = Criteria::ASC) Order by the file column
 * @method     ChildGalleryImageQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildGalleryImageQuery orderBySubtypeId($order = Criteria::ASC) Order by the subtype_id column
 * @method     ChildGalleryImageQuery orderByTypeId($order = Criteria::ASC) Order by the type_id column
 * @method     ChildGalleryImageQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     ChildGalleryImageQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildGalleryImageQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildGalleryImageQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildGalleryImageQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildGalleryImageQuery groupById() Group by the id column
 * @method     ChildGalleryImageQuery groupByGalleryId() Group by the gallery_id column
 * @method     ChildGalleryImageQuery groupByFile() Group by the file column
 * @method     ChildGalleryImageQuery groupByType() Group by the type column
 * @method     ChildGalleryImageQuery groupBySubtypeId() Group by the subtype_id column
 * @method     ChildGalleryImageQuery groupByTypeId() Group by the type_id column
 * @method     ChildGalleryImageQuery groupByUrl() Group by the url column
 * @method     ChildGalleryImageQuery groupByPosition() Group by the position column
 * @method     ChildGalleryImageQuery groupByVisible() Group by the visible column
 * @method     ChildGalleryImageQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildGalleryImageQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildGalleryImageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildGalleryImageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildGalleryImageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildGalleryImageQuery leftJoinGallery($relationAlias = null) Adds a LEFT JOIN clause to the query using the Gallery relation
 * @method     ChildGalleryImageQuery rightJoinGallery($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Gallery relation
 * @method     ChildGalleryImageQuery innerJoinGallery($relationAlias = null) Adds a INNER JOIN clause to the query using the Gallery relation
 *
 * @method     ChildGalleryImageQuery leftJoinGalleryImageI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the GalleryImageI18n relation
 * @method     ChildGalleryImageQuery rightJoinGalleryImageI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GalleryImageI18n relation
 * @method     ChildGalleryImageQuery innerJoinGalleryImageI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the GalleryImageI18n relation
 *
 * @method     ChildGalleryImage findOne(ConnectionInterface $con = null) Return the first ChildGalleryImage matching the query
 * @method     ChildGalleryImage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildGalleryImage matching the query, or a new ChildGalleryImage object populated from the query conditions when no match is found
 *
 * @method     ChildGalleryImage findOneById(int $id) Return the first ChildGalleryImage filtered by the id column
 * @method     ChildGalleryImage findOneByGalleryId(int $gallery_id) Return the first ChildGalleryImage filtered by the gallery_id column
 * @method     ChildGalleryImage findOneByFile(string $file) Return the first ChildGalleryImage filtered by the file column
 * @method     ChildGalleryImage findOneByType(string $type) Return the first ChildGalleryImage filtered by the type column
 * @method     ChildGalleryImage findOneBySubtypeId(int $subtype_id) Return the first ChildGalleryImage filtered by the subtype_id column
 * @method     ChildGalleryImage findOneByTypeId(int $type_id) Return the first ChildGalleryImage filtered by the type_id column
 * @method     ChildGalleryImage findOneByUrl(string $url) Return the first ChildGalleryImage filtered by the url column
 * @method     ChildGalleryImage findOneByPosition(int $position) Return the first ChildGalleryImage filtered by the position column
 * @method     ChildGalleryImage findOneByVisible(int $visible) Return the first ChildGalleryImage filtered by the visible column
 * @method     ChildGalleryImage findOneByCreatedAt(string $created_at) Return the first ChildGalleryImage filtered by the created_at column
 * @method     ChildGalleryImage findOneByUpdatedAt(string $updated_at) Return the first ChildGalleryImage filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildGalleryImage objects filtered by the id column
 * @method     array findByGalleryId(int $gallery_id) Return ChildGalleryImage objects filtered by the gallery_id column
 * @method     array findByFile(string $file) Return ChildGalleryImage objects filtered by the file column
 * @method     array findByType(string $type) Return ChildGalleryImage objects filtered by the type column
 * @method     array findBySubtypeId(int $subtype_id) Return ChildGalleryImage objects filtered by the subtype_id column
 * @method     array findByTypeId(int $type_id) Return ChildGalleryImage objects filtered by the type_id column
 * @method     array findByUrl(string $url) Return ChildGalleryImage objects filtered by the url column
 * @method     array findByPosition(int $position) Return ChildGalleryImage objects filtered by the position column
 * @method     array findByVisible(int $visible) Return ChildGalleryImage objects filtered by the visible column
 * @method     array findByCreatedAt(string $created_at) Return ChildGalleryImage objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildGalleryImage objects filtered by the updated_at column
 *
 */
abstract class GalleryImageQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Gallery\Model\Base\GalleryImageQuery object.
     *
     * @param string $dbName     The database name
     * @param string $modelName  The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Gallery\\Model\\GalleryImage', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildGalleryImageQuery object.
     *
     * @param string   $modelAlias The alias of a model in the query
     * @param Criteria $criteria   Optional Criteria to build the query from
     *
     * @return ChildGalleryImageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gallery\Model\GalleryImageQuery) {
            return $criteria;
        }
        $query = new \Gallery\Model\GalleryImageQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
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
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildGalleryImage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GalleryImageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(GalleryImageTableMap::DATABASE_NAME);
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
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildGalleryImage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, GALLERY_ID, FILE, TYPE, SUBTYPE_ID, TYPE_ID, URL, POSITION, VISIBLE, CREATED_AT, UPDATED_AT FROM gallery_image WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildGalleryImage();
            $obj->hydrate($row);
            GalleryImageTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildGalleryImage|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param array               $keys Primary keys to use for the query
     * @param ConnectionInterface $con  an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        return $this->addUsingAlias(GalleryImageTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array $keys The list of primary key to use for the query
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        return $this->addUsingAlias(GalleryImageTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param mixed  $id         The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the gallery_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGalleryId(1234); // WHERE gallery_id = 1234
     * $query->filterByGalleryId(array(12, 34)); // WHERE gallery_id IN (12, 34)
     * $query->filterByGalleryId(array('min' => 12)); // WHERE gallery_id > 12
     * </code>
     *
     * @see       filterByGallery()
     *
     * @param mixed  $galleryId  The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByGalleryId($galleryId = null, $comparison = null)
    {
        if (is_array($galleryId)) {
            $useMinMax = false;
            if (isset($galleryId['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::GALLERY_ID, $galleryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($galleryId['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::GALLERY_ID, $galleryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::GALLERY_ID, $galleryId, $comparison);
    }

    /**
     * Filter the query on the file column
     *
     * Example usage:
     * <code>
     * $query->filterByFile('fooValue');   // WHERE file = 'fooValue'
     * $query->filterByFile('%fooValue%'); // WHERE file LIKE '%fooValue%'
     * </code>
     *
     * @param string $file       The value to use as filter.
     *                           Accepts wildcards (* and % trigger a LIKE)
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByFile($file = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($file)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $file)) {
                $file = str_replace('*', '%', $file);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::FILE, $file, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param string $type       The value to use as filter.
     *                           Accepts wildcards (* and % trigger a LIKE)
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the subtype_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySubtypeId(1234); // WHERE subtype_id = 1234
     * $query->filterBySubtypeId(array(12, 34)); // WHERE subtype_id IN (12, 34)
     * $query->filterBySubtypeId(array('min' => 12)); // WHERE subtype_id > 12
     * </code>
     *
     * @param mixed  $subtypeId  The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterBySubtypeId($subtypeId = null, $comparison = null)
    {
        if (is_array($subtypeId)) {
            $useMinMax = false;
            if (isset($subtypeId['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::SUBTYPE_ID, $subtypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($subtypeId['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::SUBTYPE_ID, $subtypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::SUBTYPE_ID, $subtypeId, $comparison);
    }

    /**
     * Filter the query on the type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTypeId(1234); // WHERE type_id = 1234
     * $query->filterByTypeId(array(12, 34)); // WHERE type_id IN (12, 34)
     * $query->filterByTypeId(array('min' => 12)); // WHERE type_id > 12
     * </code>
     *
     * @param mixed  $typeId     The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByTypeId($typeId = null, $comparison = null)
    {
        if (is_array($typeId)) {
            $useMinMax = false;
            if (isset($typeId['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::TYPE_ID, $typeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($typeId['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::TYPE_ID, $typeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::TYPE_ID, $typeId, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param string $url        The value to use as filter.
     *                           Accepts wildcards (* and % trigger a LIKE)
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $url)) {
                $url = str_replace('*', '%', $url);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::URL, $url, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByPosition(1234); // WHERE position = 1234
     * $query->filterByPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByPosition(array('min' => 12)); // WHERE position > 12
     * </code>
     *
     * @param mixed  $position   The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByVisible(1234); // WHERE visible = 1234
     * $query->filterByVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param mixed  $visible    The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::VISIBLE, $visible, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param mixed  $createdAt  The value to use as filter.
     *                           Values can be integers (unix timestamps), DateTime objects, or strings.
     *                           Empty strings are treated as NULL.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param mixed  $updatedAt  The value to use as filter.
     *                           Values can be integers (unix timestamps), DateTime objects, or strings.
     *                           Empty strings are treated as NULL.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(GalleryImageTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(GalleryImageTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GalleryImageTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gallery\Model\Gallery object
     *
     * @param \Gallery\Model\Gallery|ObjectCollection $gallery    The related object(s) to use as filter
     * @param string                                  $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByGallery($gallery, $comparison = null)
    {
        if ($gallery instanceof \Gallery\Model\Gallery) {
            return $this
                ->addUsingAlias(GalleryImageTableMap::GALLERY_ID, $gallery->getId(), $comparison);
        } elseif ($gallery instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GalleryImageTableMap::GALLERY_ID, $gallery->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByGallery() only accepts arguments of type \Gallery\Model\Gallery or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Gallery relation
     *
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function joinGallery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Gallery');

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
            $this->addJoinObject($join, 'Gallery');
        }

        return $this;
    }

    /**
     * Use the Gallery relation Gallery object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                              to be used as main alias in the secondary query
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Gallery\Model\GalleryQuery A secondary query class using the current class as primary query
     */
    public function useGalleryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGallery($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Gallery', '\Gallery\Model\GalleryQuery');
    }

    /**
     * Filter the query by a related \Gallery\Model\GalleryImageI18n object
     *
     * @param \Gallery\Model\GalleryImageI18n|ObjectCollection $galleryImageI18n the related object to use as filter
     * @param string                                           $comparison       Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function filterByGalleryImageI18n($galleryImageI18n, $comparison = null)
    {
        if ($galleryImageI18n instanceof \Gallery\Model\GalleryImageI18n) {
            return $this
                ->addUsingAlias(GalleryImageTableMap::ID, $galleryImageI18n->getId(), $comparison);
        } elseif ($galleryImageI18n instanceof ObjectCollection) {
            return $this
                ->useGalleryImageI18nQuery()
                ->filterByPrimaryKeys($galleryImageI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByGalleryImageI18n() only accepts arguments of type \Gallery\Model\GalleryImageI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GalleryImageI18n relation
     *
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function joinGalleryImageI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GalleryImageI18n');

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
            $this->addJoinObject($join, 'GalleryImageI18n');
        }

        return $this;
    }

    /**
     * Use the GalleryImageI18n relation GalleryImageI18n object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                              to be used as main alias in the secondary query
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Gallery\Model\GalleryImageI18nQuery A secondary query class using the current class as primary query
     */
    public function useGalleryImageI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinGalleryImageI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GalleryImageI18n', '\Gallery\Model\GalleryImageI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param ChildGalleryImage $galleryImage Object to remove from the list of results
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function prune($galleryImage = null)
    {
        if ($galleryImage) {
            $this->addUsingAlias(GalleryImageTableMap::ID, $galleryImage->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the gallery_image table.
     *
     * @param  ConnectionInterface $con the connection to use
     * @return int                 The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(GalleryImageTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            GalleryImageTableMap::clearInstancePool();
            GalleryImageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildGalleryImage or Criteria object OR a primary key value.
     *
     * @param  mixed               $values Criteria or ChildGalleryImage object or primary key or array of primary keys
     *                                     which is used to create the DELETE statement
     * @param  ConnectionInterface $con    the connection to use
     * @return int                 The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                                    if supported by native driver or if emulated using Propel.
     * @throws PropelException     Any exceptions caught during processing will be
     *                                    rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(GalleryImageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(GalleryImageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

        GalleryImageTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            GalleryImageTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param int $nbDays Maximum age of the latest update in days
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(GalleryImageTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param int $nbDays Maximum age of in days
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(GalleryImageTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(GalleryImageTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(GalleryImageTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(GalleryImageTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(GalleryImageTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param string $locale        Locale to use for the join condition, e.g. 'fr_FR'
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'GalleryImageI18n';

        return $this
            ->joinGalleryImageI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param string $locale   Locale to use for the join condition, e.g. 'fr_FR'
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return ChildGalleryImageQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('GalleryImageI18n');
        $this->with['GalleryImageI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param string $locale        Locale to use for the join condition, e.g. 'fr_FR'
     * @param string $relationAlias optional alias for the relation
     * @param string $joinType      Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return ChildGalleryImageI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GalleryImageI18n', '\Gallery\Model\GalleryImageI18nQuery');
    }

} // GalleryImageQuery

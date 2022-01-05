<?php

namespace App\Repository;

/**
 * Need variables on repository :
 * - private $idKey = 'id';
 */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

trait RepositoryAdditionalMethods {

    private string $tableNameCache;
    private Connection $dbConnection;

    /**
     * @return ArrayCollection Found entities, indexed.
     */
    public function getAll()
    {
        // init
        $res = new ArrayCollection();

        // get results
        $entities = parent::findAll();

        // indexing
        $res = $this->index($entities);

        // return
        unset($entities);
        return $res;
    }

    /**
     * get all ids of a table, indexed.
     *
     * @return	array	Id list.
     */
    public function getAllIds() {
        // verif
        if (empty($this->idKey)) {
            return [];
        }

        // request
        {
            $sql = 'SELECT ' . $this->idKey . ' FROM ' . $this->getTableName() . ' ORDER BY ' . $this->idKey . ' ASC';
            $data = $this->queryAll($sql);
            if (empty($data)) {
                unset($data, $sql);
                return [];
            }
        }

        // indexation
        {
            $res = array();
            foreach($data as $line) {
                $myId = trim($line[$this->idKey]);
                $res[$myId] = $myId;
            }
        }

        // retour
        unset($sql, $data, $myId);
        return ($res);
    }

    public function findBy(array $criterias, array $orderBy = null, $limit = null, $offset = null)
    {
        // get results
        $entities = parent::findBy($criterias, $orderBy, $limit, $offset);

        // indexing
        $res = $this->index($entities);

        // return
        unset($entities);
        return $res;
    }

    /**
     * @param array $criterias
     * @return array Found Ids.
     */
    public function findIdsBy(array $criterias)
    {
        // verif
        if (empty($this->idKey)) {
            return [];
        }

        // sql generation
        {
            // "where" management
            {
                $where = '';
                if (!empty($criterias) && is_array($criterias)) {
                    $whereTmp = array();
                    foreach ($criterias as $field => $criteria) {
                        if (!is_array($criteria))
                            $whereTmp[$field] = $field . ' = ' . $this->quote($criteria) . ' ';
                        else {
                            $tmp = array();
                            foreach ($criteria as $datum)
                                $tmp[] = $this->quote($datum);
                            $whereTmp[$field] = $field . " IN (" . implode(", ", $tmp) . ") ";
                            unset($tmp);
                        }
                    }
                    $where = ' WHERE ' . implode(' AND ', $whereTmp) . ' ';
                    unset($whereTmp, $criteria, $tmp, $datum);
                }
            }

            // request
            $sql = 'SELECT ' . $this->idKey . ' FROM ' . $this->getTableName() . $where . ' ORDER BY ' . $this->idKey . ' ASC';

            // retour
            unset($where);
        }

        // request execution
        $data = $this->queryAll($sql);

        // indexation
        {
            $res = array();
            if (!empty($data)) {
                foreach ($data as $line) {
                    $myId = $line[$this->idKey];
                    $res[$myId] = $myId;
                }
            }
            unset($line, $myId, $data);
        }

        // return
        unset($sql, $data);
        return ($res);
    }

    /**
     * @param array $criterias
     * @return int Found Ids.
     */
    public function findIdBy(array $criterias)
    {
        // verif
        if (empty($this->idKey)) {
            return [];
        }

        // sql generation
        {
            // "where" management
            {
                $where = '';
                if (!empty($criterias) && is_array($criterias)) {
                    $whereTmp = array();
                    foreach ($criterias as $field => $criteria) {
                        if (!is_array($criteria))
                            $whereTmp[$field] = $field . ' = ' . $this->quote($criteria) . ' ';
                        else {
                            $tmp = array();
                            foreach ($criteria as $datum)
                                $tmp[] = $this->quote($datum);
                            $whereTmp[$field] = $field . " IN (" . implode(", ", $tmp) . ") ";
                            unset($tmp);
                        }
                    }
                    $where = ' WHERE ' . implode(' AND ', $whereTmp) . ' ';
                    unset($whereTmp, $criteria, $tmp, $datum);
                }
            }

            // request
            $sql = 'SELECT ' . $this->idKey . ' FROM ' . $this->getTableName() . $where . ' ORDER BY ' . $this->idKey . ' ASC LIMIT 1';

            // retour
            unset($where);
        }

        // request execution
        $res = $this->queryOneField($sql);

        // return
        unset($sql);
        return ($res);
    }

    /**
     * remove db lines.
     * @param	int	$ids		Line identifer to remove.
     */
    public function removeFromIds($ids) {
        // verif
        if (empty($ids) || !is_array($ids) || empty($this->idKey)) {
            return;
        }

        // encoding
        $myIds = [];
        foreach ($ids as $myId) {
            $myIds[] = $this->quote($myId);
        }

        // request
        $sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE ' . $this->idKey . " IN (" . implode(', ', $myIds) . ')';

        // request execution
        $this->execute($sql);

        // return
        unset($sql, $myIds, $myId);
    }

    /**
     * @return int Max table Id.
     */
    public function getMaxId()
    {
        $sql = 'SELECT MAX(' . $this->idKey . ') AS RES FROM ' . $this->getTableName();
        $res = $this->queryOneField($sql);

        unset ($sql);
        return $res;
    }

    /**
     * @return int Min table Id.
     */
    public function getMinId()
    {
        $sql = 'SELECT MIN(' . $this->idKey . ') AS RES FROM ' . $this->getTableName();
        $res = $this->queryOneField($sql);

        unset ($sql);
        return $res;
    }

    /**
     * @return int Nb rows on table.
     */
    public function countRows() {
        $sql = 'SELECT COUNT(' . $this->idKey . ') AS RES FROM ' . $this->getTableName();
        $res = $this->queryOneField($sql);

        unset ($sql);
        return $res;
    }

    /**
     * @param ArrayCollection|array $entityList
     * @return ArrayCollection Indexed entity list
     *
     * It can get empty parameter.
     */
    public function index($entityList)
    {
        // verif
        if (empty($entityList) || empty($this->idKey)) {
            return ($entityList);
        }

        // process
        {
            $res = new ArrayCollection();
            $fct = 'get' . ucfirst($this->idKey); // getId
            foreach ($entityList as $entity) {
                $res->set($entity->$fct(), $entity);
            }
            unset($fct, $entity);
        }

        // return
        return ($res);
    }

    /**
     * Encrypting one element.
     * @param string|int	$elem	Element to encrypt.
     */
    public function quote($elem) {
        if ($elem === 0 || $elem === '0')
            return ("'0'");
        if (empty($elem))
            return ("''");
        if ($elem == 'NOW()')
            return ("NOW()");
        if ($elem == 'NULL')
            return ("NULL");
        if (is_int($elem) || ctype_digit($elem))
            return ("'$elem'");
        // encryption d'une string
        $elem = mysqli_real_escape_string($elem);
        return ("'$elem'");
    }

    /**
     * @return string
     */
    private function getTableName() {
        // get from cache
        if (!empty($this->tableNameCache))
        {
            return ($this->tableNameCache);
        }

        // process
        $this->tableNameCache = empty($this->baseName) ? $this->tableName : $this->baseName . '.' . $this->tableName;

        // return
        return ($this->tableNameCache);
    }

    /**
     * @param string $sql
     * @return int
     */
    private function queryOneField(string $sql)
    {
        if (empty($this->dbConnection))
        {
            $this->dbConnection = $this->getEntityManager()->getConnection();
        }

        $res = $this->dbConnection->fetchOne($sql);
        return ($res);
    }

    /**
     * @param string $sql   Query
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function queryOne(string $sql)
    {
        if (empty($this->dbConnection))
        {
            $this->dbConnection = $this->getEntityManager()->getConnection();
        }

        $res = $this->dbConnection->fetchAssociative($sql);
        return ($res);
    }

    /**
     * @param string $sql Query
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function queryAll(string $sql)
    {
        if (empty($this->dbConnection))
        {
            $this->dbConnection = $this->getEntityManager()->getConnection();
        }

        $res = $this->dbConnection->fetchAllAssociative($sql);
        return ($res);
    }

    /**
     * @param string $sql Query
     */
    private function execute(string $sql)
    {
        if (empty($this->dbConnection))
        {
            $this->dbConnection = $this->getEntityManager()->getConnection();
        }

        $this->dbConnection->exec($sql);
    }
}

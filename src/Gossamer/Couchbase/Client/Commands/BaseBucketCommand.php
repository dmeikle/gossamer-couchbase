<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/**
 * Created by PhpStorm.
 * User: user
 * Date: 6/9/2017
 * Time: 10:46 PM
 */

namespace Gossamer\Couchbase\Client\Commands;




use Gossamer\Core\CommandFactory\AbstractCommand;

abstract class BaseBucketCommand extends AbstractCommand
{

    protected function query($queryString, $connName = null) {
        $nodeConfig = $this->httpRequest->getNodeConfig();
        $conn = $this->getConnection($nodeConfig['entity_db'], $connName);

        if($conn instanceof \Gossamer\Pesedget\Extensions\Couchbase\Connections\CouchbaseDBConnection) {
            $query = \CouchbaseN1qlQuery::fromString($queryString);
            $result = $conn->getBucket()->query($query);

            return $this->resultsToArray($result);
        }

        return $conn->query($queryString);
    }

    protected function resultsToArray($results, $shiftArray = false)
    {
        if (!is_object($results)) {
            return array();
        }
        if ($shiftArray) {
            if (isset($results->rows)) {
                return current(json_decode(json_encode($results->rows), TRUE));
            }
            return current(json_decode(json_encode($results->values), TRUE));
        }
        if (isset($results->rows)) {
            return json_decode(json_encode($results->rows), TRUE);
        }
        return json_decode(json_encode($results->value), TRUE);
    }


    protected function getFilter(array $params)
    {
        $retval = '';
        foreach ($params as $key => $value) {
            if ($key == 'locale' || strpos($key, 'directive::') !== false) {
                continue;
            }
            if ($key == 'search') {
                return $this->getSearchFilter($value);
            } else {
                $retval .= " AND ($key = '$value')";
            }
        }

        return $retval;
    }

    protected function getSearchFilter($keyword)
    {
        $retval = '';

        foreach ($this->getSearchFields() as $field) {
            $retval .= " OR ($field LIKE '%$keyword%')";
        }
        if (strlen($retval) == 0) {
            return;
        }

        return 'AND (' . (substr($retval, 3)) . ')';
    }


    protected function removeRowHeadings(array $result)
    {
        return array_values($result);
    }

    protected function getSearchFields() {
        throw new \Exception('searchFields method must be overridden in calling class');
    }

    protected function getFields() {
        return '*';
    }

    protected function getMasterBucketName() {
        return $this->entityManager->getCredentials('master_bucket');
    }

    protected function getOrderBy(array &$params, $column = null)
    {
        $orderBy = (is_null($column) ? '': ' ORDER BY ' . $column . ' ASC');



        if (array_key_exists('directive::ORDER_BY', $params)) {
            $column = $params['directive::ORDER_BY'];

            $orderBy = ' ORDER BY ' . $column;
            unset($params['directive::ORDER_BY']);
            if (array_key_exists('directive::DIRECTION', $params)) {
                $orderBy .= ' ' . $params['directive::DIRECTION'];
                unset($params['directive::DIRECTION']);
            }
        }

        return $orderBy;
    }


    protected function getLimit(array &$params)
    {
        $limit = '';
        $offset = '';

        if (array_key_exists('directive::OFFSET', $params)) {
            $offset = ' OFFSET ' . intval($params['directive::OFFSET']);
            unset($params['directive::OFFSET']);
        }

        if (array_key_exists('directive::LIMIT', $params)) {
            $limit = ' LIMIT ' . intval($params['directive::LIMIT']);
            unset($params['directive::LIMIT']);
        }

        return $limit . $offset;
    }
}
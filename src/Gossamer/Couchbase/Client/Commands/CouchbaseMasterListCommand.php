<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/7/2016
 * Time: 11:51 AM
 */

namespace Gossamer\Couchbase\Client\Commands;


class CouchbaseMasterListCommand extends BaseBucketCommand
{

    /**
     * executes code specific to the child class
     *
     * @param array     URI params
     * @param array     POST params
     */
    public function execute($params = array(), $request = array()) {
        $queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getMasterBucketName() .
            "` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params) . $this->getOrderBy($params, 'id') .
            $this->getLimit($params);

        $rows = $this->query($queryString);

        $rowCount = $this->getTotalRowCount($params);

        return  array($this->entity->getIdentityField() => $rows, $this->entity->getIdentityField() . 'Count' => $rowCount);
    }

    public function getTotalRowCount($params = array(), $request = array()) {
        $queryString = "SELECT count('id') as rowCount FROM `" . $this->getMasterBucketName() .
            "` WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params);

        return $this->query($queryString);
    }
}
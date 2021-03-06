<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/7/2016
 * Time: 11:51 AM
 */

namespace Gossamer\Couchbase\Client\Commands;


class CouchbaseMasterGetCommand extends BaseBucketCommand
{
    public function execute($params = array(), $request = array())
    {
        $queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getMasterBucketName() .
            "` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params) . ' LIMIT 1';

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        $rows = $this->query($queryString);

        $this->httpRequest->setAttribute($this->entity->getClassName(),  $this->removeRowHeadings($this->resultsToArray($rows, true)));
    }
}
<?php
/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

/** *
 * Author: dave
 * Date: 10/4/2016
 * Time: 5:18 PM
 */

namespace Gossamer\Couchbase\Client\Commands;


class AbstractCouchbaseGetCommand extends AbstractCouchbaseCommand
{
    protected function getFields() {
        return '*';
    }

    public function execute($params = array(), $request = array())
    {
        $queryString = "SELECT " . $this->getFields() . " FROM `" . $this->getBucketName() .
            "` as " . $this->entity->getClassName() . " WHERE type ='" . $this->entity->getIdentityField() . "' AND isActive = '1' " .
            $this->getFilter($params) . ' LIMIT 1';

        $query = \CouchbaseN1qlQuery::fromString($queryString);

        $rows = $this->getBucket()->query($query);

        $this->httpRequest->setAttribute($this->entity->getClassName(),  $this->removeRowHeadings($this->resultsToArray($rows, true)));
    }
}
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
 * Time: 5:17 PM
 */

namespace Gossamer\Couchbase\Client\Commands;


use Gossamer\Couchbase\Documents\Document;

class CouchbaseMasterSaveCommand extends AbstractCouchbaseSaveCommand
{

    public function execute($params = array(), $requestParams = array())
    {

        $this->prepare($this->entity, $requestParams);
        $this->populateDocument($this->entity, $requestParams);

        $id = $requestParams['id'];
        $this->getBucket(true)->upsert($id, $this->entity->toArray());
        $result = $this->getBucket(true)->get($id);
        $object =  array();
        $object[] = json_decode(json_encode($result->value),true);

        $this->httpRequest->setAttribute($this->entity->getClassName(), $object);
    }



    protected function setDocumentId(Document $document, array &$params)
    {
        if (array_key_exists('id', $params) && strlen($params['id']) > 0) {

            return;
        }

        $counter = $this->getBucket(true)->counter($document->getDocumentKey(), 1, array('initial' => 100));
        $params['id'] = $document->getDocumentKey() . $counter->value;

    }




}
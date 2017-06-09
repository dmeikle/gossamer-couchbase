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
 * Date: 6/8/2017
 * Time: 8:56 PM
 */

namespace Gossamer\Couchbase\Utils;


interface YAMLInterface
{
    public function __construct(Logger $logger = null);

    public function findNodeByURI( $uri, $searchFor);

    public function loadConfig();

    public function setFilePath($ymlFilePath);
}
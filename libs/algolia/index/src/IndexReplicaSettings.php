<?php

/*
 * This file is part of AlgoliaIndex library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia\Index;

final class IndexReplicaSettings
{
    /**
     * @var string
     */
    private $indexName;

    /**
     * @var IndexSettings
     */
    private $indexSettings;

    /**
     * @param       $indexName
     * @param array $options
     */
    public function __construct($indexName, array $options = array())
    {
        $this->indexName = (string) $indexName;
        if (isset($options['replicas'])) {
            throw new \InvalidArgumentException('Replica settings can not have replica options.');
        }
        $this->indexSettings = new IndexSettings($options);
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->indexSettings->toArray();
    }
}

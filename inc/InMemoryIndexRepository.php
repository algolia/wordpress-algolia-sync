<?php

/*
 * This file is part of WpAlgolia library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@algolia.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia;

use WpAlgolia\Index\Index;
use WpAlgolia\Index\Repository;

class InMemoryIndexRepository implements Repository
{
    /**
     * @var Index[]
     */
    private $indices = array();

    /**
     * @param string $key
     *
     * @return Index
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \RuntimeException(sprintf('No index keyed "%s" is in the repository.', $key));
        }

        return $this->indices[$key];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->indices[$key]);
    }

    /**
     * @param       $key
     * @param Index $index
     */
    public function add($key, Index $index)
    {
        if ($this->has($key)) {
            throw new \LogicException(sprintf('An index keyed "%s" is already in the repository.', $key));
        }

        $this->indices[$key] = $index;
    }

    /**
     * @return Index[]
     */
    public function all()
    {
        return $this->indices;
    }
}

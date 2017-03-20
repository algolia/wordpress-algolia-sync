<?php

/*
 * This file is part of WpAlgolia library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@algolia.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia;

use WpAlgolia\Index\RecordsProvider;

abstract class WpQueryRecordsProvider implements RecordsProvider
{
    /**
     * @param int $perPage
     *
     * @return int
     */
    public function getTotalPagesCount($perPage)
    {
        $results = $this->newQuery(array('posts_per_page' => (int) $perPage));

        return (int) $results->max_num_pages;
    }

    /**
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    public function getRecords($page, $perPage)
    {
        $query = $this->newQuery(
            array(
                'posts_per_page' => $perPage,
                'paged'          => $page,
            )
        );

        return $this->getRecordsForQuery($query);
    }

    /**
     * @param mixed $id
     *
     * @return array
     */
    public function getRecordsForId($id)
    {
        $post = get_post($id);

        if (!$post instanceof \WP_Post) {
            return array();
        }

        return $this->getRecordsForPost($post);
    }

    /**
     * @param \WP_Post $post
     *
     * @return array
     */
    abstract public function getRecordsForPost(\WP_Post $post);

    /**
     * @return array
     */
    abstract protected function getDefaultQueryArgs();

    /**
     * @param array $args
     *
     * @return \WP_Query
     */
    private function newQuery(array $args = array())
    {
        $defaultArgs = $this->getDefaultQueryArgs();

        $args = array_merge($defaultArgs, $args);
        $query = new \WP_Query($args);

        return $query;
    }

    /**
     * @param \WP_Query $query
     *
     * @return array
     */
    private function getRecordsForQuery(\WP_Query $query)
    {
        $records = array();
        foreach ($query->posts as $post) {
            $post = get_post($post);
            if (!$post instanceof \WP_Post) {
                continue;
            }
            $records = array_merge($records, $this->getRecordsForPost($post));
        }

        return $records;
    }
}

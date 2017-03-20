<?php

/*
 * This file is part of WpAlgolia library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@algolia.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia\MyCompany;

use WpAlgolia\PostsIndex;

class PostChangeListener
{
    /**
     * @var PostsIndex
     */
    private $index;

    private $postType = 'post';

    /**
     * @param PostsIndex $index
     */
    public function __construct(PostsIndex $index)
    {
        $this->index = $index;
        add_action('save_post', array($this, 'pushRecords'), 10, 2);
        add_action('before_delete_post', array($this, 'deleteRecords'));
        add_action('wp_trash_post', array($this, 'deleteRecords'));
    }

    /**
     * @param int      $postId
     * @param \WP_Post $post
     */
    public function pushRecords($postId, $post)
    {
        if ($this->postType !== $post->post_type) {
            return;
        }

        if ($post->post_status !== 'publish' || !empty($post->post_password)) {
            return $this->deleteRecords($postId);
        }

        $this->index->pushRecordsForPost($post);
    }

    /**
     * @param int $postId
     */
    public function deleteRecords($postId)
    {
        $post = get_post($postId);
        if ($post instanceof \WP_Post && $post->post_type === $this->postType) {
            $this->index->deleteRecordsForPost($post);
        }
    }
}

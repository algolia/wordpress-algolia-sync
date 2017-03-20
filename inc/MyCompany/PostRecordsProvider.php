<?php

/*
 * This file is part of WpAlgolia library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@algolia.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia\MyCompany;

use WpAlgolia\WpQueryRecordsProvider;

class PostRecordsProvider extends WpQueryRecordsProvider
{
    /**
     * @param \WP_Post $post
     *
     * @return array
     */
    public function getRecordsForPost(\WP_Post $post)
    {
        $post = $post->filter('display');

        $record = array(
            'objectID'            => (string) $post->ID,
            'post_type'           => $post->post_type,
            'ID'                  => (int) $post->ID,
            'guid'                => $post->guid,
            'post_date'           => get_post_time('U', false, $post),
            'post_date_formatted' => get_the_date('', $post),
            'permalink'           => get_permalink($post),
            'post_title'          => $post->post_title,
            'content'             => mb_substr(strip_tags($post->post_content), 0, 600), // We only take the 600 first bytes of the content. If more is needed, content should be split across multiples records and the DISTINCT feature should be used.
        );

        // Retrieve featured image.
        $featuredImage = get_the_post_thumbnail_url($post, 'post-thumbnail');
        $record['featured_image'] = $featuredImage ? $featuredImage : '';

        // Retrieve tags.
        $tags = wp_get_post_tags($post->ID);
        $record['tags'] = wp_list_pluck($tags, 'name');

        // Retrieve author.
        $author = get_userdata($post->post_author);
        $record['post_author'] = $author ? $author->display_name : '';

        return array($record);
    }

    /**
     * @return array
     */
    protected function getDefaultQueryArgs()
    {
        return array(
            'post_type'   => 'post',
            'post_status' => 'publish',
        );
    }
}

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
        global $sitepress;

        $langInfo = wpml_get_language_information(null, $post->ID);

        $current_lang = $sitepress->get_current_language(); //save current language
        $sitepress->switch_lang($langInfo['language_code']);

        $user = get_userdata($post->post_author);
        if ($user instanceof \WP_User) {
            $user_data = array(
                'raw'          => $user->user_login,
                'login'        => $user->user_login,
                'display_name' => $user->display_name,
                'id'           => $user->ID,
            );
        } else {
            $user_data = array(
                'raw'          => '',
                'login'        => '',
                'display_name' => '',
                'id'           => '',
            );
        }
        $post_date = $post->post_date;
        $post_date_gmt = $post->post_date_gmt;
        $post_modified = $post->post_modified;
        $post_modified_gmt = $post->post_modified_gmt;
        $comment_count = absint($post->comment_count);
        $comment_status = absint($post->comment_status);
        $ping_status = absint($post->ping_status);
        $menu_order = absint($post->menu_order);

        $record = array(
            'objectID'            => (string) $post->ID,
            'post_id'             => $post->ID,
            'ID'                  => $post->ID,
            'post_author'         => $user_data,
            'post_date'           => $post_date,
            'post_date_gmt'       => $post_date_gmt,
            'post_title'          => $this->prepareTextContent(get_the_title($post->ID)),
            'post_excerpt'        => $this->prepareTextContent($post->post_excerpt),
            'post_content'        => mb_substr($this->prepareTextContent(apply_filters('the_content', $post->post_content)), 0, 600), // We only take the 600 first bytes of the content. If more is needed, content should be split across multiples records and the DISTINCT feature should be used.
            'post_status'         => $post->post_status,
            'post_name'           => $post->post_name,
            'post_modified'       => $post_modified,
            'post_modified_gmt'   => $post_modified_gmt,
            'post_parent'         => $post->post_parent,
            'post_type'           => $post->post_type,
            'post_mime_type'      => $post->post_mime_type,
            'permalink'           => get_permalink($post->ID),
            'comment_count'       => $comment_count,
            'comment_status'      => $comment_status,
            'ping_status'         => $ping_status,
            'menu_order'          => $menu_order,
            'guid'                => $post->guid,
            'wpml'                => $langInfo,
            //'site_id'         => get_current_blog_id(),
        );

        // Retrieve featured image.
        $featuredImage = get_the_post_thumbnail_url($post, 'post-thumbnail');
        $record['featured_image'] = $featuredImage ? $featuredImage : '';

        // Retrieve tags.
        $tags = wp_get_post_tags($post->ID);
        $record['tags'] = wp_list_pluck($tags, 'name');

        $sitepress->switch_lang($current_lang); //restore previous language

        return array($record);
    }

    /**
     * @return array
     */
    protected function getDefaultQueryArgs()
    {
        return array(
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'suppress_filters' => true
        );
    }

    private function prepareTextContent($content)
    {
        $content = strip_tags($content);
        $content = preg_replace('#[\n\r]+#s', ' ', $content);

        return $content;
    }
}

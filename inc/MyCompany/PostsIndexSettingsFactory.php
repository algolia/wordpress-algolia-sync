<?php

/*
 * This file is part of WpAlgolia library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@algolia.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia\MyCompany;

use WpAlgolia\Index\IndexSettings;

class PostsIndexSettingsFactory
{
    /**
     * @return IndexSettings
     */
    public function create()
    {
        return new IndexSettings(
            array(
                'searchableAttributes' => array(
                    'unordered(post_title)',
                    'unordered(content)',
                ),
                'customRanking' => array(
                    'desc(post_date)',
                ),
                'attributesForFaceting' => array(
                    'tags',
                    'post_author',
                    'wpml.language_code',
                ),
                'attributesToSnippet' => array(
                    'content:10',
                ),
                'snippetEllipsisText' => '…',
            )
        );
    }
}

<?php

/*
 * This file is part of WpAlgolia library.
 * (c) Raymond Rutjes for Algolia <raymond.rutjes@algolia.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace WpAlgolia;

use WP_CLI;
use WP_CLI_Command;

class Commands extends WP_CLI_Command
{
    /**
     * @var InMemoryIndexRepository
     */
    private $indexRepository;

    /**
     * @param InMemoryIndexRepository $indexRepository
     */
    public function __construct(InMemoryIndexRepository $indexRepository)
    {
        $this->indexRepository = $indexRepository;
    }

    /**
     * Push all records to Algolia for a given index.
     *
     * ## OPTIONS
     *
     * <indexName>
     * : The key of the index.
     *
     * [--clear]
     * : Clear all existing records prior to pushing the records.
     *
     * [--batch=<batch>]
     * : Number of items to push to Algolia at the same time.
     * ---
     * default: 1000
     * ---
     *
     *
     * ## EXAMPLES
     *
     *     wp algolia re-index articles
     *
     * @when before_wp_load
     *
     * @param mixed $args
     * @param mixed $assoc_args
     */
    public function reIndex($args, $assoc_args)
    {
        list($indexName) = $args;
        $perPage = (int) $assoc_args['batch'];
        if ($perPage <= 0) {
            throw new \InvalidArgumentException('The "--batch" option should be greater than 0.');
        }

        $clear = (bool) $assoc_args['clear'];

        $index = $this->indexRepository->get($indexName);

        if ($clear) {
            WP_CLI::line(sprintf(__('About to clear index %s...', 'algolia'), $index->getName()));
            $index->clear();
            WP_CLI::success(sprintf(__('Correctly cleared index "%s".', 'algolia'), $index->getName()));
        }

        WP_CLI::line(sprintf(__('About to push the settings for index %s...', 'algolia'), $index->getName()));
        $index->pushSettings();
        WP_CLI::success(sprintf(__('Correctly pushed settings to the index "%s".', 'algolia'), $index->getName()));

        WP_CLI::line(__('About to push all records to Algolia. Please be patient...', 'algolia'));

        $start = microtime(true);

        $totalPages = $index->getRecordsProvider()->getTotalPagesCount($perPage);
        $progress = WP_CLI\Utils\make_progress_bar(__('Pushing records to Algolia', 'algolia'), $totalPages);

        $totalRecordsCount = $index->reIndex(false, $perPage, function () use ($progress) {
            $progress->tick();
        });

        $progress->finish();

        $elapsed = microtime(true) - $start;

        WP_CLI::success(sprintf(__('%d records pushed to Algolia in %d seconds!', 'algolia'), $totalRecordsCount, $elapsed));
    }

    /**
     * Push the settings for an index to Algolia.
     *
     * ## OPTIONS
     *
     * <indexName>
     * : The key of the index.
     *
     *
     * ## EXAMPLES
     *
     *     wp algolia pushSettings articles
     *
     * @when before_wp_load
     *
     * @param mixed $args
     * @param mixed $assoc_args
     */
    public function pushSettings($args, $assoc_args)
    {
        list($indexName) = $args;

        $index = $this->indexRepository->get($indexName);

        WP_CLI::line(sprintf(__('About to push the settings for index %s...', 'algolia'), $index->getName()));
        $index->pushSettings();
        WP_CLI::success(sprintf(__('Correctly pushed settings to the index "%s".', 'algolia'), $index->getName()));
    }
}

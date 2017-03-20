<?php

/**
 * Plugin Name: Algolia Integration Boilerplate
 * Description: Gives you a clean way to get started with indexing content into Algolia.
 * Version: 0.1.0
 * Author Name: Raymond Rutjes
 */

add_action(
    'plugins_loaded',
    function () {

        if(!defined('ALGOLIA_APPLICATION_ID') || !defined('ALGOLIA_ADMIN_API_KEY')) {
            // Unless we have access to the Algolia credentials, stop here.
            return;
        }

        // Composer dependencies.
        require_once 'libs/autoload.php';

        // Local dependencies.
        require_once 'inc/InMemoryIndexRepository.php';
        require_once 'inc/PostsIndex.php';
        require_once 'inc/WpQueryRecordsProvider.php';

        // MyCompany dependencies.
        require_once 'inc/MyCompany/PostRecordsProvider.php';
        require_once 'inc/MyCompany/PostsIndexSettingsFactory.php';
        require_once 'inc/MyCompany/PostChangeListener.php';

        $indexRepository = new \WpAlgolia\InMemoryIndexRepository();
        $algoliaClient = new \WpAlgolia\Client(ALGOLIA_APPLICATION_ID, ALGOLIA_ADMIN_API_KEY);

        // Register article index.
        $settings = new \WpAlgolia\MyCompany\PostsIndexSettingsFactory();
        $recordsProvider = new \WpAlgolia\MyCompany\PostRecordsProvider();
        $index = new \WpAlgolia\PostsIndex('posts', $algoliaClient, $settings->create(), $recordsProvider);
        new \WpAlgolia\MyCompany\PostChangeListener($index);
        $indexRepository->add('posts', $index);



        // WP CLI commands.
        if (defined('WP_CLI') && WP_CLI) {
            require_once 'inc/Commands.php';
            $commands = new \WpAlgolia\Commands($indexRepository);
            WP_CLI::add_command('algolia', $commands);
        }

    }
);

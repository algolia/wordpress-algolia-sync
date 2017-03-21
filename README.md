WpAlgolia
-----------------------------

## Installation

1. Extract the archive in a folder named `algolia` in the `wp-content/plugins` directory of your WordPress instance
2. Enable the plugin from your admin Plugins page
3. Add 2 constants to your wp-config.php file: `ALGOLIA_APPLICATION_ID` & `ALGOLIA_ADMIN_API_KEY`
4. Add 1 constant to determine the environment prefix for your indices: `ALGOLIA_PREFIX`
 
## What is already provided

We provided you with some code for the `Posts` to get you started with building an indexing pipeline.

Please have a look inside the `inc/MyCompany` folder.

3 files there:

* **PostRecordsProvider**: Defines the way to fetch records from WordPress and how to transform it into Algolia records.
* **PostsIndexSettingsFactory**: Defines the Algolia settings for the index (will be pushed on every new full re-index).
* **PostChangeListener**: Watches for changes on the articles, and pushes the updated article to Algolia.


The last file you need to be aware of is:

**algolia.php**: It bootstraps the plugin and registers your indices.

## Index your data

The indexing needs to be done from the command line.

First make sure you have [WP-CLI](http://wp-cli.org/) installed.

Then you can run the re-index command we provide:

```bash
$ wp algolia reIndex articles
```

Where `articles` is the name of your index. When you'll have more indices, you can change that with another index name.

After the re-index process finished, you should have everything pushed to Algolia, and the settings should be set.
 
## Adding more indices

To be able to add your other content types, you need to inspire from what's existing:
- Copy paste the Post classes from `inc/MyCompany` and adjust them
- Register the new index in the `algolia.php` file

Once you have introduced new indices, you can re-index them by doing for example:

```bash
$ wp algolia reIndex my_custom_index_name
```

In case you altered just the settings an don't want to operate a full re-index, you can run the following command:

```bash
$ wp algolia pushSettings my_custom_index_name
```







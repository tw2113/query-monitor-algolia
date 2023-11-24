=== Query Monitor Algolia ===
Contributors: tw2113
Tags: query monitor, debugging, algolia, algolia search
Requires at least: 6.2.2
Tested up to: 6.4.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: MIT

WP Search with Algolia and Query Monitor

== Description ==

Adds support for [WP Search with Algolia](https://www.wordpress.org/plugins/wp-search-with-algolia/) information to the [Query Monitor](https://wordpress.org/plugins/query-monitor/) developer addon.

## Features

### Status panel

#### Current displayed item
* Content type listing
* If is searchable
* If is currently indexed.
* If is currently SEO indexable

#### Search status
* Searchable post index enabled
* List of indexable post types

#### Indexes
Table of index names, entries count, and last updated timestamp for current site.

#### WP Search with Algolia settings status
* API is reachable
* Autocomplete enabled
* Collected Autocomplete configurations list
* Search style (native/backend/instantsearch)
* "Powered by" enabled.

### Index Settings
* List of all current index configuration for each index associated with the current site.

### Constants panel

List of any defined WP Search with Algolia PHP constants and current defined value.

== Installation ==

= Minimum Requirements =

* WordPress 6.3 or greater
* PHP version 7.4 or greater
* WP Search with Algolia
* Query Monitor

== Changelog ==

= 1.1.0 - 2023-11-24 =
* Added: SEO noindex detection support for plugins currently supported with WP Search with Algolia Pro.

= 1.0.0 - 2023-09-21 =
* Initial release

== Upgrade Notice ==

= 1.1.0 - 2023-11-24 =
* Added: SEO noindex detection support for plugins currently supported with WP Search with Algolia Pro.

= 1.0.0 - 2023-09-21 =
* Initial release

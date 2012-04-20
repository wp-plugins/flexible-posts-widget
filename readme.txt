=== Flexible Posts Widget ===
Author: David Paul Ellenwood
Author URI: http://dpedesign.com
Plugin URI: http://wordpress.org/extend/plugins/flexible-posts-widget/
Tags: widget, widgets, posts, recent posts, thumbnails, custom post types, custom taxonomies
Contributors: David Paul Ellenwood
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 1.0.1

An advanced posts display widget with many options: post by taxonomy & term or post type, thumbnails, order & order by, customizable templates

== Description ==

The default WordPress Recent Posts widget is exceptionally basic.  Flexible Posts Widget extends this widget with many per-widget customizable options.

Flexible Posts Widget was born as I found myself always needing a simple way to display a selection of posts from any taxonomy or post type as a list of "Recent" or "Related" posts.

= Features & options =

* Customizable widget title
* Get posts by either a taxonomy & term *OR* by any available post type.
* Control the number of posts displayed.
* Option to display the post thumbnail (feature image).
* Selectable post thumbnail size from available image sizes.
* Selectable sort order by (Date, ID, Title, Menu Order, Random) and order (ASC, DESC).
* The widget's HTML output can be customized by user-defined templates located in the current theme folder.
* Currently, no added CSS or JavaScripts added to your site.  Style the widget how ever you'd like!


== Installation ==

1. Upload the 'fleible-posts-widget' folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to 'Appearance' > 'Widgets' and place the widget into a sidebar to begin configuring it.

= To use a customized HTML output template =

1. Create a folder called `flexible-posts-widget` in the root of your theme folder.
1. Copy `widget.php` from within this plugin's `views` folder into your theme's new `flexible-posts-widget` folder.
1. Optional: Rename your theme's `widget.php` template file to a more descriptive name of your choice.
1. Go to 'Appearance' > 'Widgets' page in WordPress and configure a new Flexible Posts Widget
1. Enter the name of the template file you added to your theme *without* the .php extension in the 'Template Filename' field.


== Other Notes ==

= Default vs. Custom Templates =

Flexible Posts Widget comes with a default template for the widget output. If you would like to alter the widget display code, create a new folder called `flexible-posts-widget` in your template directory and copy over the "views/widget.php" file.

Edit the new file in your theme to your desired HTML layout. Please do not edit the one in the plugin folder as that will cause conflicts when you update the plugin to the latest release.

= Wish List =

Plugin updates & future features list

* Fix debug notices for undefined indexes on widgets admin screen.
* Dynamically populate available terms based on a selected taxonomy.
* Make the "Get Posts By" section selectable and only show the chosen method: Taxonomy & Term or Post Type.
* Dynamically populate the "Template Filename" field based on the templates available.
* Add default styles for the widget display & an option to load or not load them (?)


== Frequently Asked Questions ==

= Questions, Support & Bug Reports =
To get answers to your questions, request help or submit a bug report, please visit the forum: http://wordpress.org/tags/flexible-posts-widget/


== Screenshots ==

1. Flexible Posts Widget admin screen.
1. Example Flexible Posts Widget showing the post thumbnail and title wrapped in a link to the post.



== Changelog ==

= 1.0 =
* First public release

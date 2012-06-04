=== Flexible Posts Widget ===
Contributors: dpe415
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DJKSKHJWYAWDU
Tags: widget, widgets, posts, recent posts, thumbnails, custom post types, custom taxonomies
Requires at least: 3.2
Tested up to: 3.3.2
Stable tag: 1.0.5
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An advanced posts display widget with many options. Display posts in your sidebars exactly the way you like!

== Description ==

The default WordPress Recent Posts widget is exceptionally basic. I found myself always in need of a way to display a selection of posts from any taxonomy or post type in a theme sidebar; hence, Flexible Posts Widget. Flexible Posts Widget extends the default widget with many per-instance options.

= Upgrading from v1 to v2 =
When upgrading from version 1.0.x to version 2, please remember to double-check the settings for any existing widgets.  Not all settings combinations will be saved after the upgrade.

= Features & options =

* Customizable widget title
* Get posts using either a selectable taxonomy & term *OR* by selecting any post type.
* Control the number of posts displayed.
* Option to display the post thumbnail (feature image).
* Select the post thumbnail size to display from available image sizes.
* Select the sort orderby: Date, ID, Title, Menu Order, Random and sort order: ASC or DESC.
* The widget's HTML output can be customized by user-defined templates added to the current theme folder.


== Installation ==

1. Upload the `flexible-posts-widget` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to 'Appearance' > 'Widgets' and place the widget into a sidebar to configure it.

= To use a customized HTML output template =

1. Create a folder called `flexible-posts-widget` in the root of your theme folder.
1. Copy `widget.php` from within this plugin's `views` folder into your theme's new `flexible-posts-widget` folder.
1. Optional: Rename your theme's `widget.php` template file to a name of your choice (to use different templates for each widget instance).
1. Go to 'Appearance' > 'Widgets' in WordPress to configure an instance of the widget.
1. In the 'Template Filename' field enter the name of the template file you added to your theme. Example: `my-themes-widget.php`


== Frequently Asked Questions ==

= Questions, Support & Bug Reports =
To get answers to your questions, request help or submit a bug report, please visit the forum: http://wordpress.org/tags/flexible-posts-widget/


== Screenshots ==

1. Flexible Posts Widget admin screen.
1. Example Flexible Posts Widget showing the post thumbnail and title wrapped in a link to the post.

== Upgrade Notice ==

= 2.0 =
When upgrading from version 1.0.x to version 2.x, please remember to double-check the settings for any existing widgets.  Not all settings combinations will be saved after the upgrade.


== Other Notes ==

= Upgrading from v1 to v2 =
When upgrading from version 1.0.x to version 2.x, please remember to double-check the settings for any existing widgets.  Not all settings combinations will be saved after the upgrade.

= Default vs. Custom Templates =

Flexible Posts Widget comes with a default template for the widget output. If you would like to alter the widget display code, create a new folder called `flexible-posts-widget` in your template directory and copy over the "views/widget.php" file.

Edit the new file in your theme to your desired HTML layout. Please do not edit the one in the plugin folder as that will cause conflicts when you update the plugin to the latest release.

= Future updates & features list =

* Create shortcode functionality
* Dynamically populate the "Template Filename" field based on the templates available.
* Adjust widget output template for Media-type posts.
* Add default styles for the widget display & an option to load or not load them (?)


== Changelog ==

= 2.0 =
* *Upgrade notice:* When upgrading from v1.x.x to v2.x, remember to double-check the settings for any existing widgets.
* Dynamically populate available terms based on selected taxonomy.
* Make the "Get Posts By" section selectable and only show the chosen method: Taxonomy & Term or Post Type.
* Miscellaneous admin improvements.

= 1.0.5 =
* Bug fix - Removed post_status 'private' from wp_queries. We don't want to show private posts in our loops.

= 1.0.4 =
* Fixed an issue where post thumbnails aren't displaying.

= 1.0.3 =
* Fixed PHP notices that showed in the admin when WP_DEBUG is enabled
* Added some stub code for future admin JavaScripts (not active yet).
* Readme.txt updates

= 1.0.2 =
* Readme.txt updates

= 1.0 =
* First public release

=== DeMomentSomTres Categories ===
Contributors: marcqueralt
Donate link: http://demomentsomtres.com
Tags: archives, category, all posts
Requires at least: 3.5.2
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2

Displays all categories and its descriptions on a list

== Description ==

Displays all categories and its descriptions on a list based on shortcode DeMomentSomTres-Categories.

= Features =
* Shortcode based.
* Order is set by slug.
* Categories can be excluded based on ID using 'exclude' optional parameter.
* Category description is shown.

= History & Raison d'être =

One of our customers wanted a page showing some of the categories in its website (but not all) as a table of contents containing the category name with a link and its description.

On a very first version of its website, this table of contents was build manually but, as the time went by, when they started to build many new categories, this page started to be unefective and hard to maintain.

The solution was to build this plugin to build the table of content on the fly.

== Installation ==

1. Use plugin usual installation process.

== Frequently Asked Questions ==
= How to use the shortcode =
The shortcode is called using this syntax [DeMomentSomTres-Categories exclude="id1,id2,id3"]

= Which categories are excluded in the shortcode? =

All categories that are in the exclude attribute or are set as excluded in settings will be excluded from the shortcode.

== Screenshots ==
TBD

== Changelog ==

= 2.2.1 =
* Documentation improve ;)
* Warning messages solved

= 2.2 =
* Admin categories field changed from text to textarea

= 2.1 =
* Filter the_category option and filter added

= 1.0 =
* First public version

== Upgrade Notice ==
TBD
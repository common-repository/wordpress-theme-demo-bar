=== Wordpress Theme Demo Bar ===
Contributors: Zen
Donate link: http://zenverse.net/support/
Tags: theme, preview, demo, designer, bar, showcase, extra css, theme preview, theme demo, theme showcase, theme switching, switcher, theme switcher, shortcode, template tag
Requires at least: 2.3
Tested up to: 3.3.1
Stable tag: 1.6.3

Wordpress Theme Demo Bar allows any wordpress theme to be previewed without activating it. A demo bar would be shown on top of page, allow users to preview another theme.

== Description ==

Wordpress Theme Demo Bar is a plugin for wordpress that allows any wordpress theme to be previewed without activating it. A demo bar would be shown on top of page, allow users to preview another theme. The demo bar is customisable at admin panel. More than 1 extra CSS files can be loaded too. 

**Whats New in Version 1.6.1**

*   Fixed error when use together with the new wordpress admin bar

**Whats New in Version 1.3 to 1.5**

*   Support child themes
*   With "Individual Theme Settings", you can now display a link to theme info page and download/buy page at demo bar. [(Live in Action)](http://zenverse.net/?themedemo=monoshade)
*   You can now display theme info, such as number of previews or preview URL using shortcode `[demobar]`
*   To make the process easier, you can use media button to add it (see screenshot : Media Button)
*   You can do that in template files too, using the newly added template tag function `wptdb_output()`
*   Now you can edit other theme's options (functions.php) without activating it
*   Widget to show "Select Theme" drop-down menu (for your visitor to preview non-private themes)

**Features**

*   Preview any wordpress theme without activating it.
*   A demo bar will be shown on top of the page, allow users to preview another theme.
*   The demo bar can be deactivated.
*   You can hide your themes that are private.
*   You can load extra CSS files (more than one)
*   Demo Bar can be always on top now
*   You can cusmotise the look & feel of Demo Bar using CSS
*   Auto collection of popularity count (number of previews) of each theme.
*   Persistent preview, all internal links will be auto edited to keep the ?themedemo= variable.
*   Persistent preview can be turned off by the visitors by closing the demo bar (all links will then be reverted back automatically)
*   *NEW* Support child themes
*   *NEW* With "Individual Theme Settings", you can now display a link to theme info page and download/buy page at demo bar
*   *NEW* You can now display theme info, such as number of previews or preview URL using shortcode `[demobar]`
*   *NEW* To make the process easier, you can use media button to add it (see screenshot : Media Button)
*   *NEW* You can do that in template files too, using the newly added template tag function `wptdb_output()`
*   *NEW* Now you can edit other theme's options (functions.php) without activating it
*   *NEW* Widget available to show "Select Theme" drop-down menu (for your visitor to preview non-private themes)
*   *NEW* New template tag function `wptdb_list_themes()` to display drop-down menu

**Usage**

*   To preview any theme, simply add a variable "themedemo" to your site URL. For example: `myblog.com/?themedemo=the-theme-folder-name` [(Live in Action)](http://zenverse.net/?themedemo=monoshade)
*   Alternatively, you can get the preview URL of all your themes at your Admin Panel > Settings > WP Theme Demo Bar
*   To hide the demo bar on individual theme, simply add a variable "hidebar" to your site URL. For example: `myblog.com/?themedemo=mytheme&hidebar=1` [(Live in Action)](http://zenverse.net/?themedemo=monoshade&hidebar=1)
*   To load extra css, simply add a variable "extracss" to your site URL and seperate using comma. For example: `myblog.com/?themedemo=mytheme&extracss=blue,twocolumn` this loads blue.css and twocolumn.css from the theme's directory

**Documentations**

*   [Understanding and Using Shortcode in Wordpress Theme Demo Bar Plugin](http://zenverse.net/using-shortcode-in-wordpress-theme-demo-bar-plugin/)
*   [Using Template Tag Functions in Wordpress Theme Demo Bar Plugin](http://zenverse.net/using-template-tag-function-in-wordpress-theme-demo-bar-plugin/)

**Info & Links**

[Plugin Info](http://zenverse.net/wordpress-theme-demo-bar-plugin/) | [Plugin Author](http://zenverse.net/)

== Installation ==

1. Download the plugin package
2. Extract and upload the "wordpress-theme-demo-bar" folder to your-wordpress-directory/wp-content/plugins/
3. Activate the plugin and its ready
4. Go to Admin Panel > Settings > WP Theme Demo Bar and customise it to suit your needs.

== Frequently Asked Questions ==

= I am getting permission/priviledge error when I edit other theme's option =
I don't have this problem, I guess most people do not have this problem too. So I can't fix this problem. Please send me your wordpress version, server configuration (apache or IIS) and other related information.

= The Demo Bar is not showing =
The theme you were previewing must be a complete theme, i.e. must have "wp_head();" and "wp_footer();" in header.php and footer.php respectively. If they were missing, you can add them yourself. Open header.php, add `<?php wp_head(); ?>` before &lt;/head>. Open footer.php, add `<?php wp_footer(); ?>` before &lt;/body>.

= How to preview a theme? =
Add a variable 'themedemo' to your url. For example: http://zenverse.net/?themedemo=monoshade

= How to load extra CSS files? =
Add a variable 'extracss' to your url. For example: http://myblog.com/?themedemo=my-theme-folder-name&extracss=blue,design2
The example above loads '.../wp-content/themes/my-theme-folder-name/blue.css' and '.../wp-content/themes/my-theme-folder-name/design2.css'
You need to include 'themedemo' in the URL to load extra CSS files and the CSS file must be at the same directory of style.css
Seperate the filename using comma.

= How to load extra CSS files located in inner folder? =
For example: http://myblog.com/?themedemo=my-theme-folder-name&extracss=css/blue
The example above loads '.../wp-content/themes/my-theme-folder-name/css/blue.css'

= How to hide the demo bar? =
Disable it at Plugin Options or add a variable 'hidebar' to your url. For example: http://zenverse.net/?themedemo=monoshade&hidebar=1

= What is Persistent preview? =
This feature allows visitor to view the demo of a theme in any page (single post, category, page, etc.) beside just blog index.
If activated, it automatically add variables 'themedemo' & 'exracss' to all internal URLs.

= Persistent preview is not working =
Most likely you have a variable 'hidebar' in the URL or you have disabled the demo bar. As long as the demo bar is hidden, persistent preview feature is off. We need to do that so that visitor can "escape" from the preview state if they want to (by closing the demo bar).

= Conflicts with other plugins =
Mostly it won't happen. If it does, first turn off javascript tooltip at plugin option page and see if it helps. We have 1 user reported a conflict when using another plugin along with the tooltip. You can also try turning off persistent preview.

== Screenshots ==
1. A live Demo Bar
2. Settings Page
3. Add output to post/page easily using media button

== Changelog ==
= 1.6.3 =
* Fixed a PHP Notice: Undefined index: themedemo in plugins\wordpress-theme-demo-bar\wp_theme_demo_bar.php on line 21

= 1.6.2 =
* Use dirname(__FILE__) in all include and require functions
* Cleaned up the codes in edit-theme-options (editing other's theme option page)

= 1.6.1 =
* Fixed error when use together with the new wordpress admin bar 

= 1.6 =
* Fixed the wrong screenshot image URL causes screenshot not being displayed under All Themes & Stats menu.

= 1.5.7 =
* Some part of the codes now uses "get_option('home')" to fix a problem for installations that the actual wordpress folder is in [another directory](http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory)

= 1.5.6 =
* Fixed IE conditional tags that failed in xHTML validation

= 1.5.5 =
* Fixed wrong number of previews for themes with invalid cookie characters when using shortcode

= 1.5.4 =
* Fixed wrong removal of padding-top or padding-bottom from body element when user closes the demo bar

= 1.5.3 =
* Now all scripts are wrapped using CDATA tags so that your pages can pass xHTML validation
* Padding-top or padding-bottom now given to html element instead of body element
 
= 1.5.2 =
* Quick fix - Fixed persistent preview feature in child theme

= 1.5.1 =
* In MSIE, the demo bar's width will be auto-resize to 100% width on window resize
* Fixed the "Demo Bar has fixed position" feature in MSIE
* If you have single quote(s) in your theme's name, it won't break the demo bar anymore
* Now you can make demo bar stay at bottom easily with no extra css
* `top:0px` removed from `#wpthemedemobar` in default.css
* Edited qtip.js to show tooltip above mouse if demo bar's position is at bottom
* Fixed some part that breaks when single or double quotes were used (eg: custom output format)

= 1.5 =
* Now you can edit other theme's options (functions.php) without activating it
* Better and nicer content under "All Themes & Stats" menu at plugin option page
* Thickbox support for theme preview at plugin option page
* Added 3 buttons under  "Plugin Support & Extra" menu for you to reset or delete options in bulk
* Template Tag functions now accept arguments in array form
* You can now sort the themes in the "Select Theme" drop-down menu
* You can now show parent-child relationship in the "Select Theme" drop-down menu
* Widget available to show "Select Theme" drop-down menu (for your visitor to preview non-private themes)
* New Template Tag function : `wptdb_list_themes` added
* Fixed a small javascript error since version 1.1.x
* Created folders 'css', 'js' and 'upgrades'
* Persistent preview now avoid replacing links start with anchor #

= 1.4 =
* Shortcode `[demobar]` added for you to output theme data (number of previews and more) in blog post / page
* Template tag function : `wptdb_output` added
* Added "height of demo bar" in "Look and Feel"
* Now we use tooltip to show descriptions at plugin option page

= 1.3.2 =
* Persistent preview now avoid replacing special links such as: `themedemo=` , `/wp-admin` , `wp-login.php` , etc.
* Replaces blank space with '+' in all preview URL under "Statistics and Links" menu

= 1.3.1 =
* Allows user to turn off javascript tooltip (if it conflicts with some other plugins)

= 1.3 =
* Support child themes
* Fixed a small javascript bug
* Cleaned up the plugin option page
* Added "Individual Theme Settings", you can now display a link to theme info page and download/buy page at demo bar
* Added tooltip script
* Added CSS and external js files for option page
* Created a folder 'images'

= 1.2.1 =
* Fixed a very small PHP error
 
= 1.2 =
* Demo bar can be hide using 'hidebar' variable in URL
* Extra CSS files can be loaded using 'extracss' variable in URL
* Persistent Preview can be disabled now
* Added feature : Always On Top
* Cleaned up the plugin option page
* You can now edit the look & feel of demo bar using CSS 

= 1.1.2 =
* Fixed a javascript error of version 1.1.1

= 1.1 =
* Added the missing javascript function that closes the demo bar
* Added "Persistent preview" feature – all internal links will be auto edited to keep the ?themedemo= variable.
* Persistent preview can be turned off by the visitors by closing the demo bar (all links will then be reverted back automatically)
  
= 1.0 =
First version of Wordpress Theme Demo Bar
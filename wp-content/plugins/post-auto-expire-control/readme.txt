=== Posts Auto Expire Control ===
Contributors: TC.K
Donate link: http://9-sec.com/donation/
Tags: post expiration, user role, post type, expiration control, auto expiration, multi-authors
Requires at least: 3.2
Tested up to: 3.6
Stable tag: 0.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Post Auto Expire Control is for admin of multi-authors site to control the posts expiration based on the user role and post type. 

== Description ==

The Post Auto Expire Control is a plugin for admin of multi-authors site/blog to control the posts expiration based on the user role and post type. 

**Plugin Features:**

* Define post expiration rules based on user roles and post type.
* Define action that will be taken when the post has expired.
* Sent notification to post's author before the post is about to expire. 
* Configure the subject and content of the notification email.
* Define when the notification mail should be sent.
* Admin able to change the expiry date for a single post.

**How to use:**
* For more info on how to use this plugin, you can goto [Here](http://9-sec.com/2012/10/post-auto-expire-control/).


If you have any problems with current plugin, please leave a
message on Forums Posts.


== Installation ==

1. Upload `posts-auto-expire-control` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Find Posts Auto Expire Control under Settings Menu

== Frequently Asked Questions ==

= How can I show the post expire date on the post? =

You can use the shorcode "[apext-expire]" to display the expire date.

= Does this plugin using cron job? =

The plugin use wp_schedule_event() to run the cron. It will be fired daily.




== Screenshots ==

1. Post expiration rules table
2. Auto post expiration email notification control panel

== Changelog ==


= 0.1 =
* First version.

= 0.1.1 =
* Fixed saving option redirect error. 

= 0.1.2 =
* Fixed email notification saving error.

= 0.1.3 =
* Add filter and action hook in post expire function.

=== Plugin Name ===
Contributors: seiyar81
Donate link: http://www.yriase.fr/
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Comment Auto Blacklist automatically adds the IP of spam comments to the comment blacklist settings field.

== Description ==

Comment Auto Blacklist automatically adds the IP of spam comments to the comment blacklist settings field.

This allows you to get a proper list of IP addresses, usable to reject connexions with iptables for instance.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `comment_auto_blacklist.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 0.1 =
* First version
* Simply adds the IP address from any comment tagged as 'spam' to the Comment blacklist settings field.

== Next features ==
* Admin panel
* iptables auto script generation